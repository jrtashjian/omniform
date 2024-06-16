<?php
/**
 * The Form block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Traits\CallbackSupport;

/**
 * The Form block class.
 */
class Form extends BaseBlock {
	use CallbackSupport;

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
		);

		if ( 'uncategorized' === $form->get_type() ) {
			// Add a nonce field to standard forms.
			$content[] = wp_nonce_field( 'omniform', 'wp_rest', true, false );

			// Add a default success response notification block if one is not present.
			if ( ! $this->has_success_response_notification( $content[0] ) ) {
				array_unshift(
					$content,
					$this->render_reponse_notification(
						'success',
						__( 'Success! Your submission has been completed.', 'omniform' )
					)
				);
			}

			// Add a default error response notification block if one is not present.
			if ( ! $this->has_error_response_notification( $content[0] ) ) {
				array_unshift(
					$content,
					$this->render_reponse_notification(
						'error',
						__( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' )
					)
				);
			}
		}

		/**
		 * Fires when the form is rendered.
		 *
		 * @param int $form_id The form ID.
		 */
		do_action( 'omniform_form_render', $form->get_id() );

		return sprintf(
			'<form method="%s" action="%s" %s>%s</form>',
			esc_attr( strtolower( $form->get_submit_method() ) ),
			esc_attr( $this->process_callbacks( $form->get_submit_action() ) ),
			get_block_wrapper_attributes(),
			implode( '', $content )
		);
	}

	/**
	 * Checks if the content has a success response notification block.
	 *
	 * @param string $content The content to check.
	 *
	 * @return boolean True if the content has a success response notification block, false otherwise.
	 */
	private function has_success_response_notification( $content ) {
		return preg_match( '/success-response-notification[^"]+?wp-block-omniform-response-notification/', $content );
	}

	/**
	 * Checks if the content has an error response notification block.
	 *
	 * @param string $content The content to check.
	 *
	 * @return boolean True if the content has an error response notification block, false otherwise.
	 */
	private function has_error_response_notification( $content ) {
		return preg_match( '/error-response-notification[^"]+?wp-block-omniform-response-notification/', $content );
	}

	/**
	 * Renders a response notification block.
	 *
	 * @param string $message_type    The message type.
	 * @param string $message_content The message content.
	 *
	 * @return string The rendered block.
	 */
	private function render_reponse_notification( $message_type, $message_content ) {
		return render_block(
			array(
				'blockName' => 'omniform/response-notification',
				'attrs'     => array(
					'messageType'    => $message_type,
					'messageContent' => $message_content,
					'style'          => array(
						'border'  => array(
							'left' => array(
								'color' => 'success' === $message_type
									? 'var(--wp--preset--color--vivid-green-cyan,#00d084)'
									: 'var(--wp--preset--color--vivid-red,#cf2e2e)',
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
		);
	}
}
