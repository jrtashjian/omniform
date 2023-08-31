<?php
/**
 * Tests the Application class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Application;

use OmniForm\Application;

/**
 * Tests the Application class.
 */
class ApplicationTest extends \WP_UnitTestCase {
	/**
	 * The full path to the plugin.
	 *
	 * @var string
	 */
	protected $plugin_path;

	/**
	 * The full path to the plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * This method is called before each test.
	 */
	public function set_up() {
		$this->plugin_path = dirname( dirname( __DIR__ ) );
		$this->plugin_file = $this->plugin_path . '/' . basename( $this->plugin_path ) . '.php';
	}

	/**
	 * Test the container's singleton.
	 */
	public function test_core_singleton() {
		$application = Application::set_instance( new Application() );

		$this->assertSame( $application, Application::get_instance() );

		Application::set_instance( null );

		$application2 = Application::get_instance();

		$this->assertInstanceOf( Application::class, $application2 );
		$this->assertNotSame( $application, $application2 );
	}

	/**
	 * Test the correct path bindings are generated.
	 */
	public function test_core_register_path_bindings() {
		$application = new Application();

		$this->assertEmpty( $application->base_path() );
		$this->assertEmpty( $application->base_url() );

		$application->set_base_path( $this->plugin_file );

		$this->assertEquals( $this->plugin_path, $application->base_path() );
		$this->assertEquals( site_url( '/wp-content/plugins/' . basename( $this->plugin_path ) ), $application->base_url() );
	}

	/**
	 * Test the version is accessible.
	 */
	public function test_core_returns_version() {
		$application = new Application();

		$plugin_data = get_plugin_data( $this->plugin_file, false );

		$this->assertEquals( $plugin_data['Version'], $application->version() );
	}

	/**
	 * Test that the activation hook is called.
	 */
	public function test_activation_hook_is_called() {
		$plugin_basename = plugin_basename( $this->plugin_file );

		register_activation_hook( $plugin_basename, array( omniform(), 'activation' ) );
		$this->assertTrue( has_filter( 'activate_' . $plugin_basename ) );

		do_action( 'activate_' . $plugin_basename ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	}

	/**
	 * Test that the deactivation hook is called.
	 */
	public function test_deactivation_hook_is_called() {
		$plugin_basename = plugin_basename( $this->plugin_file );

		register_deactivation_hook( $plugin_basename, array( omniform(), 'deactivation' ) );
		$this->assertTrue( has_filter( 'deactivate_' . $plugin_basename ) );

		do_action( 'deactivate_' . $plugin_basename ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	}

	/**
	 * Test that the deactivation hook removes the maintenance job.
	 */
	public function test_deactivation_hook_removes_maintenance_job() {
		$plugin_basename = plugin_basename( $this->plugin_file );
		$this->assertTrue( has_filter( 'deactivate_' . $plugin_basename ) );
	}
}
