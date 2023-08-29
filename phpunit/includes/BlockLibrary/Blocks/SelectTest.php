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
}

// phpcs:disable
class SelectBlock extends Select {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}