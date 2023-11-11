<?php
/**
 * The BaseControlBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;

/**
 * The BaseControlBlock block class.
 */
abstract class BaseControlBlock extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		return $this->get_field_label() ? $this->render_control() : '';
	}

	/**
	 * Gets the field label.
	 *
	 * @return string|null
	 */
	public function get_field_label() {
		return $this->get_block_context( 'omniform/fieldLabel' );
	}

	/**
	 * Gets the field name (sanitized).
	 *
	 * @return string|null
	 */
	public function get_field_name() {
		return sanitize_title( $this->get_block_context( 'omniform/fieldName' ) ?? $this->get_field_label() ?? '' );
	}

	/**
	 * Gets the field group label.
	 *
	 * @return string|null
	 */
	public function get_field_group_label() {
		return $this->get_block_context( 'omniform/fieldGroupLabel' );
	}

	/**
	 * Gets the field group name (sanitized).
	 *
	 * @return string|null
	 */
	public function get_field_group_name() {
		return sanitize_title( $this->get_block_context( 'omniform/fieldGroupName' ) ?? $this->get_field_group_label() ?? '' );
	}

	/**
	 * Is the field grouped?
	 *
	 * @return bool
	 */
	public function is_grouped() {
		return ! empty( $this->get_field_group_name() );
	}

	/**
	 * Is the field required?
	 *
	 * @return bool
	 */
	public function is_required() {
		return $this->get_block_context( 'omniform/fieldGroupIsRequired' ) ?? $this->get_block_context( 'omniform/fieldIsRequired' ) ?? false;
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function get_extra_wrapper_attributes() {
		$new_attributes = \WP_Block_Supports::get_instance()->apply_block_supports();
		// Check if the block has a custom border. If it does, we don't want to hide it on focus.
		$has_custom_border = key_exists( 'style', $new_attributes ) && strpos( $new_attributes['style'], 'border-width' ) !== false;

		return array_filter(
			array(
				'id'       => $this->get_field_name(),
				'name'     => $this->get_control_name(),
				'required' => $this->is_required(),
				'class'    => $has_custom_border ? 'has-custom-border' : '',
			)
		);
	}

	/**
	 * Gets the control's name parts.
	 *
	 * @return array
	 */
	public function get_control_name_parts() {
		return array_values(
			array_filter(
				array(
					$this->get_field_group_name(),
					$this->get_field_name(),
				)
			)
		);
	}

	/**
	 * Gets the control's name attribute.
	 *
	 * @return string
	 */
	public function get_control_name() {
		$parts = $this->get_control_name_parts();

		return 2 === count( $parts )
			? sprintf( '%s[%s]', $parts[0], $parts[1] )
			: $parts[0];
	}

	/**
	 * Get the validation rules for the field.
	 *
	 * @return array
	 */
	public function get_validation_rules() {
		return array_filter(
			array(
				$this->is_required() ? new Validation\Rules\NotEmpty() : null,
			)
		);
	}

	/**
	 * Does the field have validation rules?
	 *
	 * @return bool
	 */
	public function has_validation_rules() {
		return ! empty( $this->get_validation_rules() );
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	abstract public function render_control();
}
