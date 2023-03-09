<?php
/**
 * The FieldInput block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The FieldInput block class.
 */
class FieldInput extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( $this->isHiddenInput() ) {
			return sprintf(
				'<input type="hidden" %s />',
				trim( implode( ' ', $this->getControlAttributes() ) )
			);
		}

		return parent::render();
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function renderControl() {
		return sprintf(
			'<input class="omniform-field-control" type="%s" %s />',
			esc_attr( $this->getBlockAttribute( 'fieldType' ) ),
			trim( implode( ' ', $this->getControlAttributes() ) )
		);
	}

	/**
	 * Determine if the field type is a text input.
	 *
	 * @return bool
	 */
	protected function isTextInput() {
		return in_array(
			$this->getBlockAttribute( 'fieldType' ),
			array( 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' ),
			true
		);
	}

	/**
	 * Determine if the field type is a checbox or radio.
	 *
	 * @return bool
	 */
	protected function isCheckedInput() {
		return in_array(
			$this->getBlockAttribute( 'fieldType' ),
			array( 'checkbox', 'radio' ),
			true
		);
	}

	/**
	 * Determine if the field type is a hidden input.
	 *
	 * @return bool
	 */
	protected function isHiddenInput() {
		return 'hidden' === $this->getBlockAttribute( 'fieldType' );
	}

	/**
	 * The form control's name attribute.
	 *
	 * @return string
	 */
	public function getControlName() {
		$name = parent::getControlName();

		if ( 'radio' === $this->getBlockAttribute( 'fieldType' ) && $this->isGrouped() ) {
			$name = $this->getBlockContext( 'omniform/fieldGroupName' );
		}

		return $name;
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	public function getControlValue() {
		switch ( $this->getBlockAttribute( 'fieldType' ) ) {
			// Checkboxes default to boolean. However, when transforming from a select field we want the option name to be the value.
			case 'checkbox':
				return parent::getControlValue() ?? true;
			// Radios are always grouped so value is its name.
			case 'radio':
				return $this->getFieldName();
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
			case 'hidden':
				return $this->valueFromCallback();
			default:
				return parent::getControlValue();
		}
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
				$this->getElementAttribute( 'value', $this->getControlValue() ),
			),
			parent::getControlAttributes()
		);
		return array_filter( $attributes );
	}

	/**
	 * Get the calculated value from a callback function.
	 *
	 * @return string
	 */
	protected function valueFromCallback() {
		$callback = $this->getBlockAttribute( 'fieldValue' );

		if ( ! $callback ) {
			return '';
		}

		// Callback functions must be surrounded by curly brackets. Example: "{{ callback_function }}".
		if ( false === strpos( $callback, '{{' ) ) {
			// Allow non-callback values to be set.
			return $callback;
		}

		$fn = trim( str_replace( array( '{', '}' ), '', $callback ) );

		// Ensure the function exists before calling.
		if ( ! function_exists( $fn ) ) {
			return '';
		}

		$result = $fn();

		// Return an empty string if a string result was not received from the callback function.
		if ( is_array( $result ) || is_object( $result ) ) {
			return '';
		}

		return strval( is_bool( $result ) ? intval( $result ) : $result );
	}
}
