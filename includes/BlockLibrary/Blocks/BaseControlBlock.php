<?php
/**
 * Abstract base for form control block renderers.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;
use OmniForm\Form\Path;
use OmniForm\Traits\CallbackSupport;

/**
 * Shared foundation for form control blocks.
 *
 * Resolves field identity from block context (label, name, group, path),
 * builds HTML control attributes and Path-based name segments, applies
 * callback-aware values, and supplies validation rules. Concrete controls
 * implement render_control().
 */
abstract class BaseControlBlock extends BaseBlock {
	use CallbackSupport;

	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render(): string {
		return $this->get_field_label() ? $this->render_control() : '';
	}

	/**
	 * Gets the field label from block context.
	 *
	 * @return string|null
	 */
	public function get_field_label(): ?string {
		$label = $this->get_block_context( 'omniform/fieldLabel' );

		return null === $label ? null : (string) $label;
	}

	/**
	 * Gets the sanitized field name, falling back to the field label.
	 *
	 * @return string
	 */
	public function get_field_name(): string {
		return $this->sanitize_field_name(
			(string) ( $this->get_block_context( 'omniform/fieldName' ) ?? $this->get_field_label() ?? '' )
		);
	}

	/**
	 * Gets the field group label from block context.
	 *
	 * @return string|null
	 */
	public function get_field_group_label(): ?string {
		$label = $this->get_block_context( 'omniform/fieldGroupLabel' );

		return null === $label ? null : (string) $label;
	}

	/**
	 * Gets the sanitized field group name, falling back to the group label.
	 *
	 * @return string
	 */
	public function get_field_group_name(): string {
		return $this->sanitize_field_name(
			(string) ( $this->get_block_context( 'omniform/fieldGroupName' ) ?? $this->get_field_group_label() ?? '' )
		);
	}

	/**
	 * Whether the field belongs to a named group.
	 *
	 * @return bool
	 */
	public function is_grouped(): bool {
		return '' !== $this->get_field_group_name();
	}

	/**
	 * Whether the field (or its group) is required.
	 *
	 * Group-level required takes precedence over field-level required.
	 *
	 * @return bool
	 */
	public function is_required(): bool {
		return (bool) (
			$this->get_block_context( 'omniform/fieldGroupIsRequired' )
			?? $this->get_block_context( 'omniform/fieldIsRequired' )
			?? false
		);
	}

	/**
	 * Extra attributes for get_block_wrapper_attributes().
	 *
	 * @return array<string, mixed>
	 */
	public function get_extra_wrapper_attributes(): array {
		return array_filter(
			array(
				'id'       => $this->get_field_name(),
				'name'     => $this->get_control_name(),
				'value'    => $this->get_control_value(),
				'required' => $this->is_required(),
				'class'    => $this->has_custom_border() ? 'has-custom-border' : '',
			)
		);
	}

	/**
	 * Path segments used to build the control's HTML name attribute.
	 *
	 * @return list<string>
	 */
	public function get_control_name_parts(): array {
		$field_path = (string) ( $this->get_block_context( 'omniform/fieldPath' ) ?? '' );
		$path_parts = '' === $field_path ? array() : explode( '.', $field_path );

		return array_values(
			array_filter(
				array(
					...$path_parts,
					$this->get_field_name(),
				),
				static fn( string $part ): bool => '' !== $part
			)
		);
	}

	/**
	 * The control's HTML name attribute (e.g. group[field]).
	 *
	 * @return string
	 */
	public function get_control_name(): string {
		return Path::from_segments( $this->get_control_name_parts() )->html_name();
	}

	/**
	 * The form control's value attribute, with callback placeholders resolved.
	 *
	 * @return string
	 */
	public function get_control_value(): string {
		$value = (string) ( $this->get_block_attribute( 'fieldValue' ) ?? '' );

		return $this->has_callback( $value )
			? $this->process_callbacks( $value )
			: $value;
	}

	/**
	 * Validation rules for the field.
	 *
	 * @return list<object>
	 */
	public function get_validation_rules(): array {
		return array_values(
			array_filter(
				array(
					$this->is_required() ? new Validation\Rules\NotEmpty() : null,
				)
			)
		);
	}

	/**
	 * Whether the field has any validation rules.
	 *
	 * @return bool
	 */
	public function has_validation_rules(): bool {
		return array() !== $this->get_validation_rules();
	}

	/**
	 * Renders the form control markup.
	 *
	 * @return string
	 */
	abstract public function render_control(): string;

	/**
	 * Whether block supports include a custom border width.
	 *
	 * When present, focus styles should not hide the border.
	 *
	 * @return bool
	 */
	private function has_custom_border(): bool {
		$attributes = \WP_Block_Supports::get_instance()->apply_block_supports();
		$style      = $attributes['style'] ?? null;

		return is_string( $style ) && str_contains( $style, 'border-width' );
	}
}
