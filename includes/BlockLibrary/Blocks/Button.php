<?php
/**
 * The Button block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;

/**
 * The Button block class.
 */
class Button extends BaseBlock {
	use HasColors;

	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) {
		parent::renderBlock( $attributes, $content, $block );

		$button_classes = array(
			wp_theme_get_element_class_name( 'button' ),
			'wp-block-button__link',
			$this->getColorClasses( $attributes ),
		);

		return sprintf(
			'<div class="wp-block-omniform-button wp-block-button"><button type="%s" class="%s" %s>%s</button></div>',
			esc_attr( $attributes['buttonType'] ),
			esc_attr( implode( ' ', $button_classes ) ),
			$this->getColorStyles( $attributes ),
			wp_kses_post( $attributes['buttonLabel'] ),
		);
	}
}
