<?php
/**
 * The WebhookServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\Webhook;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;
use OmniForm\OAuth\TokenStorage;

/**
 * The WebhookServiceProvider class.
 */
class WebhookServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return bool
	 */
	public function provides( string $id ): bool {
		$services = array(
			WebhookController::class,
		);

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()
			->add( WebhookController::class )
			->addArgument( TokenStorage::class );
	}

	/**
	 * Bootstrap any application services by hooking into WordPress with actions/filters.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the REST API routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		$this->getContainer()->get( WebhookController::class )->register_routes();
	}
}
