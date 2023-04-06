<?php
/**
 * The Label block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Label block class.
 */
class Label extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		if ( empty( $this->getBlockContext( 'omniform/fieldLabel' ) ) ) {
			return '';
		}

		return sprintf(
			'<label for="%s" %s>%s</label>',
			$this->getBlockContext( 'omniform/fieldName' ),
			get_block_wrapper_attributes(),
			$this->getBlockContext( 'omniform/fieldLabel' )
		);
	}
}
