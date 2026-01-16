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
	 * Test get with valid token.
	 */
	public function testGetWithValidToken() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		WP_Mock::userFunction( 'wp_remote_get' )
			->andReturn( array( 'body' => '{"data": "test"}' ) );

		$result = $this->api_client->get( '/test' );

		$this->assertEquals( array( 'body' => '{"data": "test"}' ), $result );
	}

	/**
	 * Test post with valid token.
	 */
	public function testPostWithValidToken() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

		WP_Mock::userFunction( 'wp_remote_post' )
			->andReturn( array( 'body' => '{"success": true}' ) );

		$result = $this->api_client->post( '/test' );

		$this->assertEquals( array( 'body' => '{"success": true}' ), $result );
	}

	/**
	 * Test get with expired token and successful refresh.
	 */
	public function testGetWithExpiredTokenAndSuccessfulRefresh() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( true );
		$this->oauth_manager->shouldReceive( 'refresh_access_token' )->andReturn( true );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'new_token' );

		WP_Mock::userFunction( 'wp_remote_get' )
			->andReturn( array( 'body' => '{"data": "refreshed"}' ) );

		$result = $this->api_client->get( '/test' );

		$this->assertEquals( array( 'body' => '{"data": "refreshed"}' ), $result );
	}

	/**
	 * Test post with expired token and failed refresh.
	 */
	public function testPostWithExpiredTokenAndFailedRefresh() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( true );
		$this->oauth_manager->shouldReceive( 'refresh_access_token' )->andReturn( false );
		$this->token_storage->shouldReceive( 'clear_tokens' )->once();

		Mockery::mock( 'overload:WP_Error' )
			->shouldReceive( 'get_error_code' )
			->andReturn( 'oauth_no_token' );

		$result = $this->api_client->post( '/test' );

		$this->assertInstanceOf( 'WP_Error', $result );
		$this->assertEquals( 'oauth_no_token', $result->get_error_code() );
	}

	/**
	 * Test get with additional args.
	 */
	public function testGetWithAdditionalArgs() {
		$this->token_storage->shouldReceive( 'is_expired' )->andReturn( false );
		$this->token_storage->shouldReceive( 'get_access_token' )->andReturn( 'valid_token' );

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
			->andReturn( array( 'body' => '{"data": "with_args"}' ) );

		$result = $this->api_client->get( '/test', array( 'timeout' => 30 ) );

		$this->assertEquals( array( 'body' => '{"data": "with_args"}' ), $result );
	}
}
