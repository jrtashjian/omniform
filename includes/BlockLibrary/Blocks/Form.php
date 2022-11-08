<?php
/**
 * The Form block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

use InquiryWP\Plugin\FormIngestionEngine;

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
		return inquirywp()->basePath( '/build/block-library/form' );
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

		// Process the form.
		$form_ingestion = inquirywp()->get( FormIngestionEngine::class );
		$form_ingestion->setFormId( $attributes['ref'] );

		if ( $form_ingestion->willProcess() ) {
			return sprintf(
				'<pre>%s</pre>%s',
				print_r(
					array(
						'attributes' => $attributes,
						'_POST'      => $_POST,
					),
					true
				),
				do_blocks( $form_block->post_content )
			);
		}

		$content = do_blocks( $form_block->post_content );

		return sprintf(
			'<form method="post" action="%s" class="wp-block-inquirywp-form">%s</form>',
			esc_url( get_the_permalink() ),
			$form_ingestion->getNonceField() . $content
		);
	}
}
