<?php
/**
 * The SelectGroup block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The SelectGroup block class.
 */
class SelectGroup extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( empty( $this->get_block_attribute( 'fieldLabel' ) ) ) {
			return '';
		}

		return sprintf(
			'<optgroup label="%s">%s</optgroup>',
			esc_attr( $this->get_block_attribute( 'fieldLabel' ) ),
			$this->content
		);
	}
}
