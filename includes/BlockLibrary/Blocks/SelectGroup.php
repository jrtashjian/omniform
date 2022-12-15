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
class SelectGroup extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) {
		parent::renderBlock( $attributes, $content, $block );

		if ( ! array_key_exists( 'fieldLabel', $attributes ) ) {
			return '';
		}

		return sprintf(
			'<optgroup label="%s">%s</optgroup>',
			esc_attr( $attributes['fieldLabel'] ),
			do_blocks( $content )
		);
	}
}
