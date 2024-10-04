<?php
/**
 * The Field block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Field block class.
 */
class Field extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	protected function render() {
		if ( empty( $this->get_block_attribute( 'fieldLabel' ) ) ) {
			return '';
		}

		return sprintf(
			'<div %s>%s</div>',
			get_block_wrapper_attributes(
				array(
					// Bug in Core: https://github.com/WordPress/WordPress/blob/b9bdf794320b132e6a4ce1538e988e6d31be33b0/wp-includes/block-supports/layout.php#L817-L819/.
					'class' => 'wp-block-omniform-field-is-layout-flex',
				)
			),
			$this->content
		);
	}
}
