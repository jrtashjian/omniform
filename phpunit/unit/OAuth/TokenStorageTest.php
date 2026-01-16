<?php
/**
 * Tests the TokenStorage class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\OAuth;

use OmniForm\OAuth\TokenStorage;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the TokenStorage class.
 */
class TokenStorageTest extends BaseTestCase {
	/**
	 * The TokenStorage instance.
	 *
	 * @var TokenStorage
	 */
	private $token_storage;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->token_storage = new TokenStorage();
	}

	/**
	 * Test get_access_token returns the stored access token.
	 */
	public function testGetAccessTokenReturnsStoredValue() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::ACCESS_TOKEN_KEY, null )
			->andReturn( 'test_access_token' );

		$this->assertEquals( 'test_access_token', $this->token_storage->get_access_token() );
	}

	/**
	 * Test get_access_token returns null when no token is stored.
	 */
	public function testGetAccessTokenReturnsNullWhenNotSet() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::ACCESS_TOKEN_KEY, null )
			->andReturn( null );

		$this->assertNull( $this->token_storage->get_access_token() );
	}

	/**
	 * Test set_access_token updates access token and expiration options.
	 */
	public function testSetAccessTokenUpdatesOptions() {
		WP_Mock::userFunction( 'update_option' )
			->with( TokenStorage::ACCESS_TOKEN_KEY, 'new_token' )
			->once();
		WP_Mock::userFunction( 'update_option' )
			->with( TokenStorage::TOKEN_EXPIRES_KEY, \WP_Mock\Functions::type( 'int' ) )
			->once();

		$this->token_storage->set_access_token( 'new_token', 3600 );

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test get_refresh_token returns the stored refresh token.
	 */
	public function testGetRefreshTokenReturnsStoredValue() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::REFRESH_TOKEN_KEY, null )
			->andReturn( 'test_refresh_token' );

		$this->assertEquals( 'test_refresh_token', $this->token_storage->get_refresh_token() );
	}

	/**
	 * Test get_refresh_token returns null when no token is stored.
	 */
	public function testGetRefreshTokenReturnsNullWhenNotSet() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::REFRESH_TOKEN_KEY, null )
			->andReturn( null );

		$this->assertNull( $this->token_storage->get_refresh_token() );
	}

	/**
	 * Test set_refresh_token updates refresh token option.
	 */
	public function testSetRefreshTokenUpdatesOption() {
		WP_Mock::userFunction( 'update_option' )
			->with( TokenStorage::REFRESH_TOKEN_KEY, 'new_refresh_token' )
			->once();

		$this->token_storage->set_refresh_token( 'new_refresh_token' );

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test is_expired returns true when token is expired.
	 */
	public function testIsExpiredReturnsTrueWhenExpired() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::TOKEN_EXPIRES_KEY )
			->andReturn( time() - 100 );

		$this->assertTrue( $this->token_storage->is_expired() );
	}

	/**
	 * Test is_expired returns true when no expiration is set.
	 */
	public function testIsExpiredReturnsTrueWhenNoExpiration() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::TOKEN_EXPIRES_KEY )
			->andReturn( false );

		$this->assertTrue( $this->token_storage->is_expired() );
	}

	/**
	 * Test is_expired returns false when token is not expired.
	 */
	public function testIsExpiredReturnsFalseWhenNotExpired() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::TOKEN_EXPIRES_KEY )
			->andReturn( time() + 100 );

		$this->assertFalse( $this->token_storage->is_expired() );
	}

	/**
	 * Test get_client_id returns the stored client ID.
	 */
	public function testGetClientIdReturnsStoredValue() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::CLIENT_ID_KEY, null )
			->andReturn( 'test_client_id' );

		$this->assertEquals( 'test_client_id', $this->token_storage->get_client_id() );
	}

	/**
	 * Test get_client_id returns null when no client ID is stored.
	 */
	public function testGetClientIdReturnsNullWhenNotSet() {
		WP_Mock::userFunction( 'get_option' )
			->with( TokenStorage::CLIENT_ID_KEY, null )
			->andReturn( null );

		$this->assertNull( $this->token_storage->get_client_id() );
	}

	/**
	 * Test set_client_id updates client ID option.
	 */
	public function testSetClientIdUpdatesOption() {
		WP_Mock::userFunction( 'update_option' )
			->with( TokenStorage::CLIENT_ID_KEY, 'new_client_id' )
			->once();

		$this->token_storage->set_client_id( 'new_client_id' );

		$this->expectNotToPerformAssertions();
	}

	/**
	 * Test clear_tokens deletes all token-related options.
	 */
	public function testClearTokensDeletesAllOptions() {
		WP_Mock::userFunction( 'delete_option' )
			->with( TokenStorage::ACCESS_TOKEN_KEY )
			->once();
		WP_Mock::userFunction( 'delete_option' )
			->with( TokenStorage::REFRESH_TOKEN_KEY )
			->once();
		WP_Mock::userFunction( 'delete_option' )
			->with( TokenStorage::TOKEN_EXPIRES_KEY )
			->once();
		WP_Mock::userFunction( 'delete_option' )
			->with( TokenStorage::CLIENT_ID_KEY )
			->once();

		$this->token_storage->clear_tokens();

		$this->expectNotToPerformAssertions();
	}
}
