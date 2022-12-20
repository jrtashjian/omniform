<?php
/**
 * The FieldSelect block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The FieldSelect block class.
 */
class FieldSelect extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function renderControl() {
		$placeholder_option = '';
		if ( ! empty( $this->attributes['fieldPlaceholder'] ) ) {
			$placeholder_option = sprintf(
				'<option value="">%s</option>',
				esc_attr( $this->attributes['fieldPlaceholder'] )
			);
		}

		return sprintf(
			'<select class="omniform-field-control" %s>%s</select>',
			trim( implode( ' ', $this->getControlAttributes() ) ),
			$placeholder_option . $this->content
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
				$this->getBlockAttribute( 'isMultiple' ) ? 'multiple' : '',
			),
			parent::getControlAttributes()
		);
		return array_filter( $attributes );
	}

	/**
	 * The form control's name attribute.
	 *
	 * @return string
	 */
	protected function getControlName() {
		$name = parent::getControlName();

		return $this->getBlockAttribute( 'isMultiple' )
			? $name . '[]'
			: $name;
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	protected function getControlValue() {
		// Select form fields don't use the "value" attribute.
		return '';
	}
}
