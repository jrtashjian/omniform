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
	 * @return string Returns the block content.
	 */
	public function render() {
		$button_classes = array_merge(
			array(
				wp_theme_get_element_class_name( 'button' ),
				'wp-block-button__link',
			),
			$this->getColorClasses( $this->attributes ),
		);

		$allowed_html = array(
			'strong' => array(),
			'em'     => array(),
		);

		return sprintf(
			'<div class="wp-block-omniform-button wp-block-button"><button type="%s" %s %s>%s</button></div>',
			esc_attr( $this->attributes['buttonType'] ),
			$this->getElementAttribute( 'class', $button_classes ),
			$this->getElementAttribute( 'style', $this->getColorStyles( $this->attributes ) ),
			wp_kses( $this->attributes['buttonLabel'], $allowed_html ),
		);
	}
}
