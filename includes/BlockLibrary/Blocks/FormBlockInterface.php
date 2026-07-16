<?php
/**
 * Contract for OmniForm blocks that render on the server.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Server-side render contract for OmniForm form blocks.
 *
 * Implementations receive WordPress render-callback arguments and return the
 * HTML for the block. {@see BaseBlock} provides the shared implementation.
 */
interface FormBlockInterface {

	/**
	 * Renders the block on the server.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param string               $content    Block default content.
	 * @param \WP_Block            $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function render_block( array $attributes, string $content, \WP_Block $block ): string;
}
