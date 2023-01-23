<?php
/**
 * Tests the Core class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Core;

use OmniForm\Application;

/**
 * Tests the Core class.
 */
class CoreTest extends \WP_UnitTestCase {
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
		$this->plugin_path = dirname( dirname( dirname( __FILE__ ) ) );
		$this->plugin_file = $this->plugin_path . '/' . basename( $this->plugin_path ) . '.php';
	}

	/**
	 * Test the correct path bindings are generated.
	 */
	public function test_core_register_path_bindings() {
		$application = new Application();

		$this->assertEmpty( $application->basePath() );
		$this->assertEmpty( $application->baseUrl() );

		$application->setBasePath( $this->plugin_file );

		$this->assertEquals( $this->plugin_path, $application->basePath() );
		$this->assertEquals( site_url( '/wp-content/plugins/' . basename( $this->plugin_path ) ), $application->baseUrl() );
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
