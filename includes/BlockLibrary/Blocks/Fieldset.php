<?php
/**
 * The Fieldset block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;

/**
 * The Fieldset block class.
 */
class Fieldset extends BaseBlock {
	use HasColors;

	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		if ( empty( $this->getBlockAttribute( 'fieldLabel' ) ) ) {
			return '';
		}

		return sprintf(
			'<fieldset %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			get_block_wrapper_attributes(),
			esc_html( $this->getBlockAttribute( 'fieldLabel' ) ),
			$this->content
		);
	}
}
