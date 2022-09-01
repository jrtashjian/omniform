<?php
/**
 * The ServiceProvider class.
 *
 * @package PluginWP
 */

namespace PluginWP;

/**
 * The ServiceProvider class.
 */
abstract class ServiceProvider {
	/**
	 * The application instance.
	 *
	 * @var \PluginWP\Application
	 */
	protected $app;

	/**
	 * Create a new service provider instance.
	 *
	 * @param \PluginWP\Application $app The Application.
	 */
	public function __construct( $app ) {
		$this->app = $app;
	}

	/**
	 * Register any application services.
	 */
	public function register() {}
}
