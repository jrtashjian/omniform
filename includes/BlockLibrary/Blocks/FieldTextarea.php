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
		// if ( ! empty( $this->attributes['height'] ) ) {
		// 	$field_attributes['style'] = 'height: ' . $this->attributes['height'] . 'px;';
		// }

		return sprintf(
			'<textarea class="omniform-field-control" %s>%s</textarea>',
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
			),
			parent::getControlAttributes()
		);
		return array_filter( $attributes );
	}
}
