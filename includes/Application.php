<?php
/**
 * The Application class.
 *
 * @package OmniForm
 */

namespace OmniForm;

use OmniForm\Dependencies\League\Container\Container;
use OmniForm\Dependencies\League\Container\DefinitionContainerInterface;
use OmniForm\Dependencies\League\Container\ServiceProvider\ServiceProviderInterface;
use OmniForm\Form\Form;
use OmniForm\Plugin\FormRepository;

/**
 * Plugin application: metadata, lifecycle, and a composed DI container.
 *
 * Public form access is via form() / form_from_content(). The container is for
 * internal wiring and advanced extension, not the primary API surface.
 */
class Application {
	/**
	 * The current globally available application (if any).
	 *
	 * @var self|null
	 */
	protected static ?self $instance = null;

	/**
	 * The plugin version.
	 */
	private const VERSION = '1.3.4';

	/**
	 * The dependency injection container.
	 *
	 * @var DefinitionContainerInterface
	 */
	protected DefinitionContainerInterface $container;

	/**
	 * The base path for the plugin.
	 *
	 * @var string
	 */
	protected string $base_path;

	/**
	 * The base url for the plugin.
	 *
	 * @var string
	 */
	protected string $base_url;

	/**
	 * Create a new application instance.
	 *
	 * @param DefinitionContainerInterface|null $container Optional container. Defaults to a new League Container.
	 */
	public function __construct( ?DefinitionContainerInterface $container = null ) {
		$this->container = $container ?? new Container();
		$this->container->addShared( static::class, $this );
		$this->bind_plugin_paths();
	}

	/**
	 * Get the globally available application instance.
	 */
	public static function get_instance(): static {
		return static::$instance ??= new static();
	}

	/**
	 * Set the shared application instance.
	 *
	 * @param self|null $application The application instance, or null to clear.
	 */
	public static function set_instance( ?self $application = null ): ?self {
		static::$instance = $application;

		return static::$instance;
	}

	/**
	 * The dependency injection container used for internal wiring.
	 */
	public function container(): DefinitionContainerInterface {
		return $this->container;
	}

	/**
	 * Register a service provider with the container.
	 *
	 * @param ServiceProviderInterface $provider The service provider.
	 */
	public function register( ServiceProviderInterface $provider ): static {
		$this->container->addServiceProvider( $provider );
		return $this;
	}

	/**
	 * Load a domain Form for the given post ID.
	 *
	 * @param int $form_id The form post ID.
	 */
	public function form( int $form_id ): Form {
		return ( new FormRepository() )->get( $form_id );
	}

	/**
	 * Create a content-only domain Form from serialized block content.
	 *
	 * @param string $content The form content.
	 */
	public function form_from_content( string $content ): Form {
		return Form::from_content( $content );
	}

	/**
	 * Get the version number of the application.
	 */
	public function version(): string {
		return self::VERSION;
	}

	/**
	 * Get the base path of the plugin.
	 *
	 * @param string $path path from the root.
	 */
	public function base_path( string $path = '' ): string {
		return $this->base_path
			. ( '' !== $path ? DIRECTORY_SEPARATOR . ltrim( $path, DIRECTORY_SEPARATOR ) : '' );
	}

	/**
	 * Get the base url of the plugin.
	 *
	 * @param string $path path from the root.
	 */
	public function base_url( string $path = '' ): string {
		return $this->base_url
			. ( '' !== $path ? '/' . ltrim( $path, '/' ) : '' );
	}

	/**
	 * Callback for plugin activation.
	 */
	public function activation(): void {
		add_option( 'omniform_initial_version', self::VERSION, '', false );
		add_option( 'omniform_activated_time', time(), '', false );
		set_transient( 'omniform_just_activated', true, MINUTE_IN_SECONDS );
		do_action( 'omniform_activate' );
	}

	/**
	 * Callback for plugin deactivation.
	 */
	public function deactivation(): void {
		do_action( 'omniform_deactivate' );
	}

	/**
	 * Bind base path and URL from the root plugin file.
	 */
	private function bind_plugin_paths(): void {
		$plugin_file     = dirname( __DIR__ ) . '/omniform.php';
		$this->base_path = rtrim( plugin_dir_path( $plugin_file ), DIRECTORY_SEPARATOR );
		$this->base_url  = rtrim( plugin_dir_url( $plugin_file ), '/' );
	}
}
