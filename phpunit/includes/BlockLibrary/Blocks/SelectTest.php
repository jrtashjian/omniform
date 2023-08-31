<?php
/**
 * Tests the Select class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Select;

/**
 * Tests the Select class.
 */
class SelectTest extends FormBlockTestCase {
	/**
	 * The block instance to test against.
	 *
	 * @var \OmniForm\BlockLibrary\Blocks\Select
	 */
	protected $block_instance;

	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new SelectBlock() );
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
	 * Test rendering the select block with a placeholder.
	 */
	public function test_render_with_placeholder() {
		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );

		$this->assertFalse( strpos( $this->render_block_with_attributes(), '<option value="">' ) );
		$this->assertNotFalse( strpos( $this->render_block_with_attributes( array( 'fieldPlaceholder' => 'field placeholder' ) ), '<option value="">field placeholder</option>' ) );
	}

	/**
	 * Test the get_control_name method of the Select block.
	 */
	public function test_get_control_name() {
		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );

		$this->render_block_with_attributes();
		$this->assertEquals( $this->block_instance->get_control_name(), 'field-label' );

		$this->render_block_with_attributes( array( 'isMultiple' => true ) );
		$this->assertEquals( $this->block_instance->get_control_name(), 'field-label[]' );
	}
}

// phpcs:disable
class SelectBlock extends Select {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}