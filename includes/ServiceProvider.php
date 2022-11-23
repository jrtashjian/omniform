<?php
/**
 * The ServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm;

/**
 * The ServiceProvider class.
 */
abstract class ServiceProvider {
	/**
	 * The application instance.
	 *
	 * @var \OmniForm\Application
	 */
	protected $app;

	/**
	 * Create a new service provider instance.
	 *
	 * @param \OmniForm\Application $app The Application.
	 */
	public function __construct( $app ) {
		$this->app = $app;
	}

	/**
	 * Register any application services.
	 */
	public function register() {}
}
