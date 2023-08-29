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
class BaseControlBlockTest extends FormBlockTestCase {
	/**
	 * The block instance to test against.
	 *
	 * @var \OmniForm\BlockLibrary\Blocks\BaseControlBlock
	 */
	protected $block_instance;

	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new TextControlBlock() );
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
		$this->render_block_with_attributes();
		$this->assertFalse( $this->block_instance->is_grouped() );

		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );
		$this->apply_block_context( 'omniform/fieldName', 'field name' );

		$this->apply_block_context( 'omniform/fieldGroupLabel', 'field group label' );
		$this->assertStringContainsString( 'name="field-group-label[field-name]"', $this->render_block_with_attributes() );

		$this->apply_block_context( 'omniform/fieldGroupName', 'field group name' );
		$this->assertStringContainsString( 'name="field-group-name[field-name]"', $this->render_block_with_attributes() );

		$this->assertTrue( $this->block_instance->is_grouped() );
	}

	/**
	 * Make sure validation rules are added to the field if the fieldIsRequired context is true.
	 */
	public function test_validation_rules() {
		$this->render_block_with_attributes();
		$this->assertEmpty( $this->block_instance->has_validation_rules() );

		$this->apply_block_context( 'omniform/fieldIsRequired', true );
		$this->render_block_with_attributes();
		$this->assertNotEmpty( $this->block_instance->has_validation_rules() );
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
