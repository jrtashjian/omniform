<?php
/**
 * The Hidden block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Hidden block class.
 */
class Hidden extends Input {
	/**
	 * Gets the field label.
	 *
	 * @return string|null
	 */
	public function get_field_label() {
		return $this->get_block_attribute( 'fieldName' );
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function get_extra_wrapper_attributes() {
		return array_filter(
			array(
				'type'  => 'hidden',
				'name'  => $this->get_control_name(),
				'value' => $this->get_control_value(),
			)
		);
	}

	/**
	 * The form control's value attribute.
	 *
	 * @return string
	 */
	public function get_control_value() {
		$callback = $this->get_block_attribute( 'fieldValue' );

		if ( ! $callback ) {
			return '';
		}

		// Callback functions must be surrounded by curly brackets. Example: "{{ callback_function }}".
		if ( false === strpos( $callback, '{{' ) ) {
			// Allow non-callback values to be set.
			return esc_attr( $callback );
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

		return esc_attr( strval( is_bool( $result ) ? intval( $result ) : $result ) );
	}
}
