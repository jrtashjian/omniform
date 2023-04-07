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

		// Setup the Form object.
		$form = omniform()->get( \OmniForm\Plugin\Form::class )->getInstance( $entity_id );

		if ( ! $form ) {
			// Display notice for logged in editors, render nothing for visitors.
			return current_user_can( 'edit_posts' )
				? sprintf(
					'<p style="color:var(--wp--preset--color--vivid-red,#cf2e2e);">%s</p>',
					/* translators: %d: Form ID. */
					esc_html( sprintf( __( 'Form ID &#8220;%d&#8221; has been removed.', 'omniform' ), $entity_id ) )
				)
				: '';
		}

		if ( ! $form->isPublished() || $form->isPrivate() ) {
			// Display notice for logged in editors, render nothing for visitors.
			return current_user_can( 'edit_post', $form->getId() )
				? sprintf(
					'<p style="color:var(--wp--preset--color--vivid-red,#cf2e2e);">%s<br/><a href="%s">%s</a></p>',
					/* translators: %s: Form title. */
					esc_html( sprintf( __( 'You must publish the "%s" form for visitors to see it.', 'omniform' ), $form->getTitle() ) ),
					esc_url( admin_url( sprintf( 'post.php?post=%d&action=edit', $form->getId() ) ) ),
					esc_html( __( 'Edit the form', 'omniform' ) )
				)
				: '';
		}

		if ( ! is_admin() ) {
			// Incremement form impressions.
			$impressions = get_post_meta( $entity_id, '_omniform_impressions', true );
			update_post_meta( $entity_id, '_omniform_impressions', (int) $impressions + 1 );
		}

		$content     = do_blocks( $form->getContent() );
		$nonce_field = wp_nonce_field( 'omniform', 'wp_rest', true, false );

		return sprintf(
			'%s<form method="post" action="%s" %s>%s</form>',
			empty( $_GET['success'] ) ? '' : '<p style="background-color:var(--wp--preset--color--vivid-green-cyan);padding:1rem 1.5rem;">Submitted!</p>',
			esc_url( rest_url( 'omniform/v1/forms/' . $form->getId() . '/responses' ) ),
			get_block_wrapper_attributes(),
			$content . $nonce_field
		);
	}
}
