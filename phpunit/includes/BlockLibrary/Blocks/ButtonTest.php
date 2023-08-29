<?php
/**
 * Tests the Button class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Button;

/**
 * Tests the Button class.
 */
class ButtonTest extends FormBlockTestCase {
	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new ButtonBlock() );
	}

	/**
	 * Make sure the block does not render markup if the buttonLabel attribute is empty.
	 */
	public function test_does_not_render_without_button_label() {
		$this->assertEmpty( $this->render_block_with_attributes() );

		$this->assertNotEmpty(
			$this->render_block_with_attributes(
				array(
					'buttonLabel' => 'button label',
				)
			)
		);
	}
}

// phpcs:disable
class ButtonBlock extends Button {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}