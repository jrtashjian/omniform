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
		if ( 'omniform' === $this->get_block_context( 'postType' ) ) {
			$entity_id = $this->get_block_context( 'postId' );
		}

		if ( ! empty( $this->get_block_attribute( 'ref' ) ) ) {
			$entity_id = $this->get_block_attribute( 'ref' );
		}

		if ( empty( $entity_id ) ) {
			return '';
		}

		// Setup the Form object.
		try {
			/** @var \OmniForm\Plugin\Form */ // phpcs:ignore
			$form = omniform()->get( \OmniForm\Plugin\Form::class )->get_instance( $entity_id );
		} catch ( \Exception $e ) {
			// Display notice for logged in editors, render nothing for visitors.
			return current_user_can( 'edit_posts' )
				? sprintf(
					'<p style="color:var(--wp--preset--color--vivid-red,#cf2e2e);">%s</p>',
					esc_html( $e->getMessage() )
				)
				: '';
		}

		// If the form is password protected, render the password form.
		if ( $form->is_password_protected() ) {
			return get_the_password_form();
		}

		if ( ! $form->is_published() && ! is_preview() ) {
			// Display notice for logged in editors, render nothing for visitors.
			return current_user_can( 'edit_post', $form->get_id() )
				? sprintf(
					'<p style="color:var(--wp--preset--color--vivid-red,#cf2e2e);">%s<br/><a href="%s">%s</a></p>',
					/* translators: %s: Form title. */
					esc_html( sprintf( __( 'You must publish the "%s" form for visitors to see it.', 'omniform' ), $form->get_title() ) ),
					esc_url( admin_url( sprintf( 'post.php?post=%d&action=edit', $form->get_id() ) ) ),
					esc_html( __( 'Edit the form', 'omniform' ) )
				)
				: '';
		}

		$content = array(
			do_blocks( $form->get_content() ),
			wp_nonce_field( 'omniform', 'wp_rest', true, false ),
		);

		// Add a default success response notification block if one is not present.
		if ( ! preg_match( '/success-response-notification[^"]+?wp-block-omniform-response-notification/', $content[0] ) ) {
			array_unshift(
				$content,
				render_block(
					array(
						'blockName' => 'omniform/response-notification',
						'attrs'     => array(
							'messageType'    => 'success',
							'messageContent' => __( 'Success! Your submission has been completed.', 'omniform' ),
							'style'          => array(
								'border'  => array(
									'left' => array(
										'color' => 'var(--wp--preset--color--vivid-green-cyan,#00d084)',
										'width' => '6px',
									),
								),
								'spacing' => array(
									'padding' => array(
										'top'    => '0.5em',
										'bottom' => '0.5em',
										'left'   => '1.5em',
										'right'  => '1.5em',
									),
								),
							),
						),
					)
				)
			);
		}

		// Add a default error response notification block if one is not present.
		if ( ! preg_match( '/error-response-notification[^"]+?wp-block-omniform-response-notification/', $content[0] ) ) {
			array_unshift(
				$content,
				render_block(
					array(
						'blockName' => 'omniform/response-notification',
						'attrs'     => array(
							'messageType'    => 'error',
							'messageContent' => __( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ),
							'style'          => array(
								'border'  => array(
									'left' => array(
										'color' => 'var(--wp--preset--color--vivid-red,#cf2e2e)',
										'width' => '6px',
									),
								),
								'spacing' => array(
									'padding' => array(
										'top'    => '0.5em',
										'bottom' => '0.5em',
										'left'   => '1.5em',
										'right'  => '1.5em',
									),
								),
							),
						),
					)
				)
			);
		}

		/**
		 * Fires when the form is rendered.
		 *
		 * @param int $form_id The form ID.
		 */
		do_action( 'omniform_form_render', $form->get_id() );

		return sprintf(
			'<form method="post" action="%s" %s>%s</form>',
			esc_url( rest_url( 'omniform/v1/forms/' . $form->get_id() . '/responses' ) ),
			get_block_wrapper_attributes(),
			implode( '', $content )
		);
	}
}
