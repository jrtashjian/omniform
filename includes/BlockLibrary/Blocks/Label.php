<?php
/**
 * Server-side renderer for the form field label block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders a label element bound to a field control.
 *
 * Reads field label, name, and required state from block context, links the
 * label via the for attribute, optionally hides it visually, and appends the
 * form's required-field indicator when the field is required.
 */
class Label extends BaseBlock {
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

		return sprintf(
			'<label for="%s" %s>%s</label>',
			esc_attr( $this->control_id( $field_label ) ),
			get_block_wrapper_attributes( $this->extra_wrapper_attributes() ),
			wp_kses( $field_label, $this->allowed_html_for_labels() ) . $this->required_indicator()
		);
	}

	/**
	 * Field label text from block context, or null when empty.
	 *
	 * @return string|null
	 */
	private function field_label(): ?string {
		$label = $this->get_block_context( 'omniform/fieldLabel' );

		return empty( $label ) ? null : (string) $label;
	}

	/**
	 * Sanitized control id for the label's for attribute.
	 *
	 * @param string $field_label Fallback when fieldName context is absent.
	 *
	 * @return string
	 */
	private function control_id( string $field_label ): string {
		return $this->sanitize_field_name(
			(string) ( $this->get_block_context( 'omniform/fieldName' ) ?? $field_label )
		);
	}

	/**
	 * Extra attributes for get_block_wrapper_attributes().
	 *
	 * @return array<string, string>
	 */
	private function extra_wrapper_attributes(): array {
		return array_filter(
			array(
				'class' => $this->get_block_attribute( 'isHidden' ) ? 'screen-reader-text' : null,
			)
		);
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

		$required_label = omniform()->container()->get( \OmniForm\Plugin\Form::class )->get_required_label();

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
	 * Whether the associated field is required.
	 *
	 * @return bool
	 */
	private function is_required(): bool {
		return (bool) $this->get_block_context( 'omniform/fieldIsRequired' );
	}
}
