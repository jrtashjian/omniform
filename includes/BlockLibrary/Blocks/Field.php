<?php
/**
 * The Field block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Field block class.
 */
class Field extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		if ( empty( $this->get_block_attribute( 'fieldLabel' ) ) ) {
			return '';
		}

		return sprintf(
			'<div %s>%s</div>',
			get_block_wrapper_attributes(),
			$this->content
		);
	}
}
