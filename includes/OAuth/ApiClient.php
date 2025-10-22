<?php
/**
 * The ApiClient class.
 *
 * @package OmniForm
 */

namespace OmniForm\OAuth;

/**
 * The ApiClient class.
 */
class ApiClient {
	/**
	 * Token storage.
	 *
	 * @var TokenStorage
	 */
	private TokenStorage $token_storage;

	/**
	 * OAuth manager.
	 *
	 * @var OAuthManager
	 */
	private OAuthManager $oauth_manager;

	/**
	 * API base URL.
	 *
	 * @var string
	 */
	private string $api_base_url;

	/**
	 * Constructor.
	 *
	 * @param TokenStorage $token_storage Token storage.
	 * @param OAuthManager $oauth_manager OAuth manager.
	 * @param string       $api_base_url  API base URL.
	 */
	public function __construct( TokenStorage $token_storage, OAuthManager $oauth_manager, string $api_base_url ) {
		$this->token_storage = $token_storage;
		$this->oauth_manager = $oauth_manager;
		$this->api_base_url  = $api_base_url;
	}

	/**
	 * Make a GET request to the API.
	 *
	 * @param string $endpoint The API endpoint.
	 * @param array  $args     Additional arguments.
	 *
	 * @return array|WP_Error
	 */
	public function get( string $endpoint, array $args = array() ) {
		return $this->request( 'GET', $endpoint, $args );
	}

	/**
	 * Make a POST request to the API.
	 *
	 * @param string $endpoint The API endpoint.
	 * @param array  $args     Additional arguments.
	 *
	 * @return array|WP_Error
	 */
	public function post( string $endpoint, array $args = array() ) {
		return $this->request( 'POST', $endpoint, $args );
	}

	/**
	 * Make a request to the API.
	 *
	 * @param string $method   The HTTP method.
	 * @param string $endpoint The API endpoint.
	 * @param array  $args     Additional arguments.
	 *
	 * @return array|WP_Error
	 */
	private function request( string $method, string $endpoint, array $args = array() ) {
		$access_token = $this->get_valid_access_token();

		if ( ! $access_token ) {
			return new \WP_Error( 'oauth_no_token', __( 'No valid access token available.', 'omniform' ) );
		}

		$url = $this->api_base_url . $endpoint;

		$request_args = array_merge(
			$args,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
					'Content-Type'  => 'application/json',
				),
			)
		);

		if ( 'GET' === $method ) {
			$response = wp_remote_get( $url, $request_args );
		} elseif ( 'POST' === $method ) {
			$response = wp_remote_post( $url, $request_args );
		} else {
			return new \WP_Error( 'invalid_method', __( 'Invalid HTTP method.', 'omniform' ) );
		}

		return $response;
	}

	/**
	 * Get a valid access token, refreshing if necessary.
	 *
	 * @return string|null
	 */
	private function get_valid_access_token(): ?string {
		if ( ! $this->token_storage->is_expired() ) {
			return $this->token_storage->get_access_token();
		}

		// Try to refresh the token.
		if ( $this->oauth_manager->refresh_access_token() ) {
			return $this->token_storage->get_access_token();
		}

		// Refresh failed, clear tokens.
		$this->token_storage->clear_tokens();
		return null;
	}
}
