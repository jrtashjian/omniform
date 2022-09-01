<?php
/**
 * The Application class.
 *
 * @package PluginWP
 */

namespace PluginWP;

use PluginWP\Dependencies\Illuminate\Container\Container;

/**
 * The Application class.
 */
class Application extends Container {
	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

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
	 * Indicates if the plugin has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * All of the registered service providers.
	 *
	 * @var ServiceProvider[]
	 */
	protected $service_providers = array();

	/**
	 * All of the registered service providers.
	 *
	 * @var ServiceProvider[]
	 */
	protected $loaded_providers = array();

	/**
	 * Create a new Application instance.
	 *
	 * @param string|null $plugin_file The full path to the main plugin file.
	 */
	public function __construct( $plugin_file = null ) {
		if ( $plugin_file ) {
			$this->setBasePath( $plugin_file );
		}

		$this->registerBaseBindings();
	}

	/**
	 * Register the basic bindings into the container.
	 */
	protected function registerBaseBindings() {
		static::setInstance( $this );

		// Set this instance as the 'app'.
		$this->instance( 'app', $this );

		// Alias parameter type hints so the 'app' instance is injected.
		$this->alias( 'app', self::class );
		$this->alias( 'app', Dependencies\Illuminate\Container\Container::class );
		$this->alias( 'app', Dependencies\Psr\Container\ContainerInterface::class );
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
	public function setBasePath( $plugin_file ) {
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
	public function basePath( $path = '' ) {
		return rtrim( $this->base_path, DIRECTORY_SEPARATOR ) . ( '' !== $path ? DIRECTORY_SEPARATOR . ltrim( $path, DIRECTORY_SEPARATOR ) : '' );
	}

	/**
	 * Get the base url of the plugin.
	 *
	 * @param string $path path from the root.
	 *
	 * @return string
	 */
	public function baseUrl( $path = '' ) {
		return rtrim( $this->base_url, '/' ) . ( '' !== $path ? '/' . ltrim( $path, '/' ) : '' );
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param ServiceProvider|string $provider service provider.
	 * @param bool                   $force force registration if already registered.
	 *
	 * @return ServiceProvider
	 */
	public function register( $provider, $force = false ) {
		$registered = $this->getProvider( $provider );
		if ( $registered && ! $force ) {
			return $registered;
		}

		// If the given "provider" is a string, we will resolve it, passing in the
		// application instance automatically for the developer. This is simply
		// a more convenient way of specifying your service provider classes.
		if ( is_string( $provider ) ) {
			$provider = $this->resolveProvider( $provider );
		}

		$provider->register();

		// If there are bindings / singletons set as properties on the provider we
		// will spin through them and register them with the application, which
		// serves as a convenience layer while registering a lot of bindings.
		if ( property_exists( $provider, 'bindings' ) ) {
			foreach ( $provider->bindings as $key => $value ) {
				$this->bind( $key, $value );
			}
		}

		if ( property_exists( $provider, 'singletons' ) ) {
			foreach ( $provider->singletons as $key => $value ) {
				$this->singleton( $key, $value );
			}
		}

		$this->markAsRegistered( $provider );

		// If the application has already booted, we will call this boot method on
		// the provider class so it has an opportunity to do its boot logic and
		// will be ready for any usage by this developer's application logic.
		if ( $this->isBooted() ) {
			$this->bootProvider( $provider );
		}

		return $provider;
	}

	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param string $provider Class name to resolve.
	 *
	 * @return ServiceProvider
	 */
	public function resolveProvider( $provider ) {
		return new $provider( $this );
	}

	/**
	 * Mark the given provider as registered.
	 *
	 * @param ServiceProvider|string $provider service provider.
	 */
	protected function markAsRegistered( $provider ) {
		$this->service_providers[] = $provider;

		$this->loaded_providers[ get_class( $provider ) ] = true;
	}

	/**
	 * Determine if the application has booted.
	 *
	 * @return bool
	 */
	public function isBooted() {
		return $this->booted;
	}

	/**
	 * Boot the application's service providers.
	 */
	public function boot() {
		if ( $this->isBooted() ) {
			return;
		}

		array_walk(
			$this->service_providers,
			function( $p ) {
				$this->bootProvider( $p );
			}
		);

		$this->booted = true;
	}

	/**
	 * Boot the given service provider.
	 *
	 * @param ServiceProvider $provider service provider.
	 */
	protected function bootProvider( ServiceProvider $provider ) {
		if ( method_exists( $provider, 'boot' ) ) {
			$this->call( array( $provider, 'boot' ) );
		}
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param ServiceProvider|string $provider service provider.
	 *
	 * @return ServiceProvider|null
	 */
	public function getProvider( $provider ) {
		return array_values( $this->getProviders( $provider ) )[0] ?? null;
	}

	/**
	 * Get the registered service provider instances if any exist.
	 *
	 * @param ServiceProvider|string $provider service provider.
	 *
	 * @return array
	 */
	public function getProviders( $provider ) {
		$name = is_string( $provider ) ? $provider : get_class( $provider );

		$providers = array_filter(
			$this->service_providers,
			function( $value ) use ( $name ) {
				return $value instanceof $name;
			}
		);

		return $providers;
	}

	/**
	 * Callback for plugin deactivation.
	 */
	public function deactivation() {}

	/**
	 * Load language files.
	 */
	public function loadTextDomain() {
		load_plugin_textdomain( 'pluginwp', false, $this->basePath( 'languages' ) );
	}
}
