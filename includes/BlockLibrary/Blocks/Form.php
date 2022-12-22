<?php
/**
 * The Form block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Form block class.
 */
class Form extends BaseBlock {
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

		// Setup the Form object.
		$form = omniform()->make( \OmniForm\Plugin\Form::class );
		$form->setId( $entity_id );

		// Incremement form impressions.
		// $impressions = get_post_meta( $entity_id, 'impressions', true );
		// update_post_meta( $entity_id, 'impressions', (int) $impressions + 1 );

		$content     = do_blocks( $form_block->post_content );
		$nonce_field = wp_nonce_field( 'omniform', 'wp_rest', true, false );

		$redirect_to_url     = remove_query_arg( '_wp_http_referer' );
		$default_redirect_to = '<input type="hidden" name="_omniform_redirect" value="' . esc_url( $redirect_to_url ) . '" />';

		return sprintf(
			'<form method="post" action="%s" %s>%s</form>',
			esc_url( rest_url( 'omniform/v1/forms/' . $entity_id . '/submissions' ) ),
			get_block_wrapper_attributes(),
			$content . $nonce_field . $default_redirect_to
		);
	}
}
