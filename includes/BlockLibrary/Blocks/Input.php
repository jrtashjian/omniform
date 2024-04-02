<?php
/**
 * The Input block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;

/**
 * The Input block class.
 */
class Input extends BaseControlBlock {
	const FORMAT_DATE           = 'Y-m-d';
	const FORMAT_TIME           = 'h:i:s';
	const FORMAT_MONTH          = 'Y-m';
	const FORMAT_WEEK           = 'Y-\WW';
	const FORMAT_DATETIME_LOCAL = 'Y-m-d H:i:s';

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
				return gmdate( self::FORMAT_DATE );
			case 'time':
				return gmdate( self::FORMAT_TIME );
			case 'month':
				return gmdate( self::FORMAT_MONTH );
			case 'week':
				return gmdate( self::FORMAT_WEEK );
			case 'datetime-local':
				return gmdate( self::FORMAT_DATETIME_LOCAL );
			default:
				return '';
		}
	}

	/**
	 * Is the field required?
	 *
	 * @return bool
	 */
	public function is_required() {
		return ! in_array( $this->get_block_attribute( 'fieldType' ), array( 'radio', 'checkbox' ), true ) && parent::is_required();
	}

	/**
	 * Gets the validation rules for the field.
	 *
	 * @return array
	 */
	public function get_validation_rules() {
		$rules = parent::get_validation_rules();

		$validation_mapping = array(
			'email'  => new Validation\Rules\Email(),
			'url'    => new Validation\Rules\Url(),
			'tel'    => new Validation\Rules\Phone(),
			'number' => new Validation\Rules\Number(),
			'date'   => new Validation\Rules\Date( self::FORMAT_DATE ),
			'time'   => new Validation\Rules\Time( self::FORMAT_TIME ),
			'month'  => new Validation\Rules\Date( self::FORMAT_MONTH ),
		);

		if ( isset( $validation_mapping[ $this->get_block_attribute( 'fieldType' ) ] ) ) {
			$rule    = $validation_mapping[ $this->get_block_attribute( 'fieldType' ) ];
			$rules[] = $this->is_required()
				? $rule
				: new Validation\Rules\Optional( $rule );
		}

		return $rules;
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
