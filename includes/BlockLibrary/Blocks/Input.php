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
	public function renderControl() {
		return sprintf(
			'<input %s />',
			get_block_wrapper_attributes( $this->getExtraWrapperAttributes() )
		);
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function getExtraWrapperAttributes() {
		$extra_attributes = wp_parse_args(
			array(
				'placeholder' => $this->getBlockAttribute( 'fieldPlaceholder' ),
				'type'        => $this->getBlockAttribute( 'fieldType' ),
				'value'       => $this->getControlValue(),
			),
			parent::getExtraWrapperAttributes()
		);

		return array_filter( $extra_attributes );
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	public function getControlValue() {
		if ( $this->getBlockAttribute( 'fieldValue' ) ) {
			return $this->getBlockAttribute( 'fieldValue' );
		}

		switch ( $this->getBlockAttribute( 'fieldType' ) ) {
			// Checkboxes default to boolean. However, when transforming from a select field we want the option name to be the value.
			case 'checkbox':
				return $this->getFieldLabel();
			// Radios are always grouped so value is its name.
			case 'radio':
				return $this->getFieldLabel();
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
	public function getControlNameParts() {
		if ( in_array( $this->getBlockAttribute( 'fieldType' ), array( 'radio', 'checkbox' ), true ) && $this->isGrouped() ) {
			return array( $this->getFieldGroupName() );
		}

		return parent::getControlNameParts();
	}

	/**
	 * Gets the control's name attribute.
	 *
	 * @return string
	 */
	public function getControlName() {
		if ( 'checkbox' === $this->getBlockAttribute( 'fieldType' ) && $this->isGrouped() ) {
			return parent::getControlName() . '[]';
		}

		return parent::getControlName();
	}
}
