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
class Select extends BaseControlBlock {
	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function renderControl() {
		// Remove minHeight attribute so it doesn't get added to the wrapper element if not a multiple select.
		$block_attributes = get_block_wrapper_attributes( $this->getExtraWrapperAttributes() );

		if ( ! $this->getBlockAttribute( 'isMultiple' ) ) {
			$block_attributes = preg_replace( '/min-height:([^;]+);/', '', $block_attributes );
		}

		$placeholder = '';
		if ( $this->getBlockAttribute( 'fieldPlaceholder' ) ) {
			$placeholder = sprintf(
				'<option value="">%s</option>',
				esc_attr( $this->getBlockAttribute( 'fieldPlaceholder' ) ),
			);
		}

		return sprintf(
			'<select %s>%s</select>',
			$block_attributes,
			$placeholder . $this->content
		);
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function getExtraWrapperAttributes() {
		$extra_attributes = wp_parse_args(
			array(
				'multiple' => $this->getBlockAttribute( 'isMultiple' ),
				'style'    => $this->getElementHeight(),
			),
			parent::getExtraWrapperAttributes()
		);

		return array_filter( $extra_attributes );
	}

	/**
	 * Gets the control's name attribute.
	 *
	 * @return string
	 */
	public function getControlName() {
		return $this->getBlockAttribute( 'isMultiple' )
			? parent::getControlName() . '[]'
			: parent::getControlName();
	}

	/**
	 * Gets the height of the select element (derived from the minHeight attribute).
	 *
	 * @return string|false
	 */
	private function getElementHeight() {
		$attributes = \WP_Block_Supports::get_instance()->apply_block_supports();

		if ( ! empty( $attributes ) && array_key_exists( 'style', $attributes ) ) {
			if ( $this->getBlockAttribute( 'isMultiple' ) && preg_match( '/min-height:([^;]+);/', $attributes['style'], $matches ) ) {
				return sprintf( 'height: %s;', $matches[1] );
			}
		}

		return false;
	}
}
