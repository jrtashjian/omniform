<?php
/**
 * Abstract base for form control block renderers.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;
use OmniForm\Form\ControlName;
use OmniForm\Form\FieldName;
use OmniForm\Form\FieldPath;
use OmniForm\Traits\CallbackSupport;

/**
 * Shared foundation for form control blocks.
 *
 * Resolves field identity from block context (label, name, group, path),
 * composes HTML names via ControlName, applies callback-aware values, and
 * supplies validation rules. Concrete controls implement render_control().
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
	 * Gets the sanitized control name, falling back to the field label.
	 *
	 * @return string
	 */
	public function get_field_name(): string {
		try {
			return $this->control_field_name()->value();
		} catch ( \InvalidArgumentException $_exception ) {
			return '';
		}
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
	 * Name segments used to build the control's HTML name attribute.
	 *
	 * @return list<string>
	 */
	public function get_control_name_parts(): array {
		return $this->composed_name()->segments();
	}

	/**
	 * The control's HTML name attribute (e.g. group[field]).
	 *
	 * @return string
	 */
	public function get_control_name(): string {
		return $this->composed_name()->html_name(
			ControlName::is_multiple(
				$this->control_type(),
				$this->is_choice_group(),
				$this->control_name_is_multiple()
			)
		);
	}

	/**
	 * Composed field path for this control (path prefix + control name + flags).
	 */
	public function composed_name(): FieldPath {
		return ControlName::compose(
			$this->field_path_prefix(),
			$this->control_field_name(),
			$this->control_type(),
			$this->is_choice_group()
		);
	}

	/**
	 * Single-segment control name from fieldName or fieldLabel.
	 */
	protected function control_field_name(): FieldName {
		$name_context = $this->get_block_context( 'omniform/fieldName' );

		return FieldName::from_name_or_label(
			null === $name_context ? null : (string) $name_context,
			(string) ( $this->get_field_label() ?? '' )
		);
	}

	/**
	 * Fieldset path prefix from render context.
	 */
	protected function field_path_prefix(): FieldPath {
		$field_path = (string) ( $this->get_block_context( 'omniform/fieldPath' ) ?? '' );

		if ( '' === $field_path ) {
			return FieldPath::empty();
		}

		return FieldPath::from_segments( explode( '.', $field_path ) );
	}

	/**
	 * Control type used for composition (text, radio, select, …).
	 */
	protected function control_type(): string {
		return 'text';
	}

	/**
	 * Whether the control requests a multi-value HTML name (e.g. select multiple).
	 */
	protected function control_name_is_multiple(): bool {
		return false;
	}

	/**
	 * Whether the parent fieldset is a radio/checkbox choice group.
	 */
	protected function is_choice_group(): bool {
		return (bool) $this->get_block_context( 'omniform/isChoiceGroup' );
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
