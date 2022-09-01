<?php
/**
 * Tests the Core class.
 *
 * @package PluginWP
 */

namespace PluginWP\Tests\Core;

use PluginWP\Application;
use PluginWP\ServiceProvider;

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
	 * Test the contstructor parameter will generate the correct path bindings.
	 */
	public function test_core_constructor_can_register_path_bindings() {
		$application = new Application( $this->plugin_file );

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
	 * Test that a service provider can be registered.
	 */
	public function test_core_register_provider() {
		$application = new Application();

		$application->register( ServiceProviderStub::class );

		$this->assertInstanceOf( ServiceProviderStub::class, $application->getProvider( ServiceProviderStub::class ) );
	}

	/**
	 * Test that the provider is returned when registered.
	 */
	public function test_core_returns_provider_when_registered() {
		$application = new Application();

		$this->assertInstanceOf( ServiceProviderStub::class, $application->register( ServiceProviderStub::class ) );
	}

	/**
	 * Test that the provider is only registered once.
	 */
	public function test_core_register_returns_existing_provider() {
		$application = new Application();

		$instance = $application->register( ServiceProviderStub::class );

		$this->assertSame( $instance, $application->register( ServiceProviderStub::class ) );
	}

	/**
	 * Test that providers can be "booted".
	 */
	public function test_core_can_boot_providers() {
		$application = new Application();

		$application->register( ServiceProviderStub::class );
		$application->boot();

		$this->expectOutputString( 'booted' );
	}

	/**
	 * Test that Core only boots providers once.
	 */
	public function test_core_boots_once_only() {
		$application = new Application();

		$application->register( ServiceProviderStub::class );
		$application->boot();
		$application->boot();

		$this->expectOutputRegex( '/^booted$/' );
	}

	/**
	 * Test that the deactivation hook is called.
	 */
	public function test_deactivation_hook_is_called() {
		$plugin_basename = plugin_basename( $this->plugin_file );

		register_deactivation_hook( $plugin_basename, array( pluginwp()->app, 'deactivation' ) );
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

// phpcs:disable
class ServiceProviderStub extends ServiceProvider {
	public function boot() {
		echo 'booted';
	}
}
