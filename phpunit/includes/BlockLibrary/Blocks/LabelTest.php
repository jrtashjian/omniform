<?php
/**
 * Tests the Label class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Label;

/**
 * Tests the Label class.
 */
class LabelTest extends FormBlockTestCase {
	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new LabelBlock() );
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
	 * Ensure the label indicates if the field is required.
	 */
	public function test_label_required() {
		$this->apply_block_context( 'omniform/fieldLabel', 'field label' );
		$this->apply_block_context( 'omniform/fieldIsRequired', true );
		$this->assertStringContainsString( 'class="omniform-field-required"', $this->render_block_with_attributes() );
		$this->assertStringContainsString( '>*</abbr>', $this->render_block_with_attributes() );
	}
}

// phpcs:disable
class LabelBlock extends Label {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}