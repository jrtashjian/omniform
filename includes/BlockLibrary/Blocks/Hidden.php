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
	public function get_field_label(): ?string {
		$name = $this->get_block_attribute( 'fieldName' );

		return null === $name ? null : (string) $name;
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array<string, mixed>
	 */
	public function get_extra_wrapper_attributes(): array {
		return array_filter(
			array(
				'type'  => 'hidden',
				'id'    => $this->get_control_name(),
				'name'  => $this->get_control_name(),
				'value' => $this->get_control_value(),
			)
		);
	}
}
