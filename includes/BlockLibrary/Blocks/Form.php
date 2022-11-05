<?php
/**
 * The Form block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

/**
 * The Form block class.
 */
class Form implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return inquirywp()->basePath( '/packages/block-library/form' );
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
		if ( empty( $attributes['ref'] ) ) {
			return '';
		}

		$form_block = get_post( $attributes['ref'] );
		if ( ! $form_block || 'inquirywp_form' !== $form_block->post_type ) {
			return '';
		}

		if ( 'publish' !== $form_block->post_status || ! empty( $form_block->post_password ) ) {
			return '';
		}

		$button_classes = array(
			'wp-block-button',
			'wp-block-button__link',
			wp_theme_get_element_class_name( 'button' ),
		);
		$button_markup  = sprintf(
			'<div class="wp-block-buttons"><div class="wp-block-button"><button type="submit" class="%s">%s</button></div></div>',
			esc_attr( implode( ' ', $button_classes ) ),
			wp_kses_post( $attributes['btnSubmit'] )
		);

		$nonce   = wp_nonce_field( 'inquirywp_form_submission_' . $attributes['ref'], '_wpnonce', true, false );
		$content = do_blocks( $form_block->post_content );

		return sprintf(
			'<form method="post" action="%s" class="wp-block-inquirywp-form">%s</form>',
			esc_url( home_url( '/' ) ),
			$nonce . $content . $button_markup
		);
	}
}
