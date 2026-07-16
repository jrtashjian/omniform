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
		);
	}
}
