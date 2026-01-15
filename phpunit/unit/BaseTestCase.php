<?php
/**
 * Base test case for OmniForm tests.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit;

use WP_Mock;
use WP_Mock\Tools\TestCase as WP_Mock_TestCase;

/**
 * Base test case extending WP_Mock TestCase.
 */
class BaseTestCase extends WP_Mock_TestCase {
	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction( 'wp_parse_args' )->andReturnUsing(
			function ( $args, $defaults ) {
				return array_merge( $defaults, $args );
			}
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturnUsing(
			function ( $attrs ) {
				$parts = array();
				foreach ( $attrs as $key => $value ) {
					$parts[] = $key . '="' . $value . '"';
				}
				return implode( ' ', $parts );
			}
		);
	}
}
