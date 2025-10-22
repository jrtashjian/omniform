<?php
/**
 * Tests the Application class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit;

use OmniForm\Application;
use OmniForm\Dependencies\League\Container\Container;
use WP_Mock;

/**
 * Tests the Application class.
 */
class ApplicationTest extends BaseTestCase {
	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
			define( 'MINUTE_IN_SECONDS', 60 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		}

		// Reset the singleton instance.
		Application::set_instance( null );
	}

	/**
	 * Test the singleton pattern (get_instance and set_instance).
	 */
	public function testGetInstanceReturnsSingleton() {
		$instance1 = Application::get_instance();
		$instance2 = Application::get_instance();

		$this->assertSame( $instance1, $instance2, 'get_instance should return the same instance' );
		$this->assertInstanceOf( Application::class, $instance1, 'Instance should be of Application class' );
	}

	/**
	 * Tests the setInstance method to ensure it correctly sets the application instance.
	 */
	public function testSetInstance() {
		$new_container = new Container();
		$result        = Application::set_instance( $new_container );

		$this->assertSame( $new_container, $result, 'set_instance should return the provided container' );
		$this->assertSame( $new_container, Application::get_instance(), 'get_instance should return the set container' );

		// Reset for subsequent tests.
		Application::set_instance( null );
	}

	/**
	 * Tests the setInstance method when passed a null value.
	 */
	public function testSetInstanceWithNull() {
		$result = Application::set_instance( null );

		$this->assertNull( $result, 'set_instance with null should return null' );
		$this->assertInstanceOf( Application::class, Application::get_instance(), 'get_instance should still return a new instance after set_instance(null)' );
	}

	/**
	 * Test the version method.
	 */
	public function testVersion() {
		$app = Application::get_instance();
		$this->assertEquals( Application::VERSION, $app->version(), 'Version should match defined constant' );
	}

	/**
	 * Test setting and getting base path.
	 */
	public function testSetBasePath() {
		$plugin_file   = '/path/to/plugin/main.php';
		$expected_path = '/path/to/plugin/';
		$expected_url  = 'http://example.com/plugin/';

		// Mock WordPress functions.
		WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( $plugin_file ),
				'return' => $expected_path,
			)
		);

		WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'args'   => array( $plugin_file ),
				'return' => $expected_url,
			)
		);

		$app = Application::get_instance();

		$this->assertSame( '', $app->base_path(), 'base_path() should return empty string when base_path property is not set' );
		$this->assertSame( '', $app->base_path( 'subfolder' ), 'base_path() with subfolder should return empty string when base_path property is not set' );

		$this->assertSame( '', $app->base_url(), 'base_url() should return empty string when base_url property is not set' );
		$this->assertSame( '', $app->base_url( 'subfolder' ), 'base_url() with subfolder should return empty string when base_url property is not set' );

		$app->set_base_path( $plugin_file );

		// Test that base_path and base_url work correctly after setting.
		$this->assertEquals( rtrim( $expected_path, '/' ), $app->base_path(), 'Base path should return root path' );
		$this->assertEquals( rtrim( $expected_url, '/' ), $app->base_url(), 'Base URL should return root URL' );
	}

	/**
	 * Test base_path method with various path scenarios.
	 */
	public function testBasePathWithDifferentPaths() {
		$plugin_file   = '/path/to/plugin/main.php';
		$expected_root = '/path/to/plugin';

		WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( $plugin_file ),
				'return' => $expected_root . '/',
			)
		);

		WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'args'   => array( $plugin_file ),
				'return' => 'http://example.com/plugin/',
			)
		);

		$app = Application::get_instance();
		$app->set_base_path( $plugin_file );

		$this->assertEquals( $expected_root, $app->base_path(), 'Base path with empty path should return root' );
		$this->assertEquals( $expected_root . '/subfolder', $app->base_path( 'subfolder' ), 'Base path should append simple path' );
		$this->assertEquals( $expected_root . '/subfolder', $app->base_path( '/subfolder' ), 'Base path should handle leading slash' );
		$this->assertEquals( $expected_root . '/folder1/folder2', $app->base_path( 'folder1/folder2' ), 'Base path should handle multiple segments' );
		$this->assertEquals( $expected_root . '/folder/', $app->base_path( 'folder/' ), 'Base path should handle trailing slash' );
	}

	/**
	 * Test base_url method with various path scenarios.
	 */
	public function testBaseUrlWithDifferentPaths() {
		$plugin_file   = '/path/to/plugin/main.php';
		$expected_root = 'http://example.com/plugin';

		WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( $plugin_file ),
				'return' => '/path/to/plugin/',
			)
		);

		WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'args'   => array( $plugin_file ),
				'return' => $expected_root . '/',
			)
		);

		$app = Application::get_instance();
		$app->set_base_path( $plugin_file );

		$this->assertEquals( $expected_root, $app->base_url(), 'Base URL with empty path should return root' );
		$this->assertEquals( $expected_root . '/subfolder', $app->base_url( 'subfolder' ), 'Base URL should append simple path' );
		$this->assertEquals( $expected_root . '/subfolder', $app->base_url( '/subfolder' ), 'Base URL should handle leading slash' );
		$this->assertEquals( $expected_root . '/folder1/folder2', $app->base_url( 'folder1/folder2' ), 'Base URL should handle multiple segments' );
		$this->assertEquals( $expected_root . '/folder/', $app->base_url( 'folder/' ), 'Base URL should handle trailing slash' );
	}

	/**
	 * Test activation method.
	 */
	public function testActivation() {
		// Mock WordPress functions with exact expected arguments.
		WP_Mock::userFunction(
			'add_option',
			array(
				'args'  => array( 'omniform_initial_version', '1.3.3', '', false ),
				'times' => 1,
			)
		);

		WP_Mock::userFunction(
			'add_option',
			array(
				'args'  => array( 'omniform_activated_time', WP_Mock\Functions::type( 'int' ), '', false ),
				'times' => 1,
			)
		);

		WP_Mock::userFunction(
			'set_transient',
			array(
				'args'  => array( 'omniform_just_activated', true, MINUTE_IN_SECONDS ),
				'times' => 1,
			)
		);

		WP_Mock::expectAction( 'omniform_activate' );

		$app = Application::get_instance();
		$app->activation();

		// All assertions are handled by WP_Mock.
		$this->assertTrue( true, 'Activation completed without errors' );
	}

	/**
	 * Test deactivation method.
	 */
	public function testDeactivation() {
		// Mock action hook as per WP_Mock hooks doc.
		WP_Mock::expectAction( 'omniform_deactivate' );

		$app = Application::get_instance();
		$app->deactivation();

		// All assertions are handled by WP_Mock.
		$this->assertTrue( true, 'Deactivation completed without errors' );
	}
}
