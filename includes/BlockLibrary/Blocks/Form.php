<?php
/**
 * The Form block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Traits\HasColors;
use OmniForm\Plugin\FormIngestionEngine;

/**
 * The Form block class.
 */
class Form extends BaseBlock {
	use HasColors;

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( 'omniform' === $this->getBlockContext( 'postType' ) ) {
			$entity_id = $this->getBlockContext( 'postId' );
		}

		if ( ! empty( $this->attributes['ref'] ) ) {
			$entity_id = $this->attributes['ref'];
		}

		if ( empty( $entity_id ) ) {
			return '';
		}

		$form_block = get_post( $entity_id );
		if ( ! $form_block || 'omniform' !== $form_block->post_type ) {
			return '';
		}

		if ( 'publish' !== $form_block->post_status || ! empty( $form_block->post_password ) ) {
			return '';
		}

		// // Process the form.
		// $form_ingestion = omniform()->get( FormIngestionEngine::class );
		// $form_ingestion->setFormId( $entity_id );

		// $post_data = '';

		// if ( $form_ingestion->willProcess() ) {
		// $post_data = sprintf(
		// '<pre class="wp-block-preformatted">%s</pre>',
		// print_r(
		// array(
		// 'attributes'     => $this->attributes,
		// '_POST'          => $_POST,
		// 'form_ingestion' => $form_ingestion,
		// ),
		// true
		// )
		// );

		// $form_ingestion->savePostData();

		// Incremement form submissions.
		// $submissions = get_post_meta( $entity_id, 'submissions', true );
		// update_post_meta( $entity_id, 'submissions', (int) $submissions + 1 );
		// } else {
		// Incremement form impressions.
		// $impressions = get_post_meta( $entity_id, 'impressions', true );
		// update_post_meta( $entity_id, 'impressions', (int) $impressions + 1 );
		// }

		$content     = do_blocks( $form_block->post_content );
		$nonce_field = wp_nonce_field( 'omniform', 'wp_rest', true, false );
		// $form_ingestion->resetFormData();

		return sprintf(
			'<form method="post" action="%s" %s>%s</form>',
			esc_url( rest_url( 'omniform/v1/forms/' . $entity_id . '/submissions' ) ),
			get_block_wrapper_attributes(),
			$content . $nonce_field
		);
	}
}
