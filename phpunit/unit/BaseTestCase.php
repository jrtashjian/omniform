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
			function ( $attrs = array() ) {
				$parts = array();
				foreach ( $attrs as $key => $value ) {
					$parts[] = $key . '="' . $value . '"';
				}
				return implode( ' ', $parts );
			}
		);

		WP_Mock::userFunction( 'sanitize_html_class' )->andReturnUsing(
			function ( $classname ) {
				// simplified re-implementation of sanitize_html_class.
				$sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $classname );
				$sanitized = preg_replace( '/[^A-Za-z0-9_-]/', '', $sanitized );
				return $sanitized;
			}
		);

		WP_Mock::userFunction( 'wp_strip_all_tags' )->andReturnUsing(
			function ( $text ) {
				$text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
				$text = strip_tags( $text ); // phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
				return trim( $text );
			}
		);

		WP_Mock::userFunction( 'wp_kses' )->andReturnArg( 0 );
	}
}
