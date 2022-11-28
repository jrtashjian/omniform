<?php
/**
 * The SelectGroup block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The SelectGroup block class.
 */
class SelectGroup implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return omniform()->basePath( '/build/block-library/select-group' );
	}

	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		if ( ! array_key_exists( 'label', $attributes ) ) {
			return '';
		}

		return sprintf(
			'<optgroup label="%s">%s</optgroup>',
			esc_attr( $attributes['label'] ),
			do_blocks( $content )
		);
	}
}
