<?php
/**
 * Tests the plugin bootstrap file.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests;

use OmniForm\Application;

/**
 * Tests the plugin bootstrap file.
 */
class BootstrapTest extends \WP_UnitTestCase {

	/**
	 * Test that the plugin has been successfully loaded into the test suite.
	 */
	public function test_omniform_loaded() {
		$this->assertTrue( class_exists( Application::class ) );
	}

	/**
	 * Test that the omniform() helper function returns an instance of the Container.
	 */
	public function test_omniform_helper_returns_container_instance() {
		$container_instance = omniform();
		$this->assertTrue( $container_instance instanceof Application );
	}
}
