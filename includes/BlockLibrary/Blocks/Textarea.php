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
			'<textarea id="%1$s" name="%1$s" %2$s>%3$s</textarea>',
			$this->getBlockContext( 'omniform/fieldName' ),
			get_block_wrapper_attributes(),
			''
		);
	}
}
