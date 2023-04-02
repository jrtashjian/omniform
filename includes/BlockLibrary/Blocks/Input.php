<?php
/**
 * The Input block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Input block class.
 */
class Input extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		return sprintf(
			'<input type="%1$s" id="%2$s" name="%2$s" %3$s />',
			$this->getBlockAttribute( 'fieldType' ),
			$this->getBlockContext( 'omniform/fieldName' ),
			get_block_wrapper_attributes(),
		);
	}
}
