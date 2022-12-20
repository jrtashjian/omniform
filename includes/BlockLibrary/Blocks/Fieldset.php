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

		$attributes = array_filter(
			array(
				$this->getElementAttribute( 'class', $this->getDefaultClasses() ),
				$this->getElementAttribute( 'style', $this->getColorStyles( $this->attributes ) ),
			)
		);

		return sprintf(
			'<fieldset %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			trim( implode( ' ', $attributes ) ),
			esc_html( $this->getBlockAttribute( 'fieldLabel' ) ),
			$this->content
		);
	}

	/**
	 * Get the default classes to be applied to the block wrapper.
	 *
	 * @return array
	 */
	public function getDefaultClasses() {
		$default = array(
			$this->blockTypeClassname(),
			'is-layout-flow',
		);

		return array_merge(
			$default,
			$this->getColorClasses( $this->attributes ),
		);
	}
}
