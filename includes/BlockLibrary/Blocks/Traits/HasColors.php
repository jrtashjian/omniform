<?php
/**
 * The BaseBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks\Traits;

/**
 * The BaseBlock block class.
 */
trait HasColors {
	/**
	 * Returns color classnames depending on whether there are named or custom text and background colors.
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/87b21a557661ff53fd93ea112ae7e70e65da3382/packages/block-library/src/search/index.php#L506-L547
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

		return $classnames;
	}

	/**
	 * Builds an array of inline styles.
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/87b21a557661ff53fd93ea112ae7e70e65da3382/packages/block-library/src/search/index.php#L367-L381
	 *
	 * @param  array $attributes The block attributes.
	 *
	 * @return array Style HTML attribute.
	 */
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

		return array_map( 'safecss_filter_attr', $inline_styles );
	}
}
