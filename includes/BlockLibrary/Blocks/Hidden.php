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
	use \OmniForm\Traits\CallbackSupport;

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
		$value = parent::get_control_value();

		if ( ! $this->has_callback( $value ) ) {
			return '';
		}

		return esc_attr( strval( $this->process_callbacks( $value ) ) );
	}
}
