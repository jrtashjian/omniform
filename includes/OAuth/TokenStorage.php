<?php
/**
 * The TokenStorage class.
 *
 * @package OmniForm
 */

namespace OmniForm\OAuth;

/**
 * The TokenStorage class.
 */
class TokenStorage {
	/**
	 * Option key for access token.
	 */
	const ACCESS_TOKEN_KEY = 'omniform_access_token';

	/**
	 * Option key for refresh token.
	 */
	const REFRESH_TOKEN_KEY = 'omniform_refresh_token';

	/**
	 * Option key for token expiration.
	 */
	const TOKEN_EXPIRES_KEY = 'omniform_token_expires';

	/**
	 * Option key for client ID.
	 */
	const CLIENT_ID_KEY = 'omniform_client_id';

	/**
	 * Get the access token.
	 *
	 * @return string|null
	 */
	public function get_access_token(): ?string {
		return get_option( self::ACCESS_TOKEN_KEY, null );
	}

	/**
	 * Set the access token.
	 *
	 * @param string $token The access token.
	 * @param int    $expires_in Seconds until expiration.
	 *
	 * @return void
	 */
	public function set_access_token( string $token, int $expires_in ): void {
		update_option( self::ACCESS_TOKEN_KEY, $token );
		update_option( self::TOKEN_EXPIRES_KEY, time() + $expires_in );
	}

	/**
	 * Get the refresh token.
	 *
	 * @return string|null
	 */
	public function get_refresh_token(): ?string {
		return get_option( self::REFRESH_TOKEN_KEY, null );
	}

	/**
	 * Set the refresh token.
	 *
	 * @param string $token The refresh token.
	 *
	 * @return void
	 */
	public function set_refresh_token( string $token ): void {
		update_option( self::REFRESH_TOKEN_KEY, $token );
	}

	/**
	 * Check if the access token is expired.
	 *
	 * @return bool
	 */
	public function is_expired(): bool {
		$expires = get_option( self::TOKEN_EXPIRES_KEY );
		return ! $expires || time() > $expires;
	}

	/**
	 * Get the client ID.
	 *
	 * @return string|null
	 */
	public function get_client_id(): ?string {
		return get_option( self::CLIENT_ID_KEY, null );
	}

	/**
	 * Set the client ID.
	 *
	 * @param string $client_id The client ID.
	 *
	 * @return void
	 */
	public function set_client_id( string $client_id ): void {
		update_option( self::CLIENT_ID_KEY, $client_id );
	}

	/**
	 * Clear all tokens and client ID.
	 *
	 * @return void
	 */
	public function clear_tokens(): void {
		delete_option( self::ACCESS_TOKEN_KEY );
		delete_option( self::REFRESH_TOKEN_KEY );
		delete_option( self::TOKEN_EXPIRES_KEY );
		delete_option( self::CLIENT_ID_KEY );
	}
}
