<?php
/**
 * The FieldTextarea block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The FieldTextarea block class.
 */
class FieldTextarea extends BaseFieldBlock {

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function renderControl() {
		return sprintf(
			'<textarea %s>%s</textarea>',
			trim( implode( ' ', $this->getControlAttributes() ) ),
			esc_textarea( $this->getControlValue() )
		);
	}

	/**
	 * The attr="value" attributes for the control.
	 *
	 * @return array
	 */
	protected function getControlAttributes() {
		$attributes = wp_parse_args(
			array(
				$this->getElementAttribute( 'placeholder', $this->getBlockAttribute( 'fieldPlaceholder' ) ),

				$this->getBlockAttribute( 'height' )
					? $this->getElementAttribute( 'style', 'height:' . $this->getBlockAttribute( 'height' ) . 'px' )
					: null,
			),
			parent::getControlAttributes()
		);
		return array_filter( $attributes );
	}
}
