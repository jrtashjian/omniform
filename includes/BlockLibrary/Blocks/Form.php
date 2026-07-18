<?php
/**
 * The Form block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Form\Form as DomainForm;
use OmniForm\Form\FormNotificationSettings;
use OmniForm\Form\Response as DomainResponse;
use OmniForm\Plugin\BlockFormSchemaParser;
use OmniForm\Plugin\FormRenderContext;
use OmniForm\Plugin\FormRenderSettings;
use OmniForm\Plugin\RespectSubmissionValidator;
use OmniForm\Plugin\ResponseNotificationMailer;
use OmniForm\Plugin\SubmissionFactory;
use OmniForm\Plugin\SubmissionRenderState;
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
	public function render(): string {
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
			if ( ! empty( $entity_id ) ) {
				$form = omniform()->form( (int) $entity_id );
			} else {
				$form = omniform()->form_from_content( serialize_blocks( $this->instance->parsed_block['innerBlocks'] ) );
			}

			$this->content = $form->content();
		} catch ( \Exception $e ) {
			// Display notice for logged in editors, render nothing for visitors.
			return current_user_can( 'edit_posts' )
				? sprintf(
					'<p style="color:var(--wp--preset--color--vivid-red,#cf2e2e);">%s</p>',
					esc_html( $e->getMessage() )
				)
				: '';
		}

		// If the form is empty, render nothing.
		if ( empty( $this->content ) ) {
			return '';
		}

		$render_settings = new FormRenderSettings();
		$this->prepare_render_context( $form, $render_settings );

		// If the form is password protected, render the password form.
		if ( $form->is_persisted() && $render_settings->is_password_protected( (int) $form->id() ) ) {
			return get_the_password_form();
		}

		if ( ! $form->is_published() && ! is_preview() ) {
			// Display notice for logged in editors, render nothing for visitors.
			return current_user_can( 'edit_post', $form->id() )
				? sprintf(
					'<p style="color:var(--wp--preset--color--vivid-red,#cf2e2e);">%s<br/><a href="%s">%s</a></p>',
					/* translators: %s: Form title. */
					esc_html( sprintf( __( 'You must publish the "%s" form for visitors to see it.', 'omniform' ), $form->title() ) ),
					esc_url( admin_url( sprintf( 'post.php?post=%d&action=edit', $form->id() ) ) ),
					esc_html( __( 'Edit the form', 'omniform' ) )
				)
				: '';
		}

		// Forms without a submit action are likely submitting elsewhere, so we don't need to add default response notifications.
		if ( empty( $this->get_block_attribute( 'submit_action' ) ) ) {
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
		}

		return $form->is_persisted()
			? $this->render_standard( $form, $render_settings )
			: $this->render_standalone( $form );
	}

	/**
	 * Seed request-scoped render context for child blocks.
	 *
	 * @param DomainForm         $form             Domain form.
	 * @param FormRenderSettings $render_settings  Render settings adapter.
	 */
	private function prepare_render_context( DomainForm $form, FormRenderSettings $render_settings ): void {
		$override = $form->is_persisted()
			? null
			: ( is_string( $this->get_block_attribute( 'required_label' ) )
				? $this->get_block_attribute( 'required_label' )
				: null );

		$label = $render_settings->required_label(
			$form->id(),
			$override
		);

		omniform()->container()->get( FormRenderContext::class )->set_required_label( $label );
	}

	/**
	 * Renders the form in standard mode.
	 *
	 * @param DomainForm         $form            The form object.
	 * @param FormRenderSettings $render_settings Render settings adapter.
	 *
	 * @return string The rendered form.
	 */
	private function render_standard( DomainForm $form, FormRenderSettings $render_settings ) {
		$form_id = (int) $form->id();

		/**
		 * Fires when the form is rendered.
		 *
		 * @param int $form_id The form ID.
		 */
		do_action( 'omniform_form_render', $form_id );

		return $this->get_form_wrapper(
			$render_settings->submit_method( $form_id ),
			$render_settings->submit_action( $form_id ),
			$this->process_callbacks( do_blocks( $this->content ) ) . wp_nonce_field( 'omniform', 'wp_rest', true, false )
		);
	}

	/**
	 * Renders the form in standalone mode.
	 *
	 * @param DomainForm $form The form object.
	 *
	 * @return string The rendered form.
	 */
	private function render_standalone( DomainForm $form ) {
		$form_hash      = sha1( $this->content );
		$submitted_hash = sanitize_text_field( filter_input( INPUT_POST, 'omniform_hash' ) );

		if ( $submitted_hash === $form_hash && wp_verify_nonce( $_REQUEST['_wpnonce'], 'omniform' . $form_hash ) ) {
			$schema     = ( new BlockFormSchemaParser() )->parse( $form->content() );
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
			$submission = ( new SubmissionFactory() )->from_request( $_POST, $_FILES );
			$validation = ( new RespectSubmissionValidator() )->validate( $schema, $submission );
			$state      = omniform()->container()->get( SubmissionRenderState::class );

			if ( $validation->is_valid() ) {
				$state->mark_succeeded();
				$this->send_standalone_notification(
					new DomainResponse( $schema, $submission )
				);
			} else {
				$state->mark_failed( $validation->messages() );
			}
		}

		$additional_fields = array();

		if ( empty( $this->get_block_attribute( 'submit_action' ) ) ) {
			$additional_fields[] = sprintf(
				'<input type="hidden" name="omniform_hash" value="%s">',
				esc_attr( $form_hash )
			);

			$additional_fields[] = wp_nonce_field( 'omniform' . $form_hash, '_wpnonce', true, false );
		}

		return $this->get_form_wrapper(
			$this->get_block_attribute( 'submit_method' ) ?? 'POST',
			$this->get_block_attribute( 'submit_action' ) ?? '',
			$this->process_callbacks( do_blocks( $this->content ) ) . implode( '', $additional_fields )
		);
	}

	/**
	 * Returns the form wrapper.
	 *
	 * @param string $submit_method  The form submit method.
	 * @param string $submit_action  The form submit action.
	 * @param string $inner_content  The inner content.
	 *
	 * @return string The form wrapper.
	 */
	private function get_form_wrapper( $submit_method, $submit_action, $inner_content = '' ) {
		$form_wrapper     = '<form method="%1$s" action="%2$s" %3$s>%4$s</form>';
		$extra_attributes = array();

		// Change the form wrapper for comment forms.
		$submit_action = $this->process_callbacks( $submit_action );
		if ( str_ends_with( $submit_action, '/wp-comments-post.php' ) ) {
			$form_wrapper     = '<div %3$s><form method="%1$s" action="%2$s">%4$s</form></div>';
			$extra_attributes = array(
				'id'    => 'respond',
				'class' => 'comment-respond',
			);
			// Enqueue the comment-reply script.
			wp_enqueue_script( 'comment-reply' );
			$inner_content .= get_comment_id_fields( $this->get_block_context( 'postId' ) );
		}

		return sprintf(
			$form_wrapper,
			esc_attr( strtolower( $submit_method ) ),
			esc_attr( $submit_action ),
			get_block_wrapper_attributes( $extra_attributes ),
			$inner_content,
		);
	}

	/**
	 * Email a standalone form submission using block notify attributes.
	 *
	 * @param DomainResponse $response Domain response snapshot.
	 */
	private function send_standalone_notification( DomainResponse $response ): void {
		$notify_email = $this->get_block_attribute( 'notify_email' );
		$subject      = $this->get_block_attribute( 'notify_email_subject' );

		$recipients = $this->standalone_recipients( $notify_email );
		if ( array() === $recipients ) {
			return;
		}

		if ( ! is_string( $subject ) || '' === $subject ) {
			$subject = $this->standalone_default_subject();
		}

		$domain_form = new DomainForm(
			content: $this->content,
			title: (string) ( $this->get_block_attribute( 'form_title' ) ?? '' ),
			notifications: new FormNotificationSettings( $recipients, $subject ),
		);

		$user_ip = filter_var( $_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP );

		( new ResponseNotificationMailer() )->send(
			$response,
			$domain_form,
			array(
				'user_ip' => $user_ip ? $user_ip : '',
				'referer' => isset( $_SERVER['HTTP_REFERER'] )
					? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) )
					: '',
				'time'    => (string) current_time( 'mysql' ),
			)
		);
	}

	/**
	 * @param mixed $notify_email Block attribute value.
	 * @return list<string>
	 */
	private function standalone_recipients( mixed $notify_email ): array {
		if ( is_string( $notify_email ) && '' !== $notify_email ) {
			return array( $notify_email );
		}

		if ( is_array( $notify_email ) ) {
			$recipients = array();
			foreach ( $notify_email as $email ) {
				if ( is_string( $email ) && '' !== $email ) {
					$recipients[] = $email;
				}
			}
			if ( array() !== $recipients ) {
				return $recipients;
			}
		}

		$admin = get_option( 'admin_email' );

		return ( is_string( $admin ) && '' !== $admin ) ? array( $admin ) : array();
	}

	/**
	 * Default subject for standalone forms.
	 */
	private function standalone_default_subject(): string {
		$form_title = $this->get_block_attribute( 'form_title' );

		if ( is_string( $form_title ) && '' !== $form_title ) {
			return sprintf(
				/* translators: %1$s: Site name. %2$s: Form title. */
				__( 'New Response: %1$s - %2$s', 'omniform' ),
				get_option( 'blogname' ),
				$form_title
			);
		}

		return sprintf(
			/* translators: %1$s: Site name. */
			__( 'New Response: %1$s', 'omniform' ),
			get_option( 'blogname' )
		);
	}

	/**
	 * Checks if the content has a success response notification block.
	 *
	 * @return boolean True if the content has a success response notification block, false otherwise.
	 */
	private function has_success_response_notification() {
		return (bool) (
			preg_match( '/<!-- wp:omniform\/response-notification.*?"className":"[^"]*?is-style-success[^"]*?".*?-->/', $this->content )
			// Fallback for pre-'is-style-' response notifications.
			|| preg_match( '/<!-- wp:omniform\/response-notification.*?"messageType":"[^"]*?success[^"]*?".*?-->/', $this->content )
			|| preg_match( '/<!-- wp:omniform\/response-notification.*?"color":"[^"]*?vivid-green-cyan,#00d084[^"]*?".*?-->/', $this->content )
		);
	}

	/**
	 * Checks if the content has an error response notification block.
	 *
	 * @return boolean True if the content has an error response notification block, false otherwise.
	 */
	private function has_error_response_notification() {
		return (bool) (
			preg_match( '/<!-- wp:omniform\/response-notification.*?"className":"[^"]*?is-style-error[^"]*?".*?-->/', $this->content )
			// Fallback for pre-'is-style-' response notifications.
			|| preg_match( '/<!-- wp:omniform\/response-notification.*?"messageType":"[^"]*?error[^"]*?".*?-->/', $this->content )
			|| preg_match( '/<!-- wp:omniform\/response-notification.*?"color":"[^"]*?vivid-red,#cf2e2e[^"]*?".*?-->/', $this->content )
		);
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
					'messageContent' => $message_content,
					'className'      => 'is-style-' . $message_type,
				),
				'innerContent' => array(),
			)
		);
	}
}
