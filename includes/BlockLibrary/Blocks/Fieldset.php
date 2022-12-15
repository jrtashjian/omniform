<?php
/**
 * The Fieldset block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;

/**
 * The Fieldset block class.
 */
class Fieldset extends BaseBlock {
	use HasColors;

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

		if ( empty( $attributes['fieldLabel'] ) ) {
			return '';
		}

		$classes = array(
			'wp-block-omniform-' . $this->blockTypeName(),
			'is-layout-flow',
			$this->getColorClasses( $attributes ),
		);

		return sprintf(
			'<fieldset class="%s" %s><legend class="omniform-field-label">%s</legend>%s</fieldset>',
			esc_attr( implode( ' ', $classes ) ),
			$this->getColorStyles( $attributes ),
			esc_html( $attributes['fieldLabel'] ),
			do_blocks( $content )
		);
	}
}
