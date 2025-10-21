<?php
/**
 * Base test case for block tests.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit;

use OmniForm\BlockLibrary\Blocks\BaseBlock;

/**
 * Base test case for block tests with common mocks.
 */
class BaseBlockTestCase extends BaseTestCase {

	/**
	 * Set up common mocks for block tests.
	 */
	public function setUp(): void {
		parent::setUp();

		// Common mocks used across block tests.
		\WP_Mock::userFunction( 'sanitize_html_class' )->andReturnUsing(
			function ( $str ) {
				return strtolower( str_replace( ' ', '-', $str ) );
			}
		);

		\WP_Mock::userFunction( 'esc_attr' )->andReturnUsing(
			function ( $str ) {
				return (string) $str;
			}
		);

		\WP_Mock::userFunction( 'wp_kses' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);

		\WP_Mock::userFunction( 'wp_parse_args' )->andReturnUsing(
			function ( $args, $defaults ) {
				return array_merge( $defaults, $args );
			}
		);

		\WP_Mock::userFunction( 'wp_strip_all_tags' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);
	}

	/**
	 * Create a mock WP_Block with given context.
	 *
	 * @param array $context Block context.
	 * @return \WP_Block Mock block.
	 */
	protected function createBlockWithContext( array $context = array() ) {
		$block          = \Mockery::mock( '\OmniForm\BlockLibrary\Blocks\BaseBlock' );
		$block->context = $context;
		return $block;
	}
}
