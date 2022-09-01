<?php
/**
 * The ServiceProvider class.
 *
 * @package InquiryWP
 */

namespace InquiryWP;

/**
 * The ServiceProvider class.
 */
abstract class ServiceProvider {
	/**
	 * The application instance.
	 *
	 * @var \InquiryWP\Application
	 */
	protected $app;

	/**
	 * Create a new service provider instance.
	 *
	 * @param \InquiryWP\Application $app The Application.
	 */
	public function __construct( $app ) {
		$this->app = $app;
	}

	/**
	 * Register any application services.
	 */
	public function register() {}
}
