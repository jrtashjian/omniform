<?php
/**
 * Tests the BaseControlBlock class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseControlBlock;

/**
 * Tests the BaseControlBlock class.
 */
class BaseControlBlockTest extends \WP_UnitTestCase {
	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$block_object = new TextControlBlock();

		register_block_type(
			$block_object->block_type_metadata(),
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
	}

	/**
	 * Helper method to render a block comment delimeter.
	 *
	 * @param array $attributes The block attributes.
	 */
	private function render_block_with_attributes( $attributes = array() ) {
		return do_blocks(
			serialize_block(
				array(
					'blockName'    => 'omniform/text-control-block',
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
	private function apply_block_context( $context, $value ) {
		add_filter(
			'render_block_context',
			function( $block_contexts ) use ( $context, $value ) {
				$block_contexts[ $context ] = $value;
				return $block_contexts;
			}
		);
	}

	/**
	 * Make sure the block does not render markup if the fieldLabel attribute is empty.
	 */
	public function test_does_not_render_without_field_label() {
		$this->assertEmpty( $this->render_block_with_attributes() );

		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );
		$this->assertNotEmpty( $this->render_block_with_attributes() );
	}

	/**
	 * Make sure the field label is used as the field name if the fieldName attribute is empty.
	 */
	public function test_field_name() {
		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );
		$this->assertStringContainsString( 'name="field-label"', $this->render_block_with_attributes() );

		$this->apply_block_context( 'omniform/fieldName', 'field name' );
		$this->assertStringContainsString( 'name="field-name"', $this->render_block_with_attributes() );
	}

	/**
	 * Make sure the field label is used as the field name if the fieldName attribute is empty.
	 */
	public function test_field_group_name() {
		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );
		$this->apply_block_context( 'omniform/fieldName', 'field name' );

		$this->apply_block_context( 'omniform/fieldGroupLabel', 'field group label' );
		$this->assertStringContainsString( 'name="field-group-label[field-name]"', $this->render_block_with_attributes() );

		$this->apply_block_context( 'omniform/fieldGroupName', 'field group name' );
		$this->assertStringContainsString( 'name="field-group-name[field-name]"', $this->render_block_with_attributes() );
	}
}

// phpcs:disable
class TextControlBlock extends BaseControlBlock {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}

	public function render_control() {
		return sprintf(
			'<div %s />',
			get_block_wrapper_attributes( $this->get_extra_wrapper_attributes() )
		);
	}
}
