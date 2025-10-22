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
		wp_redirect( $oauth_manager->get_authorization_url() );
		exit;
	}
}
