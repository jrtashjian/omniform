<?php
/**
 * The Fieldset block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Fieldset block class.
 */
class Fieldset implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return omniform()->basePath( '/build/block-library/fieldset' );
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
		if ( empty( $attributes['fieldLabel'] ) ) {
			return '';
		}

		return sprintf(
			'<fieldset class="wp-block-omniform-fieldset is-layout-flow"><legend class="omniform-field-label">%1$s</legend>%2$s</fieldset>',
			esc_html( $attributes['fieldLabel'] ),
			do_blocks( $content )
		);
	}
}
