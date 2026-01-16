<?php
/**
 * Tests the OAuthManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\OAuth;

use OmniForm\OAuth\OAuthManager;
use OmniForm\OAuth\TokenStorage;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;

/**
 * Tests the OAuthManager class.
 */
class OAuthManagerTest extends BaseTestCase {
	/**
	 * The TokenStorage mock.
	 *
	 * @var \Mockery\MockInterface|TokenStorage
	 */
	private $token_storage;

	/**
	 * The OAuthManager instance.
	 *
	 * @var OAuthManager
	 */
	private $oauth_manager;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
			define( 'MINUTE_IN_SECONDS', 60 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		}

		$this->token_storage = Mockery::mock( TokenStorage::class );
		$this->oauth_manager = new OAuthManager( $this->token_storage, 'https://api.example.com', 'https://account.example.com' );
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
		$this->assertInstanceOf( OAuthManager::class, $this->oauth_manager );
	}

	/**
	 * Test get_registration_url returns correct URL.
	 */
	public function testGetRegistrationUrlReturnsCorrectUrl() {
		WP_Mock::userFunction( 'admin_url' )
			->with( 'admin.php?page=omniform' )
			->andReturn( 'https://example.com/wp-admin/admin.php?page=omniform' );

		$result = $this->oauth_manager->get_registration_url();

		$this->assertEquals( 'https://account.example.com/register-client?redirect_uri=https%3A%2F%2Fexample.com%2Fwp-admin%2Fadmin.php%3Fpage%3Domniform', $result );
	}

	/**
	 * Test get_authorization_url redirects to registration when no client_id.
	 */
	public function testGetAuthorizationUrlRedirectsToRegistrationWhenNoClientId() {
		$this->token_storage->shouldReceive( 'get_client_id' )->andReturn( null );

		WP_Mock::userFunction( 'admin_url' )
			->with( 'admin.php?page=omniform' )
			->andReturn( 'https://example.com/wp-admin/admin.php?page=omniform' );

		$result = $this->oauth_manager->get_authorization_url();

		$this->assertEquals( 'https://account.example.com/register-client?redirect_uri=https%3A%2F%2Fexample.com%2Fwp-admin%2Fadmin.php%3Fpage%3Domniform', $result );
	}

	/**
	 * Test get_authorization_url returns correct URL when client_id exists.
	 */
	public function testGetAuthorizationUrlReturnsCorrectUrlWithClientId() {
		$this->token_storage->shouldReceive( 'get_client_id' )->andReturn( 'test_client_id' );

		WP_Mock::userFunction( 'set_transient' )
			->with( 'omniform_oauth_code_verifier', \Mockery::type( 'string' ), 600 )
			->once();
		WP_Mock::userFunction( 'admin_url' )
			->with( 'admin.php?page=omniform' )
			->andReturn( 'https://example.com/wp-admin/admin.php?page=omniform' );

		$result = $this->oauth_manager->get_authorization_url();

		$this->assertStringStartsWith( 'https://account.example.com/oauth/authorize?', $result );
		$this->assertTrue( strpos( $result, 'client_id=test_client_id' ) !== false );
		$this->assertTrue( strpos( $result, 'redirect_uri=' ) !== false );
		$this->assertTrue( strpos( $result, 'response_type=code' ) !== false );
		$this->assertTrue( strpos( $result, 'code_challenge=' ) !== false );
		$this->assertTrue( strpos( $result, 'code_challenge_method=S256' ) !== false );
	}

	/**
	 * Test handle_callback processes valid code.
	 */
	public function testHandleCallbackProcessesValidCode() {

		$_GET['code'] = 'test_code';

		WP_Mock::userFunction( 'wp_unslash' )
			->with( 'test_code' )
			->andReturn( 'test_code' );
		WP_Mock::userFunction( 'sanitize_text_field' )
			->with( 'test_code' )
			->andReturn( 'test_code' );
		WP_Mock::userFunction( 'get_transient' )
			->with( 'omniform_oauth_code_verifier' )
			->andReturn( 'test_verifier' );
		WP_Mock::userFunction( 'delete_transient' )
			->with( 'omniform_oauth_code_verifier' )
			->once();

		$this->token_storage->shouldReceive( 'get_client_id' )->andReturn( 'test_client_id' );

		WP_Mock::userFunction( 'wp_remote_post' )
			->andReturn(
				array(
					'body' => json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
						array(
							'access_token'  => 'new_access_token',
							'refresh_token' => 'new_refresh_token',
							'expires_in'    => 3600,
						)
					),
				)
			);
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->andReturn(
				json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
					array(
						'access_token'  => 'new_access_token',
						'refresh_token' => 'new_refresh_token',
						'expires_in'    => 3600,
					)
				)
			);
		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( false );

		$this->token_storage->shouldReceive( 'set_access_token' )
			->with( 'new_access_token', 3600 )
			->once();
		$this->token_storage->shouldReceive( 'set_refresh_token' )
			->with( 'new_refresh_token' )
			->once();

		WP_Mock::userFunction( 'admin_url' )
			->with( 'admin.php?page=omniform' )
			->andReturn( 'https://example.com/wp-admin/admin.php?page=omniform' );
		WP_Mock::userFunction( 'wp_safe_redirect' )
			->with( 'https://example.com/wp-admin/admin.php?page=omniform' )
			->once();

		$this->oauth_manager->handle_callback();

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test handle_callback dies when no code verifier.
	 */
	public function testHandleCallbackDiesWhenNoCodeVerifier() {
		$_GET['code'] = 'test_code';

		WP_Mock::userFunction( 'wp_unslash' )
			->with( 'test_code' )
			->andReturn( 'test_code' );
		WP_Mock::userFunction( 'sanitize_text_field' )
			->with( 'test_code' )
			->andReturn( 'test_code' );
		WP_Mock::userFunction( 'get_transient' )
			->with( 'omniform_oauth_code_verifier' )
			->andReturn( false );

		WP_Mock::userFunction( 'wp_die' )
			->with( 'OAuth code verifier not found. Please try again.' )
			->once();

		$this->oauth_manager->handle_callback();

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test handle_callback dies when token exchange fails.
	 */
	public function testHandleCallbackDiesWhenTokenExchangeFails() {
		$_GET['code'] = 'test_code';

		WP_Mock::userFunction( 'wp_unslash' )
			->with( 'test_code' )
			->andReturn( 'test_code' );
		WP_Mock::userFunction( 'sanitize_text_field' )
			->with( 'test_code' )
			->andReturn( 'test_code' );
		WP_Mock::userFunction( 'get_transient' )
			->with( 'omniform_oauth_code_verifier' )
			->andReturn( 'test_verifier' );
		WP_Mock::userFunction( 'delete_transient' )
			->with( 'omniform_oauth_code_verifier' )
			->once();

		$this->token_storage->shouldReceive( 'get_client_id' )->andReturn( 'test_client_id' );

		WP_Mock::userFunction( 'wp_remote_post' )
			->andReturn(
				array(
					'body' => json_encode( array() ), // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
				)
			);
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->andReturn( json_encode( array() ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( false );

		WP_Mock::userFunction( 'admin_url' )
			->with( 'admin.php?page=omniform' )
			->andReturn( 'https://example.com/wp-admin/admin.php?page=omniform' );

		WP_Mock::userFunction( 'wp_die' )
			->with( 'Failed to exchange code for tokens.' )
			->once();

		$this->oauth_manager->handle_callback();

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test refresh_access_token succeeds.
	 */
	public function testRefreshAccessTokenSucceeds() {
		$this->token_storage->shouldReceive( 'get_refresh_token' )->andReturn( 'refresh_token' );
		$this->token_storage->shouldReceive( 'get_client_id' )->andReturn( 'client_id' );

		WP_Mock::userFunction( 'wp_remote_post' )
			->andReturn(
				array(
					'body' => json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
						array(
							'access_token'  => 'new_access_token',
							'expires_in'    => 3600,
							'refresh_token' => 'new_refresh_token',
						)
					),
				)
			);
		WP_Mock::userFunction( 'wp_remote_retrieve_body' )
			->andReturn(
				json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
					array(
						'access_token'  => 'new_access_token',
						'expires_in'    => 3600,
						'refresh_token' => 'new_refresh_token',
					)
				)
			);
		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( false );

		$this->token_storage->shouldReceive( 'set_access_token' )
			->with( 'new_access_token', 3600 )
			->once();
		$this->token_storage->shouldReceive( 'set_refresh_token' )
			->with( 'new_refresh_token' )
			->once();

		$result = $this->oauth_manager->refresh_access_token();

		$this->assertTrue( $result );
	}

	/**
	 * Test refresh_access_token fails when no refresh token.
	 */
	public function testRefreshAccessTokenFailsWhenNoRefreshToken() {
		$this->token_storage->shouldReceive( 'get_refresh_token' )->andReturn( null );
		$this->token_storage->shouldReceive( 'get_client_id' )->andReturn( 'client_id' );

		$result = $this->oauth_manager->refresh_access_token();

		$this->assertFalse( $result );
	}

	/**
	 * Test refresh_access_token fails on API error.
	 */
	public function testRefreshAccessTokenFailsOnApiError() {
		$this->token_storage->shouldReceive( 'get_refresh_token' )->andReturn( 'refresh_token' );
		$this->token_storage->shouldReceive( 'get_client_id' )->andReturn( 'client_id' );

		WP_Mock::userFunction( 'wp_remote_post' )
			->andReturn( 'error' );
		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( true );

		$result = $this->oauth_manager->refresh_access_token();

		$this->assertFalse( $result );
	}
}
