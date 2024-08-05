<?php
/**
 * The Form block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Plugin\FormFactory;
use OmniForm\Plugin\Response;
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

		if ( empty( $entity_id ) && empty( $this->content ) ) {
			return '';
		}

		// Setup the Form object.
		try {
			$form_factory = omniform()->get( FormFactory::class );

			if ( ! empty( $entity_id ) ) {
				/** @var \OmniForm\Plugin\Form */ // phpcs:ignore
				$form = $form_factory->create_with_id( $entity_id );
			} else {
				/** @var \OmniForm\Plugin\Form */ // phpcs:ignore
				$form = $form_factory->create_with_content( serialize_blocks( $this->instance->parsed_block['innerBlocks'] ) );
			}

			$this->content = $form->get_content();
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

		// Add a default success response notification block if one is not present.
		if ( ! $this->has_success_response_notification( $this->content ) ) {
			$this->content = $this->render_reponse_notification(
				'success',
				__( 'Success! Your submission has been completed.', 'omniform' )
			) . $this->content;
		}

		// Add a default error response notification block if one is not present.
		if ( ! $this->has_error_response_notification( $this->content ) ) {
			$this->content = $this->render_reponse_notification(
				'error',
				__( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' )
			) . $this->content;
		}

		return $form->get_id()
			? $this->render_standard( $form )
			: $this->render_standalone( $form );
	}

	/**
	 * Renders the form in standard mode.
	 *
	 * @param \OmniForm\Plugin\Form $form The form object.
	 *
	 * @return string The rendered form.
	 */
	private function render_standard( \OmniForm\Plugin\Form $form ) {
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
			do_blocks( $this->content ) . wp_nonce_field( 'omniform', 'wp_rest', true, false )
		);
	}

	/**
	 * Renders the form in standalone mode.
	 *
	 * @param \OmniForm\Plugin\Form $form The form object.
	 *
	 * @return string The rendered form.
	 */
	private function render_standalone( \OmniForm\Plugin\Form $form ) {
		$form_hash      = sha1( $this->content );
		$submitted_hash = sanitize_text_field( filter_input( INPUT_POST, 'omniform_hash' ) );

		if ( $submitted_hash === $form_hash && wp_verify_nonce( $_REQUEST['_wpnonce'], 'omniform' . $form_hash ) ) {
			// Validate the form.
			$form->set_request_params( $_POST );

			if ( empty( $form->validate() ) ) {
				// Send the notification email.
				$response = new Response( $form );

				$response->set_request_params( $form->get_request_params() );
				$response->set_fields( $form->get_fields() );
				$response->set_groups( $form->get_groups() );
				$response->set_date( current_time( 'mysql' ) );

				wp_mail(
					get_option( 'admin_email' ),
					// translators: %1$s represents the blog name.
					esc_attr( sprintf( __( 'New Response: %1$s', 'omniform' ), get_option( 'blogname' ) ) ),
					wp_kses( $response->email_content(), array() )
				);
			}
		}

		$form_hash_input = sprintf(
			'<input type="hidden" name="omniform_hash" value="%s">',
			esc_attr( $form_hash )
		);

		return sprintf(
			'<form method="%s" action="%s" %s>%s</form>',
			esc_attr( strtolower( 'POST' ) ),
			esc_attr( '' ),
			get_block_wrapper_attributes(),
			do_blocks( $this->content ) . $form_hash_input . wp_nonce_field( 'omniform' . $form_hash, '_wpnonce', true, false )
		);
	}

	/**
	 * Checks if the content has a success response notification block.
	 *
	 * @return boolean True if the content has a success response notification block, false otherwise.
	 */
	private function has_success_response_notification() {
		return (bool) preg_match( '/<!-- wp:omniform\/response-notification(?:(?!messageType).)*?-->/', $this->content )
			|| preg_match( '/<!-- wp:omniform\/response-notification.*?"messageType":"success".*?-->/', $this->content );
	}

	/**
	 * Checks if the content has an error response notification block.
	 *
	 * @return boolean True if the content has an error response notification block, false otherwise.
	 */
	private function has_error_response_notification() {
		return (bool) preg_match( '/<!-- wp:omniform\/response-notification.*?"messageType":"error".*?-->/', $this->content );
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
		return serialize_block(
			array(
				'blockName'    => 'omniform/response-notification',
				'attrs'        => array(
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
				'innerContent' => array(),
			)
		);
	}
}
