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
	public function renderControl() {
		return sprintf(
			'<textarea %s>%s</textarea>',
			get_block_wrapper_attributes( $this->getExtraWrapperAttributes() ),
			esc_textarea( $this->getBlockAttribute( 'fieldPlaceholder' ) )
		);
	}
}
