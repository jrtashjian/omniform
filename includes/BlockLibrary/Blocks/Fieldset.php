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

		$classes = array_merge(
			array(
				$this->blockTypeClassName(),
				'is-layout-flow',
			),
			$this->getColorClasses( $this->attributes ),
		);

		return sprintf(
			'<fieldset %s %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			$this->getElementAttribute( 'class', $classes ),
			$this->getElementAttribute( 'style', $this->getColorStyles( $this->attributes ) ),
			esc_html( $this->attributes['fieldLabel'] ),
			$this->content
		);
	}
}
