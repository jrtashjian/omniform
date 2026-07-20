<?php
/**
 * Tests the ApiClient class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\OAuth;

use OmniForm\OAuth\ApiClient;
use OmniForm\OAuth\TokenStorage;
use OmniForm\OAuth\OAuthManager;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;

/**
 * Tests the ApiClient class.
 */
class ApiClientTest extends BaseTestCase {
	/**
	 * The TokenStorage mock.
	 *
	 * @var \Mockery\MockInterface|TokenStorage
	 */
	private $token_storage;

	/**
	 * The OAuthManager mock.
	 *
	 * @var \Mockery\MockInterface|OAuthManager
	 */
	private $oauth_manager;

	/**
	 * The ApiClient instance.
	 *
	 * @var ApiClient
	 */
	private $api_client;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction( '__' )->andReturn( 'message' );

		$this->token_storage = Mockery::mock( TokenStorage::class );
		$this->oauth_manager = Mockery::mock( OAuthManager::class );
		$this->api_client    = new ApiClient( $this->token_storage, $this->oauth_manager, 'https://api.example.com' );
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Test constructor sets dependencies correctly.
	 */
	public function testConstructorSetsDependencies() {
		$this->assertInstanceOf( ApiClient::class, $this->api_client );
	}

	/**
	 * Test healthcheck makes unauthenticated request.
	 */
	public function testHealthcheckMakesUnauthenticatedRequest() {
		WP_Mock::userFunction( 'wp_remote_get' )
			->with( 'https://api.example.com/up' )
			->andReturn( array( 'response' => array( 'code' => 200 ) ) );

		$result = $this->api_client->healthcheck();

		$this->assertEquals( array( 'response' => array( 'code' => 200 ) ), $result );
	}

	/**
	 * Test get with valid token returns the decoded response body.
	 */
	public function testGetWithValidToken() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		$response = array( 'body' => '{"data": "test"}' );

		WP_Mock::userFunction( 'wp_remote_get' )
			->andReturn( $response );
		WP_Mock::userFunction( 'is_wp_error' )
			->with( $response )
			->once()
			->andReturn( false );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code' )
			->with( $response )
			->once()
			->andReturn( 200 );
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->with( $response )
			->once()
			->andReturn( '{"data": "test"}' );

		$result = $this->api_client->get( '/test' );

		$this->assertEquals( array( 'data' => 'test' ), $result );
	}

	/**
	 * Test post with valid token returns the decoded response body.
	 */
	public function testPostWithValidToken() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		$response = array( 'body' => '{"success": true}' );

		WP_Mock::userFunction( 'wp_remote_post' )
			->andReturn( $response );
		WP_Mock::userFunction( 'is_wp_error' )
			->with( $response )
			->once()
			->andReturn( false );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code' )
			->with( $response )
			->once()
			->andReturn( 200 );
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->with( $response )
			->once()
			->andReturn( '{"success": true}' );

		$result = $this->api_client->post( '/test' );

		$this->assertEquals( array( 'success' => true ), $result );
	}

	/**
	 * Test get with expired token and successful refresh.
	 */
	public function testGetWithExpiredTokenAndSuccessfulRefresh() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( true );
		$this->oauth_manager->shouldReceive( 'refresh_access_token' )->andReturn( true );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'new_token' );

		$response = array( 'body' => '{"data": "refreshed"}' );

		WP_Mock::userFunction( 'wp_remote_get' )
			->andReturn( $response );
		WP_Mock::userFunction( 'is_wp_error' )
			->with( $response )
			->once()
			->andReturn( false );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code' )
			->with( $response )
			->once()
			->andReturn( 200 );
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->with( $response )
			->once()
			->andReturn( '{"data": "refreshed"}' );

		$result = $this->api_client->get( '/test' );

		$this->assertEquals( array( 'data' => 'refreshed' ), $result );
	}

	/**
	 * Test post with expired token and failed refresh returns a WP_Error.
	 */
	public function testPostWithExpiredTokenAndFailedRefresh() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( true );
		$this->oauth_manager->shouldReceive( 'refresh_access_token' )->andReturn( false );
		$this->token_storage->shouldReceive( 'clear_tokens' )->once();

		$result = $this->api_client->post( '/test' );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertEquals( 'oauth_no_token', $result->get_error_code() );
	}

	/**
	 * Test get with additional args passes them through to the request.
	 */
	public function testGetWithAdditionalArgs() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		$response = array( 'body' => '{"data": "with_args"}' );

		WP_Mock::userFunction( 'wp_remote_get' )
			->with(
				'https://api.example.com/test',
				array(
					'headers' => array(
						'Authorization' => 'Bearer valid_token',
						'Content-Type'  => 'application/json',
					),
					'timeout' => 30,
				)
			)
			->andReturn( $response );
		WP_Mock::userFunction( 'is_wp_error' )
			->with( $response )
			->once()
			->andReturn( false );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code' )
			->with( $response )
			->once()
			->andReturn( 200 );
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->with( $response )
			->once()
			->andReturn( '{"data": "with_args"}' );

		$result = $this->api_client->get( '/test', array( 'timeout' => 30 ) );

		$this->assertEquals( array( 'data' => 'with_args' ), $result );
	}

	/**
	 * Test get returns the WP_Error untouched when the transport fails.
	 */
	public function testGetReturnsWpErrorOnTransportFailure() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		$error = new \WP_Error( 'http_failure', 'Request failed.' );

		WP_Mock::userFunction( 'wp_remote_get' )
			->andReturn( $error );
		WP_Mock::userFunction( 'is_wp_error' )
			->with( $error )
			->once()
			->andReturn( true );

		$result = $this->api_client->get( '/test' );

		$this->assertSame( $error, $result );
	}

	/**
	 * Test get returns a WP_Error with the status code for a non-2xx response.
	 */
	public function testGetWithNon2xxStatusCode() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		$response = array( 'response' => array( 'code' => 500 ) );

		WP_Mock::userFunction( 'wp_remote_get' )
			->andReturn( $response );
		WP_Mock::userFunction( 'is_wp_error' )
			->with( $response )
			->once()
			->andReturn( false );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code' )
			->with( $response )
			->once()
			->andReturn( 500 );

		$result = $this->api_client->get( '/test' );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertEquals( 'api_error', $result->get_error_code() );
		$this->assertEquals( array( 'status' => 500 ), $result->get_error_data() );
	}

	/**
	 * Test get returns a WP_Error when the response body is not valid JSON.
	 */
	public function testGetWithInvalidJson() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		$response = array( 'body' => 'not json' );

		WP_Mock::userFunction( 'wp_remote_get' )
			->andReturn( $response );
		WP_Mock::userFunction( 'is_wp_error' )
			->with( $response )
			->once()
			->andReturn( false );
		WP_Mock::userFunction( 'wp_remote_retrieve_response_code' )
			->with( $response )
			->once()
			->andReturn( 200 );
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->with( $response )
			->once()
			->andReturn( 'not json' );

		$result = $this->api_client->get( '/test' );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertEquals( 'invalid_json', $result->get_error_code() );
	}
}
