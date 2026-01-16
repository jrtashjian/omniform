<?php
/**
 * The Button block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Button block class.
 */
class Button extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( empty( $this->get_block_attribute( 'buttonLabel' ) ) ) {
			return '';
		}

		return sprintf(
			'<button %s>%s</button>',
			get_block_wrapper_attributes(
				array(
					'class' => wp_theme_get_element_class_name( 'button' ),
					'type'  => esc_attr( $this->get_block_attribute( 'buttonType' ) ?? 'button' ),
				)
			),
			wp_kses( $this->get_block_attribute( 'buttonLabel' ), $this->allowed_html_for_labels )
		);
	}
}
