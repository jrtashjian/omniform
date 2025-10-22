<?php
/**
 * The OAuthManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\OAuth;

/**
 * The OAuthManager class.
 */
class OAuthManager {
	/**
	 * Token storage.
	 *
	 * @var TokenStorage
	 */
	private TokenStorage $token_storage;

	/**
	 * API base URL.
	 *
	 * @var string
	 */
	private string $api_base_url;

	/**
	 * Account base URL.
	 *
	 * @var string
	 */
	private string $account_base_url;

	/**
	 * Constructor.
	 *
	 * @param TokenStorage $token_storage   Token storage.
	 * @param string       $api_base_url    API base URL.
	 * @param string       $account_base_url Account base URL.
	 */
	public function __construct( TokenStorage $token_storage, string $api_base_url, string $account_base_url ) {
		$this->token_storage    = $token_storage;
		$this->api_base_url     = $api_base_url;
		$this->account_base_url = $account_base_url;
	}

	/**
	 * Get the client registration URL.
	 *
	 * @return string
	 */
	public function get_registration_url(): string {
		$params = array(
			'redirect_uri' => $this->get_redirect_uri(),
			'client_name'  => 'OmniForm WordPress Plugin',
		);

		return $this->account_base_url . '/register-client?' . http_build_query( $params );
	}

	/**
	 * Get the authorization URL.
	 *
	 * @return string
	 */
	public function get_authorization_url(): string {
		$client_id = $this->token_storage->get_client_id();

		if ( ! $client_id ) {
			// If no client ID, redirect to registration first.
			return $this->get_registration_url();
		}

		$code_verifier  = $this->generate_code_verifier();
		$code_challenge = $this->generate_code_challenge( $code_verifier );

		// Store code verifier in transient for later use.
		set_transient( 'omniform_oauth_code_verifier', $code_verifier, 10 * MINUTE_IN_SECONDS );

		$params = array(
			'client_id'             => $client_id,
			'redirect_uri'          => $this->get_redirect_uri(),
			'response_type'         => 'code',
			'scope'                 => '',
			'code_challenge'        => $code_challenge,
			'code_challenge_method' => 'S256',
		);

		return $this->account_base_url . '/oauth/authorize?' . http_build_query( $params );
	}

	/**
	 * Handle the OAuth callback.
	 *
	 * @return void
	 */
	public function handle_callback(): void {
		if ( ! isset( $_GET['code'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$code          = sanitize_text_field( wp_unslash( $_GET['code'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$code_verifier = get_transient( 'omniform_oauth_code_verifier' );

		if ( ! $code_verifier ) {
			wp_die( esc_html__( 'OAuth code verifier not found. Please try again.', 'omniform' ) );
		}

		delete_transient( 'omniform_oauth_code_verifier' );

		$tokens = $this->exchange_code_for_tokens( $code, $code_verifier );

		if ( $tokens ) {
			$this->token_storage->set_access_token( $tokens['access_token'], $tokens['expires_in'] );
			$this->token_storage->set_refresh_token( $tokens['refresh_token'] );
		} else {
			wp_die( esc_html__( 'Failed to exchange code for tokens.', 'omniform' ) );
		}

		// Redirect back to the admin page.
		wp_safe_redirect( admin_url( 'admin.php?page=omniform' ) );
		exit;
	}

	/**
	 * Refresh the access token.
	 *
	 * @return bool
	 */
	public function refresh_access_token(): bool {
		$refresh_token = $this->token_storage->get_refresh_token();
		$client_id     = $this->token_storage->get_client_id();

		if ( ! $refresh_token || ! $client_id ) {
			return false;
		}

		$response = wp_remote_post(
			$this->api_base_url . '/oauth/token',
			array(
				'body' => array(
					'grant_type'    => 'refresh_token',
					'refresh_token' => $refresh_token,
					'client_id'     => $client_id,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['access_token'] ) ) {
			$this->token_storage->set_access_token( $body['access_token'], $body['expires_in'] );
			if ( isset( $body['refresh_token'] ) ) {
				$this->token_storage->set_refresh_token( $body['refresh_token'] );
			}
			return true;
		}

		return false;
	}

	/**
	 * Get the redirect URI.
	 *
	 * @return string
	 */
	private function get_redirect_uri(): string {
		return admin_url( 'admin.php?page=omniform' );
	}

	/**
	 * Generate a code verifier.
	 *
	 * @return string
	 */
	private function generate_code_verifier(): string {
		return bin2hex( random_bytes( 32 ) );
	}

	/**
	 * Generate a code challenge from verifier.
	 *
	 * @param string $verifier The code verifier.
	 *
	 * @return string
	 */
	private function generate_code_challenge( string $verifier ): string {
		return rtrim( strtr( base64_encode( hash( 'sha256', $verifier, true ) ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Exchange authorization code for tokens.
	 *
	 * @param string $code         The authorization code.
	 * @param string $code_verifier The code verifier.
	 *
	 * @return array|null
	 */
	private function exchange_code_for_tokens( string $code, string $code_verifier ): ?array {
		$client_id = $this->token_storage->get_client_id();

		if ( ! $client_id ) {
			return null;
		}

		$response = wp_remote_post(
			$this->api_base_url . '/oauth/token',
			array(
				'body' => array(
					'grant_type'    => 'authorization_code',
					'client_id'     => $client_id,
					'redirect_uri'  => $this->get_redirect_uri(),
					'code'          => $code,
					'code_verifier' => $code_verifier,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return null;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['access_token'] ) ) {
			return $body;
		}

		return null;
	}
}
