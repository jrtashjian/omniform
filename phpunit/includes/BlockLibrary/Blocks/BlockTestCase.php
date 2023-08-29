<?php
/**
 * Tests the BlockTestCase class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

/**
 * Tests the BlockTestCase class.
 */
class BlockTestCase extends \WP_UnitTestCase {
	/**
	 * The block instance to test against.
	 *
	 * @var \OmniForm\BlockLibrary\Blocks\BaseControlBlock
	 */
	protected $block_instance;

	/**
	 * The block type name to test against.
	 *
	 * @var string
	 */
	protected $block_type_name;

	/**
	 * Register the block to test against.
	 *
	 * @param \OmniForm\BlockLibrary\Blocks\BaseControlBlock $block_object The block object to test against.
	 */
	protected function register_block_type( \OmniForm\BlockLibrary\Blocks\BaseControlBlock $block_object ) {
		$this->block_type_name = $block_object->block_type_metadata();

		register_block_type(
			$this->block_type_name,
			array(
				'render_callback' => array( $block_object, 'render_block' ),
				'uses_context'    => array(
					'omniform/fieldGroupName',
					'omniform/fieldGroupLabel',
					'omniform/fieldGroupIsRequired',
					'omniform/fieldLabel',
					'omniform/fieldName',
					'omniform/fieldIsRequired',
				),
			)
		);

		add_filter(
			'render_block_' . $this->block_type_name,
			function( $block_content, $block, $instance ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
				$this->block_instance = $instance->block_type->render_callback[0];
				return $block_content;
			},
			10,
			3
		);
	}

	/**
	 * Helper method to render a block comment delimeter.
	 *
	 * @param array $attributes The block attributes.
	 */
	protected function render_block_with_attributes( $attributes = array() ) {
		return do_blocks(
			serialize_block(
				array(
					'blockName'    => $this->block_type_name,
					'attrs'        => $attributes,
					'innerContent' => array(),
				)
			)
		);
	}

	/**
	 * Helper method to apply a block context.
	 *
	 * @param string $context The block context to apply.
	 * @param mixed  $value   The value to apply to the block context.
	 */
	protected function apply_block_context( $context, $value ) {
		add_filter(
			'render_block_context',
			function( $block_contexts ) use ( $context, $value ) {
				$block_contexts[ $context ] = $value;
				return $block_contexts;
			}
		);
	}
}
