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
class Textarea extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		return sprintf(
			'<textarea name="%1$s" %2$s>%s</textarea>',
			$this->getBlockContext( 'omniform/fieldName' ),
			get_block_wrapper_attributes(),
			''
		);
	}
}
