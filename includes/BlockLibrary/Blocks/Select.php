<?php
/**
 * The Select block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Select block class.
 */
class Select extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		$placeholder_option = '';

		if ( ! $this->getBlockAttribute( 'isMultiple' ) ) {
			$placeholder_option = sprintf(
				'<option value="">%s</option>',
				esc_attr( $this->getBlockAttribute( 'fieldPlaceholder' ) )
			);
		}

		$extra_attributes = array_filter(
			array(
				'id'       => sanitize_title( $this->getBlockContext( 'omniform/fieldName' ) ),
				'name'     => sanitize_title( $this->getBlockContext( 'omniform/fieldName' ) ),
				'multiple' => $this->getBlockAttribute( 'isMultiple' ),
			)
		);

		// Detect minHeight attribute so we can use it to set the height of the select element.
		$attributes = \WP_Block_Supports::get_instance()->apply_block_supports();

		if ( ! empty( $attributes ) && array_key_exists( 'style', $attributes ) ) {
			if ( $this->getBlockAttribute( 'isMultiple' ) && preg_match( '/min-height:([^;]+);/', $attributes['style'], $matches ) ) {
				$extra_attributes['style'] = esc_attr( sprintf( 'height: %s;', $matches[1] ) );
			}
		}

		// Remove minHeight attribute so it doesn't get added to the wrapper element if not a multiple select.
		$block_attributes = get_block_wrapper_attributes( $extra_attributes );
		if ( ! $this->getBlockAttribute( 'isMultiple' ) ) {
			$block_attributes = preg_replace( '/min-height:([^;]+);/', '', $block_attributes );
		}

		return sprintf(
			'<select %s>%s</select>',
			$block_attributes,
			$placeholder_option . $this->content
		);
	}
}
