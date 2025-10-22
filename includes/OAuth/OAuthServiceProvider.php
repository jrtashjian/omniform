<?php
/**
 * The OAuthServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\OAuth;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;

/**
 * The OAuthServiceProvider class.
 */
class OAuthServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return bool
	 */
	public function provides( string $id ): bool {
		$services = array(
			OAuthManager::class,
			TokenStorage::class,
			ApiClient::class,
		);

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()->addShared(
			TokenStorage::class,
			function () {
				return new TokenStorage();
			}
		);

		$this->getContainer()->addShared(
			OAuthManager::class,
			function () {
				return new OAuthManager(
					$this->getContainer()->get( TokenStorage::class ),
					$this->get_api_base_url(),
					$this->get_account_base_url()
				);
			}
		);

		$this->getContainer()->addShared(
			ApiClient::class,
			function () {
				return new ApiClient(
					$this->getContainer()->get( TokenStorage::class ),
					$this->getContainer()->get( OAuthManager::class ),
					$this->get_api_base_url()
				);
			}
		);
	}

	/**
	 * Get the API base URL.
	 *
	 * @return string
	 */
	private function get_api_base_url(): string {
		return defined( 'OMNIFORM_API_BASE_URL' ) ? constant( 'OMNIFORM_API_BASE_URL' ) : 'http://api.omniform.io';
	}

	/**
	 * Get the account base URL.
	 *
	 * @return string
	 */
	private function get_account_base_url(): string {
		return defined( 'OMNIFORM_ACCOUNT_BASE_URL' ) ? constant( 'OMNIFORM_ACCOUNT_BASE_URL' ) : 'http://account.omniform.io';
	}

	/**
	 * Bootstrap any application services by hooking into WordPress with actions/filters.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'admin_init', array( $this, 'handle_oauth_callback' ) );
		add_action( 'admin_init', array( $this, 'handle_registration_callback' ) );
		add_action( 'admin_init', array( $this, 'handle_connect' ) );
		add_action( 'admin_init', array( $this, 'handle_disconnect' ) );
		add_action( 'allowed_redirect_hosts', array( $this, 'add_allowed_redirect_host' ) );
	}

	/**
	 * Handle OAuth callback.
	 *
	 * @return void
	 */
	public function handle_oauth_callback(): void {
		if ( ! isset( $_GET['page'] ) || 'omniform' !== $_GET['page'] || ! isset( $_GET['code'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$oauth_manager = $this->getContainer()->get( OAuthManager::class );
		$oauth_manager->handle_callback();
	}

	/**
	 * Handle client registration callback.
	 *
	 * @return void
	 */
	public function handle_registration_callback(): void {
		if ( ! isset( $_GET['page'] ) || 'omniform' !== $_GET['page'] || ! isset( $_GET['client_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$client_id = sanitize_text_field( wp_unslash( $_GET['client_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

		$token_storage = $this->getContainer()->get( TokenStorage::class );
		$token_storage->set_client_id( $client_id );

		// Redirect to initiate OAuth flow.
		$oauth_manager = $this->getContainer()->get( OAuthManager::class );
		wp_safe_redirect( $oauth_manager->get_authorization_url() );
		exit;
	}

	/**
	 * Adds allowed redirect hosts for OAuth authentication.
	 *
	 * @param array $hosts An array of hostnames to allow for redirects.
	 * @return array The updated array of allowed redirect hosts.
	 */
	public function add_allowed_redirect_host( array $hosts ): array {
		$urls = array(
			$this->get_api_base_url(),
			$this->get_account_base_url(),
		);

		$parsed_hosts = array_map(
			function ( $url ) {
				$parsed_url = wp_parse_url( $url );
				return isset( $parsed_url['host'] ) ? $parsed_url['host'] : null;
			},
			$urls
		);

		return array_merge( $hosts, array_filter( $parsed_hosts ) );
	}

	/**
	 * Handle connect API action.
	 *
	 * @return void
	 */
	public function handle_connect(): void {
		if ( ! isset( $_POST['connect_api'] ) || ! isset( $_GET['page'] ) || 'omniform' !== $_GET['page'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'connect_api' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'omniform' ) );
		}

		$token_storage = $this->getContainer()->get( TokenStorage::class );
		$oauth_manager = $this->getContainer()->get( OAuthManager::class );

		$client_id = $token_storage->get_client_id();

		if ( $client_id ) {
			wp_safe_redirect( $oauth_manager->get_authorization_url() );
			exit;
		} else {
			wp_safe_redirect( $oauth_manager->get_registration_url() );
			exit;
		}
	}

	/**
	 * Handle disconnect API action.
	 *
	 * @return void
	 */
	public function handle_disconnect(): void {
		if ( ! isset( $_POST['disconnect_api'] ) || ! isset( $_GET['page'] ) || 'omniform' !== $_GET['page'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'disconnect_api' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'omniform' ) );
		}

		$token_storage = $this->getContainer()->get( TokenStorage::class );
		$token_storage->clear_tokens();

		wp_safe_redirect( admin_url( 'admin.php?page=omniform' ) );
		exit;
	}
}
