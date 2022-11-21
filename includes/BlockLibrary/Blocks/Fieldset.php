<?php
/**
 * The Fieldset block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

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
		return inquirywp()->basePath( '/build/block-library/fieldset' );
	}

	/**
	 * Renders the block on the server.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block default content.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content ) {
		if ( empty( $attributes['legend'] ) ) {
			return '';
		}

		return sprintf(
			'<fieldset class="wp-block-inquirywp-fieldset is-layout-flow"><legend class="inquirywp-field-label">%1$s</legend>%2$s</fieldset>',
			esc_html( $attributes['legend'] ),
			do_blocks( $content )
		);
	}
}
