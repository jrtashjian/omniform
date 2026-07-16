<?php
/**
 * Server-side renderer for the field container block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders a field wrapper around label and control inner blocks.
 *
 * Returns empty markup when the field has no field label attribute. Applies a
 * Core layout class workaround until the upstream flex layout bug is fixed.
 */
class Field extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render(): string {
		if ( null === $this->field_label() ) {
			return '';
		}

		return sprintf(
			'<div %s>%s</div>',
			get_block_wrapper_attributes( $this->extra_wrapper_attributes() ),
			$this->content
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
	 * Extra attributes for get_block_wrapper_attributes().
	 *
	 * @return array<string, string>
	 */
	private function extra_wrapper_attributes(): array {
		return array(
			// Bug in Core: https://github.com/WordPress/WordPress/blob/b9bdf794320b132e6a4ce1538e988e6d31be33b0/wp-includes/block-supports/layout.php#L817-L819/.
			'class' => 'wp-block-omniform-field-is-layout-flex',
		);
	}
}
