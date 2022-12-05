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
class Button implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return omniform()->basePath( '/build/block-library/button' );
	}

	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		$button_classes = array(
			'wp-block-omniform-button',
			'wp-block-button',
			wp_theme_get_element_class_name( 'button' ),
			$this->getColorClasses( $attributes ),
		);

		return sprintf(
			'<button type="%s" class="%s" %s>%s</button>',
			esc_attr( $attributes['buttonType'] ),
			esc_attr( implode( ' ', $button_classes ) ),
			$this->getColorStyles( $attributes ),
			wp_kses_post( $attributes['buttonLabel'] ),
		);
	}

	/**
	 * Returns color classnames depending on whether there are named or custom text and background colors.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string The color classnames to be applied to the block elements.
	 */
	protected function getColorClasses( $attributes ) {
		$classnames = array();

		// Text color.
		$has_named_text_color  = ! empty( $attributes['textColor'] );
		$has_custom_text_color = ! empty( $attributes['style']['color']['text'] );
		if ( $has_named_text_color ) {
			$classnames[] = sprintf( 'has-text-color has-%s-color', $attributes['textColor'] );
		} elseif ( $has_custom_text_color ) {
			// If a custom 'textColor' was selected instead of a preset, still add the generic `has-text-color` class.
			$classnames[] = 'has-text-color';
		}

		// Background color.
		$has_named_background_color  = ! empty( $attributes['backgroundColor'] );
		$has_custom_background_color = ! empty( $attributes['style']['color']['background'] );
		$has_named_gradient          = ! empty( $attributes['gradient'] );
		$has_custom_gradient         = ! empty( $attributes['style']['color']['gradient'] );
		if (
			$has_named_background_color ||
			$has_custom_background_color ||
			$has_named_gradient ||
			$has_custom_gradient
		) {
			$classnames[] = 'has-background';
		}
		if ( $has_named_background_color ) {
			$classnames[] = sprintf( 'has-%s-background-color', $attributes['backgroundColor'] );
		}
		if ( $has_named_gradient ) {
			$classnames[] = sprintf( 'has-%s-gradient-background', $attributes['gradient'] );
		}

		return implode( ' ', $classnames );
	}

	protected function getColorStyles( $attributes ) {
		$inline_styles = array();

		// Add color styles.
		$has_text_color = ! empty( $attributes['style']['color']['text'] );
		if ( $has_text_color ) {
			$inline_styles[] = sprintf( 'color: %s;', $attributes['style']['color']['text'] );
		}

		$has_background_color = ! empty( $attributes['style']['color']['background'] );
		if ( $has_background_color ) {
			$inline_styles[] = sprintf( 'background-color: %s;', $attributes['style']['color']['background'] );
		}

		$has_custom_gradient = ! empty( $attributes['style']['color']['gradient'] );
		if ( $has_custom_gradient ) {
			$inline_styles[] = sprintf( 'background: %s;', $attributes['style']['color']['gradient'] );
		}

		return sprintf(
			'style="%s"',
			esc_attr( safecss_filter_attr( implode( ' ', $inline_styles ) ) )
		);
	}
}
