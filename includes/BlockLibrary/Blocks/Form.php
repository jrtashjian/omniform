<?php
/**
 * The Form block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Plugin\FormIngestionEngine;

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
		return omniform()->basePath( '/build/block-library/form' );
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
		if ( empty( $attributes['ref'] ) && empty( $block->context['postId'] ) ) {
			return '';
		}

		if ( array_key_exists( 'postType', $block->context ) && 'omniform' === $block->context['postType'] ) {
			$entity_id = $block->context['postId'];
		} else {
			$entity_id = $attributes['ref'];
		}

		$form_block = get_post( $entity_id );
		if ( ! $form_block || 'omniform' !== $form_block->post_type ) {
			return '';
		}

		if ( 'publish' !== $form_block->post_status || ! empty( $form_block->post_password ) ) {
			return '';
		}

		// Process the form.
		$form_ingestion = omniform()->get( FormIngestionEngine::class );
		$form_ingestion->setFormId( $entity_id );

		$post_data = '';

		if ( $form_ingestion->willProcess() ) {
			$post_data = sprintf(
				'<pre class="wp-block-preformatted">%s</pre>',
				print_r(
					array(
						'attributes'     => $attributes,
						'_POST'          => $_POST,
						'form_ingestion' => $form_ingestion,
					),
					true
				)
			);

			$form_ingestion->savePostData();

			// Incremement form submissions.
			$submissions = get_post_meta( $entity_id, 'submissions', true );
			update_post_meta( $entity_id, 'submissions', (int) $submissions + 1 );
		} else {
			// Incremement form impressions.
			$impressions = get_post_meta( $entity_id, 'impressions', true );
			update_post_meta( $entity_id, 'impressions', (int) $impressions + 1 );
		}

		$content = do_blocks( $form_block->post_content );
		$form_ingestion->resetFormData();

		return sprintf(
			'%s<form method="post" action="%s" class="wp-block-omniform-form">%s</form>',
			$post_data,
			esc_url( get_the_permalink() ),
			$form_ingestion->getNonceField() . $form_ingestion->getHoneypotField() . $content
		);
	}
}
