<?php
/**
 * The FormBlockInterface class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Describes the interface of a form block that exposes methods to render it's content.
 */
interface FormBlockInterface {

	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function render_block( $attributes, $content, $block );
}
