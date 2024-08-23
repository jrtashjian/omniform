<?php
/**
 * The Hidden block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Traits\CallbackSupport;

/**
 * The Hidden block class.
 */
class Hidden extends Input {
	use CallbackSupport;

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
				'id'    => $this->get_control_name(),
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
		$value = parent::get_control_value();

		if ( $this->has_callback( $value ) ) {
			$value = $this->process_callbacks( $value );
		}

		return esc_attr( $value );
	}
}
