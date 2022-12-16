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

		$classes = array(
			'wp-block-omniform-' . $this->blockTypeName(),
			'is-layout-flow',
			$this->getColorClasses( $this->attributes ),
		);

		return sprintf(
			'<fieldset class="%s" %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			esc_attr( implode( ' ', $classes ) ),
			$this->getColorStyles( $this->attributes ),
			esc_html( $this->attributes['fieldLabel'] ),
			$this->renderContent()
		);
	}
}
