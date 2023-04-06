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
		$extra_attributes = array_filter(
			array(
				'id'   => sanitize_title( $this->getBlockContext( 'omniform/fieldName' ) ),
				'name' => sanitize_title( $this->getBlockContext( 'omniform/fieldName' ) ),
				'type' => $this->getBlockAttribute( 'fieldType' ),
			)
		);

		return sprintf(
			'<input %s />',
			get_block_wrapper_attributes( $extra_attributes )
		);
	}
}
