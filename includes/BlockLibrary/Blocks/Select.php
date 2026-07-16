<?php
/**
 * The Select block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Form\Path;

/**
 * The Select block class.
 */
class Select extends BaseControlBlock {
	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function render_control(): string {
		// Remove minHeight attribute so it doesn't get added to the wrapper element if not a multiple select.
		$block_attributes = get_block_wrapper_attributes( $this->get_extra_wrapper_attributes() );

		if ( ! $this->get_block_attribute( 'isMultiple' ) ) {
			$block_attributes = preg_replace( '/min-height:([^;]+);/', '', $block_attributes );
		}

		$placeholder = '';
		if ( $this->get_block_attribute( 'fieldPlaceholder' ) ) {
			$placeholder = sprintf(
				'<option value="">%s</option>',
				esc_attr( $this->get_block_attribute( 'fieldPlaceholder' ) ),
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
	 * @return array<string, mixed>
	 */
	public function get_extra_wrapper_attributes(): array {
		$extra_attributes = wp_parse_args(
			array(
				'multiple' => $this->get_block_attribute( 'isMultiple' ),
				'style'    => $this->get_element_height(),
			),
			parent::get_extra_wrapper_attributes()
		);

		return array_filter( $extra_attributes );
	}

	/**
	 * Gets the control's name attribute.
	 */
	public function get_control_name(): string {
		return Path::from_segments( $this->get_control_name_parts() )
			->html_name( (bool) $this->get_block_attribute( 'isMultiple' ) );
	}

	/**
	 * Gets the height of the select element (derived from the minHeight attribute).
	 *
	 * @return string|false
	 */
	private function get_element_height() {
		$attributes = \WP_Block_Supports::get_instance()->apply_block_supports();

		if ( ! empty( $attributes ) && array_key_exists( 'style', $attributes ) ) {
			if ( $this->get_block_attribute( 'isMultiple' ) && preg_match( '/min-height:([^;]+);/', $attributes['style'], $matches ) ) {
				return sprintf( 'height: %s;', $matches[1] );
			}
		}

		return false;
	}
}
