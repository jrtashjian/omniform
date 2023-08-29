<?php
/**
 * Tests the Field class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Field;

/**
 * Tests the Field class.
 */
class FieldTest extends FormBlockTestCase {
	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new FieldBlock() );
	}

	/**
	 * Make sure the block does not render markup if the fieldLabel attribute is empty.
	 */
	public function test_does_not_render_without_field_label() {
		$this->assertEmpty( $this->render_block_with_attributes() );

		$this->assertNotEmpty(
			$this->render_block_with_attributes(
				array(
					'fieldLabel' => 'field label',
				)
			)
		);
	}
}

// phpcs:disable
class FieldBlock extends Field {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}