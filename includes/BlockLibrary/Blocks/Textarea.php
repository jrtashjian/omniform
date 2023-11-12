<?php
/**
 * The Textarea block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Textarea block class.
 */
class Textarea extends BaseControlBlock {
	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function render_control() {
		return sprintf(
			'<textarea %s>%s</textarea>',
			get_block_wrapper_attributes( $this->get_extra_wrapper_attributes() ),
			esc_textarea( $this->get_block_attribute( 'fieldValue' ) ?? '' )
		);
	}

	/**
	 * Gets the extra wrapper attributes for the field to be passed into get_block_wrapper_attributes().
	 *
	 * @return array
	 */
	public function get_extra_wrapper_attributes() {
		$extra_attributes = wp_parse_args(
			array(
				'placeholder' => $this->get_block_attribute( 'fieldPlaceholder' ),
				'aria-label'  => esc_attr( wp_strip_all_tags( $this->get_field_label() ) ),
			),
			parent::get_extra_wrapper_attributes()
		);

		return array_filter( $extra_attributes );
	}
}
