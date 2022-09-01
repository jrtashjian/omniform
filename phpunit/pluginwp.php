<?php
/**
 * Tests the plugin bootstrap file.
 *
 * @package PluginWP
 */

namespace PluginWP\Tests;

use PluginWP\Application;

/**
 * Tests the plugin bootstrap file.
 */
class Bootstrap_Test extends \WP_UnitTestCase {

	/**
	 * Test that the plugin has been successfully loaded into the test suite.
	 */
	public function test_curatewp_loaded() {
		$this->assertTrue( class_exists( Application::class ) );
	}

	/**
	 * Test that the pluginwp() helper function returns an instance of the Container.
	 */
	public function test_curatewp_helper_returns_container_instance() {
		$container_instance = pluginwp();
		$this->assertTrue( $container_instance instanceof Application );
	}
}
