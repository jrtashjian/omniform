<?php
/**
 * Form repository.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Exceptions\FormNotFoundException;
use OmniForm\Exceptions\InvalidFormIdException;
use OmniForm\Form\Form;
use OmniForm\Form\FormNotificationSettings;

/**
 * Loads form definitions from the omniform post type.
 */
class FormRepository {
	/**
	 * Load a form by post ID.
	 *
	 * @param int $form_id The form post ID.
	 *
	 * @throws InvalidFormIdException If the form ID is not a positive integer.
	 * @throws FormNotFoundException If the form does not exist or is the wrong post type.
	 */
	public function get( int $form_id ): Form {
		if ( $form_id < 1 ) {
			throw new InvalidFormIdException(
				/* translators: %d: Form ID. */
				esc_attr( sprintf( __( 'Form ID must be an integer. &#8220;%s&#8221; is not a valid integer.', 'omniform' ), $form_id ) )
			);
		}

		$post = get_post( $form_id );

		if ( ! $post || 'omniform' !== $post->post_type ) {
			throw new FormNotFoundException(
				/* translators: %d: Form ID. */
				esc_attr( sprintf( __( 'Form ID &#8220;%d&#8221; does not exist.', 'omniform' ), $form_id ) )
			);
		}

		return new Form(
			content: $post->post_content,
			title: $post->post_title,
			status: $post->post_status,
			id: $post->ID,
			notifications: $this->notifications_from_meta( $post->ID, $post->post_title ),
		);
	}

	/**
	 * Build notification settings from form post meta (with legacy defaults).
	 *
	 * @param int    $form_id Form post ID.
	 * @param string $title   Form title for default subject.
	 */
	private function notifications_from_meta( int $form_id, string $title ): FormNotificationSettings {
		return new FormNotificationSettings(
			$this->recipients_from_meta( $form_id ),
			$this->subject_from_meta( $form_id, $title )
		);
	}

	/**
	 * @return list<string>
	 */
	private function recipients_from_meta( int $form_id ): array {
		$raw = get_post_meta( $form_id, 'notify_email', true );
		$recipients = $this->normalize_recipients( $raw );

		if ( array() !== $recipients ) {
			return $recipients;
		}

		$admin = get_option( 'admin_email' );

		return ( is_string( $admin ) && '' !== $admin ) ? array( $admin ) : array();
	}

	/**
	 * @param int    $form_id Form post ID.
	 * @param string $title   Form title.
	 */
	private function subject_from_meta( int $form_id, string $title ): string {
		$subject = get_post_meta( $form_id, 'notify_email_subject', true );

		if ( is_string( $subject ) && '' !== $subject ) {
			return $subject;
		}

		return sprintf(
			/* translators: %1$s: Site name. %2$s: Form title. */
			__( 'New Response: %1$s - %2$s', 'omniform' ),
			(string) get_option( 'blogname' ),
			$title
		);
	}

	/**
	 * @param mixed $raw Meta value (string, list of strings, or empty).
	 * @return list<string>
	 */
	private function normalize_recipients( mixed $raw ): array {
		if ( is_string( $raw ) && '' !== $raw ) {
			return array( $raw );
		}

		if ( ! is_array( $raw ) ) {
			return array();
		}

		$recipients = array();

		foreach ( $raw as $email ) {
			if ( is_string( $email ) && '' !== $email ) {
				$recipients[] = $email;
			}
		}

		return $recipients;
	}
}
