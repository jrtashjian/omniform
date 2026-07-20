<?php
/**
 * Tests the WebhookController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Webhook;

use Mockery;
use OmniForm\OAuth\TokenStorage;
use OmniForm\Tests\Unit\BaseTestCase;
use OmniForm\Webhook\WebhookController;
use WP_Error;
use WP_Mock;
use WP_REST_Request;

/**
 * Tests the WebhookController class.
 */
class WebhookControllerTest extends BaseTestCase {
	/**
	 * The TokenStorage mock.
	 *
	 * @var \Mockery\MockInterface|TokenStorage
	 */
	private $token_storage;

	/**
	 * The WebhookController instance.
	 *
	 * @var WebhookController
	 */
	private $controller;

	/**
	 * The signing secret used across tests.
	 *
	 * @var string
	 */
	private $secret = 'test_webhook_secret';

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction( '__' )->andReturnUsing(
			function ( $text ) {
				return $text;
			}
		);

		$this->token_storage = Mockery::mock( TokenStorage::class );
		$this->controller    = new WebhookController( $this->token_storage );
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Sign a raw body with the test secret.
	 *
	 * @param string $body The raw request body.
	 *
	 * @return string The hex HMAC-SHA256 signature.
	 */
	private function sign( $body ) {
		return hash_hmac( 'sha256', $body, $this->secret );
	}

	/**
	 * Build a mocked request with a body and signature header.
	 *
	 * @param string      $body      The raw request body.
	 * @param string|null $signature The signature header value, or null to omit.
	 *
	 * @return \Mockery\MockInterface|WP_REST_Request
	 */
	private function build_request( $body, $signature = null ) {
		$request = Mockery::mock( WP_REST_Request::class );
		$request->shouldReceive( 'get_body' )->andReturn( $body );

		$request->shouldReceive( 'get_header' )
			->with( 'x_omniform_signature' )
			->andReturn( $signature );

		return $request;
	}

	/**
	 * Test handle_webhook returns 403 when no webhook secret is configured.
	 */
	public function testHandleWebhookReturns403WhenNoSecret() {
		$this->token_storage->shouldReceive( 'get_webhook_secret' )->andReturn( null );

		$request = $this->build_request( '{}' );

		$result = $this->controller->handle_webhook( $request );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'omniform_webhook_not_configured', $result->get_error_code() );
		$this->assertSame( 403, $result->get_error_data()['status'] );
	}

	/**
	 * Test handle_webhook returns 401 when the signature header is missing.
	 */
	public function testHandleWebhookReturns401WhenSignatureMissing() {
		$this->token_storage->shouldReceive( 'get_webhook_secret' )->andReturn( $this->secret );

		$request = $this->build_request( '{"type":"test"}', null );

		$result = $this->controller->handle_webhook( $request );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'omniform_webhook_missing_signature', $result->get_error_code() );
		$this->assertSame( 401, $result->get_error_data()['status'] );
	}

	/**
	 * Test handle_webhook returns 401 when the signature does not match.
	 */
	public function testHandleWebhookReturns401WhenSignatureInvalid() {
		$this->token_storage->shouldReceive( 'get_webhook_secret' )->andReturn( $this->secret );

		$request = $this->build_request( '{"type":"test"}', 'deadbeef' );

		$result = $this->controller->handle_webhook( $request );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'omniform_webhook_invalid_signature', $result->get_error_code() );
		$this->assertSame( 401, $result->get_error_data()['status'] );
	}

	/**
	 * Test handle_webhook returns 400 when the payload is missing the type field.
	 */
	public function testHandleWebhookReturns400WhenTypeMissing() {
		$this->token_storage->shouldReceive( 'get_webhook_secret' )->andReturn( $this->secret );

		$body    = '{"data":"no type here"}';
		$request = $this->build_request( $body, $this->sign( $body ) );

		$result = $this->controller->handle_webhook( $request );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'omniform_webhook_invalid_payload', $result->get_error_code() );
		$this->assertSame( 400, $result->get_error_data()['status'] );
	}

	/**
	 * Test handle_webhook returns 400 when the body is not valid JSON.
	 */
	public function testHandleWebhookReturns400WhenBodyNotJson() {
		$this->token_storage->shouldReceive( 'get_webhook_secret' )->andReturn( $this->secret );

		$body    = 'not json at all';
		$request = $this->build_request( $body, $this->sign( $body ) );

		$result = $this->controller->handle_webhook( $request );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'omniform_webhook_invalid_payload', $result->get_error_code() );
		$this->assertSame( 400, $result->get_error_data()['status'] );
	}

	/**
	 * Test handle_webhook fires the action and returns received on a valid request.
	 */
	public function testHandleWebhookFiresActionOnValidRequest() {
		$this->token_storage->shouldReceive( 'get_webhook_secret' )->andReturn( $this->secret );

		$body    = '{"type":"form.submitted","data":{"id":1}}';
		$request = $this->build_request( $body, $this->sign( $body ) );

		WP_Mock::expectAction(
			'omniform_webhook_received',
			array(
				'type' => 'form.submitted',
				'data' => array( 'id' => 1 ),
			)
		);

		$result = $this->controller->handle_webhook( $request );

		$this->assertNotInstanceOf( WP_Error::class, $result );
		$this->assertSame( array( 'received' => true ), $result );
	}
}
