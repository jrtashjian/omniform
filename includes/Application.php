<?php
/**
 * The Application class.
 *
 * @package OmniForm
 */

namespace OmniForm;

use OmniForm\Dependencies\League\Container\Container;
use OmniForm\Dependencies\League\Container\DefinitionContainerInterface;

/**
 * The Application class.
 */
class Application extends Container {
	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.2.1';

	/**
	 * The base path for the plugin.
	 *
	 * @var string
	 */
	protected $base_path;

	/**
	 * The base url for the plugin.
	 *
	 * @var string
	 */
	protected $base_url;

	/**
	 * Get the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( \is_null( static::$instance ) ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Set the shared instance of the container.
	 *
	 * @param  \OmniForm\Dependencies\League\Container\DefinitionContainerInterface|null $container The Dependency Injection Container.
	 *
	 * @return \OmniForm\Dependencies\League\Container\DefinitionContainerInterface|static
	 */
	public static function set_instance( DefinitionContainerInterface $container = null ) {
		static::$instance = $container;
		return static::$instance;
	}

	/**
	 * Get the version number of the application.
	 *
	 * @return string
	 */
	public function version() {
		return static::VERSION;
	}

	/**
	 * Register the path bindings based on the main plugin file.
	 *
	 * @param string $plugin_file The full path to the main plugin file.
	 */
	public function set_base_path( $plugin_file ) {
		$this->base_path = plugin_dir_path( $plugin_file );
		$this->base_url  = plugin_dir_url( $plugin_file );
	}

	/**
	 * Get the base path of the plugin.
	 *
	 * @param string $path path from the root.
	 *
	 * @return string
	 */
	public function base_path( $path = '' ) {
		return rtrim( $this->base_path, DIRECTORY_SEPARATOR ) . ( '' !== $path ? DIRECTORY_SEPARATOR . ltrim( $path, DIRECTORY_SEPARATOR ) : '' );
	}

	/**
	 * Get the base url of the plugin.
	 *
	 * @param string $path path from the root.
	 *
	 * @return string
	 */
	public function base_url( $path = '' ) {
		return rtrim( $this->base_url, '/' ) . ( '' !== $path ? '/' . ltrim( $path, '/' ) : '' );
	}

	/**
	 * Callback for plugin activation.
	 */
	public function activation() {
		do_action( 'omniform_activate' );
	}


	/**
	 * Callback for plugin deactivation.
	 */
	public function deactivation() {
		do_action( 'omniform_deactivate' );
	}
}
