<?php
/**
 * Server-side renderer for the fieldset block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders a fieldset with legend for grouped form controls.
 *
 * Returns empty markup when the group has no field label. Appends the form's
 * required-field indicator when the fieldset is required, and exposes group
 * label/name for nested controls.
 */
class Fieldset extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render(): string {
		$field_label = $this->field_label();
		if ( null === $field_label ) {
			return '';
		}

		$legend = wp_kses( $field_label, $this->allowed_html_for_labels() ) . $this->required_indicator();

		return sprintf(
			'<fieldset %1$s><legend>%2$s</legend><div class="omniform-field-label" aria-hidden="true">%2$s</div>%3$s</fieldset>',
			get_block_wrapper_attributes(),
			$legend,
			$this->content
		);
	}

	/**
	 * Field group label from block attributes.
	 *
	 * @return string|null
	 */
	public function get_field_group_label(): ?string {
		$label = $this->get_block_attribute( 'fieldLabel' );

		return null === $label ? null : (string) $label;
	}

	/**
	 * Sanitized field group name, falling back to the group label.
	 *
	 * @return string
	 */
	public function get_field_group_name(): string {
		return $this->sanitize_field_name(
			(string) ( $this->get_block_attribute( 'fieldName' ) ?? $this->get_field_group_label() ?? '' )
		);
	}

	/**
	 * Field label from block attributes, or null when empty.
	 *
	 * @return string|null
	 */
	private function field_label(): ?string {
		$label = $this->get_block_attribute( 'fieldLabel' );

		return empty( $label ) ? null : (string) $label;
	}

	/**
	 * Required-field indicator markup, or empty when not required.
	 *
	 * @return string
	 */
	private function required_indicator(): string {
		if ( ! $this->is_required() ) {
			return '';
		}

		$required_label = omniform()->container()->get( \OmniForm\Plugin\FormRenderContext::class )->required_label();

		return match ( $required_label ) {
			'*' => sprintf(
				'<abbr class="omniform-field-required" title="%s">*</abbr>',
				esc_attr__( 'required', 'omniform' )
			),
			default => sprintf(
				'<span class="omniform-field-required">%s</span>',
				wp_kses( $required_label, $this->allowed_html_for_labels() )
			),
		};
	}

	/**
	 * Whether the fieldset is required.
	 *
	 * @return bool
	 */
	private function is_required(): bool {
		return (bool) $this->get_block_attribute( 'isRequired' );
	}
}
