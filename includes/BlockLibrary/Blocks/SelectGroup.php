<?php
/**
 * Server-side renderer for the select option group block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders an optgroup wrapping select option inner blocks.
 *
 * Returns empty markup when the group has no field label attribute.
 */
class SelectGroup extends BaseBlock {
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
			'<optgroup label="%s">%s</optgroup>',
			esc_attr( $field_label ),
			$this->content
		);
	}

	/**
	 * Group label from block attributes, or null when empty.
	 *
	 * @return string|null
	 */
	private function field_label(): ?string {
		$label = $this->get_block_attribute( 'fieldLabel' );

		return empty( $label ) ? null : (string) $label;
	}
}
