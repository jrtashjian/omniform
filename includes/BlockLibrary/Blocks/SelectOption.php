<?php
/**
 * The SelectOption block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The SelectOption block class.
 */
class SelectOption extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( empty( $this->getBlockAttribute( 'fieldLabel' ) ) ) {
			return '';
		}

		return sprintf(
			'<option value="%s">%s</option>',
			esc_attr( sanitize_title( $this->getBlockAttribute( 'fieldLabel' ) ) ),
			esc_attr( $this->getBlockAttribute( 'fieldLabel' ) ),
		);
	}
}
