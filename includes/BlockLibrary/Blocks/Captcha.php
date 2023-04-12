<?php
/**
 * The Captcha block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Captcha block class.
 */
class Captcha extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		return sprintf(
			'<div %s>CAPTCHA BLOCK</div>',
			get_block_wrapper_attributes()
		);
	}
}
