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
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata();

	/**
	 * Renders the block on the server.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block default content.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content );
}
