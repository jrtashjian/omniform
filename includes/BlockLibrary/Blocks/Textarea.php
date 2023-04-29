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
			esc_textarea( $this->get_block_attribute( 'fieldPlaceholder' ) )
		);
	}
}
