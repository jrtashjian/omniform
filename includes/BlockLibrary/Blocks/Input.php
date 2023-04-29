<?php
/**
 * The Input block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Input block class.
 */
class Input extends BaseControlBlock {
	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function render_control() {
		return sprintf(
			'<input %s />',
			get_block_wrapper_attributes( $this->get_extra_wrapper_attributes() )
		);
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function get_extra_wrapper_attributes() {
		$extra_attributes = wp_parse_args(
			array(
				'placeholder' => $this->get_block_attribute( 'fieldPlaceholder' ),
				'type'        => $this->get_block_attribute( 'fieldType' ),
				'value'       => $this->get_control_value(),
				'aria-label'  => esc_attr( wp_strip_all_tags( $this->get_field_label() ) ),
			),
			parent::get_extra_wrapper_attributes()
		);

		return array_filter( $extra_attributes );
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	public function get_control_value() {
		if ( $this->get_block_attribute( 'fieldValue' ) ) {
			return $this->get_block_attribute( 'fieldValue' );
		}

		switch ( $this->get_block_attribute( 'fieldType' ) ) {
			// Checkboxes default to boolean. However, when transforming from a select field we want the option name to be the value.
			case 'checkbox':
				return $this->get_field_label();
			// Radios are always grouped so value is its name.
			case 'radio':
				return $this->get_field_label();
			// Date and time inputs need a default vaule to display properly on iOS.
			case 'date':
				return gmdate( 'Y-m-d' );
			case 'time':
				return gmdate( 'h:i:00' );
			case 'month':
				return gmdate( 'Y-m' );
			case 'week':
				return gmdate( 'Y-\WW' );
			case 'datetime-local':
				return gmdate( 'Y-m-d H:i:00' );
			default:
				return '';
		}
	}

	/**
	 * Gets the control's name parts.
	 *
	 * @return array
	 */
	public function get_control_name_parts() {
		if ( in_array( $this->get_block_attribute( 'fieldType' ), array( 'radio', 'checkbox' ), true ) && $this->is_grouped() ) {
			return array( $this->get_field_group_name() );
		}

		return parent::get_control_name_parts();
	}

	/**
	 * Gets the control's name attribute.
	 *
	 * @return string
	 */
	public function get_control_name() {
		if ( 'checkbox' === $this->get_block_attribute( 'fieldType' ) && $this->is_grouped() ) {
			return parent::get_control_name() . '[]';
		}

		return parent::get_control_name();
	}
}
