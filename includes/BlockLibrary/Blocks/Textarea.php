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
		$extra_attributes = array_filter(
			array(
				'id'   => sanitize_title( $this->getBlockContext( 'omniform/fieldName' ) ),
				'name' => sanitize_title( $this->getBlockContext( 'omniform/fieldName' ) ),
			)
		);

		return sprintf(
			'<textarea %s>%s</textarea>',
			get_block_wrapper_attributes( $extra_attributes ),
			$this->getBlockAttribute( 'fieldPlaceholder' )
		);
	}
}
