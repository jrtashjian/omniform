<?php
/**
 * The ButtonSubmit block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The ButtonSubmit block class.
 */
class ButtonSubmit implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return omniform()->basePath( '/build/block-library/button-submit' );
	}

	/**
	 * Renders the block on the server.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block default content.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		$button_classes = array(
			'wp-block-omniform-button-submit',
			'wp-block-button',
			wp_theme_get_element_class_name( 'button' ),
		);

		return sprintf(
			'<button type="submit" class="%s">%s</button>',
			esc_attr( implode( ' ', $button_classes ) ),
			wp_kses_post( $attributes['text'] )
		);
	}
}
