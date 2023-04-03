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
		return sprintf(
			'<select id="%1$s" name="%1$s" %2$s>%3$s</select>',
			$this->getBlockContext( 'omniform/fieldName' ),
			get_block_wrapper_attributes(),
			''
		);
	}
}
