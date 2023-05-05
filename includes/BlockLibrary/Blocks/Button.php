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

		$allowed_html = array(
			'strong' => array(),
			'em'     => array(),
			'img'    => array(
				'class' => true,
				'style' => true,
				'src'   => true,
				'alt'   => true,
			),
		);

		$classname = implode(
			' ',
			array(
				wp_theme_get_element_class_name( 'button' ),
				'wp-block-button__link',
			)
		);

		return sprintf(
			'<button %s>%s</button>',
			get_block_wrapper_attributes(
				array(
					'class' => $classname,
					'type'  => esc_attr( $this->get_block_attribute( 'buttonType' ) ),
				)
			),
			wp_kses( $this->get_block_attribute( 'buttonLabel' ), $allowed_html )
		);
	}
}
