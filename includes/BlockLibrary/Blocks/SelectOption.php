<?php
/**
 * Server-side renderer for the select option block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders a single option element for a select control.
 *
 * Uses the field label attribute for both the option value and display text.
 * Returns empty markup when the label is missing.
 */
class SelectOption extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render(): string {
		$field_label = $this->field_label();
		if ( null === $field_label ) {
			return '';
		}

		return sprintf(
			'<option value="%s">%s</option>',
			esc_attr( $field_label ),
			esc_attr( $field_label ),
		);
	}

	/**
	 * Option label from block attributes, or null when empty.
	 *
	 * @return string|null
	 */
	private function field_label(): ?string {
		$label = $this->get_block_attribute( 'fieldLabel' );

		return empty( $label ) ? null : (string) $label;
	}
}
