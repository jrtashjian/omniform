<?php
/**
 * Server-side renderer for the form button block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders a button element for form actions.
 *
 * Returns empty markup when the button has no label. Applies the theme button
 * element class and defaults the type attribute to "button".
 */
class Button extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render(): string {
		$button_label = $this->button_label();
		if ( null === $button_label ) {
			return '';
		}

		return sprintf(
			'<button %s>%s</button>',
			get_block_wrapper_attributes( $this->extra_wrapper_attributes() ),
			wp_kses( $button_label, $this->allowed_html_for_labels() )
		);
	}

	/**
	 * Button label from block attributes, or null when empty.
	 *
	 * @return string|null
	 */
	private function button_label(): ?string {
		$label = $this->get_block_attribute( 'buttonLabel' );

		return empty( $label ) ? null : (string) $label;
	}

	/**
	 * Extra attributes for get_block_wrapper_attributes().
	 *
	 * @return array<string, string>
	 */
	private function extra_wrapper_attributes(): array {
		return array(
			'class' => wp_theme_get_element_class_name( 'button' ),
			'type'  => esc_attr( $this->button_type() ),
		);
	}

	/**
	 * Button type attribute, defaulting to "button".
	 *
	 * @return string
	 */
	private function button_type(): string {
		return (string) ( $this->get_block_attribute( 'buttonType' ) ?? 'button' );
	}
}
