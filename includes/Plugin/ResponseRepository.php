<?php
/**
 * Response repository.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Exceptions\InvalidResponseIdException;
use OmniForm\Exceptions\ResponseNotFoundException;
use OmniForm\Form\Response;

/**
 * Loads and saves domain Responses on the omniform_response post type.
 */
class ResponseRepository {
	/**
	 * Load a response by post ID.
	 *
	 * @throws InvalidResponseIdException If the ID is not positive.
	 * @throws ResponseNotFoundException If missing or wrong post type.
	 * @throws \InvalidArgumentException If post content is not a valid payload.
	 */
	public function get( int $response_id ): Response {
		if ( $response_id < 1 ) {
			throw new InvalidResponseIdException(
				/* translators: %d: Response ID. */
				esc_attr( sprintf( __( 'Response ID must be an integer. &#8220;%s&#8221; is not a valid integer.', 'omniform' ), $response_id ) )
			);
		}

		$post = get_post( $response_id );

		if ( ! $post || 'omniform_response' !== $post->post_type ) {
			throw new ResponseNotFoundException(
				/* translators: %d: Response ID. */
				esc_attr( sprintf( __( 'Response ID &#8220;%d&#8221; does not exist.', 'omniform' ), $response_id ) )
			);
		}

		$data = json_decode( $post->post_content, true );

		if ( ! is_array( $data ) ) {
			throw new \InvalidArgumentException(
				sprintf( 'Response ID %d does not contain a valid JSON payload.', $response_id )
			);
		}

		return Response::from_array( $data );
	}

	/**
	 * Persist a response under a form.
	 *
	 * @param Response             $response Domain response (schema + submission).
	 * @param int                  $form_id  Parent form post ID.
	 * @param array<string, mixed> $meta     Optional post meta (e.g. user IP, referer).
	 *
	 * @return int New response post ID.
	 *
	 * @throws \InvalidArgumentException If the form ID is invalid.
	 * @throws \RuntimeException If wp_insert_post fails.
	 */
	public function save( Response $response, int $form_id, array $meta = array() ): int {
		if ( $form_id < 1 ) {
			throw new \InvalidArgumentException( 'Form ID must be a positive integer.' );
		}

		$payload = wp_json_encode( $response->to_array(), JSON_UNESCAPED_UNICODE );

		if ( false === $payload ) {
			throw new \RuntimeException( 'Failed to encode response payload.' );
		}

		$meta_input = array(
			'_omniform_id' => $form_id,
		);

		if ( array_key_exists( 'user_ip', $meta ) ) {
			$meta_input['_omniform_user_ip'] = sanitize_text_field( (string) $meta['user_ip'] );
		}

		if ( array_key_exists( 'referer', $meta ) ) {
			$meta_input['_wp_http_referer'] = esc_url_raw( (string) $meta['referer'] );
		}

		$result = wp_insert_post(
			array(
				'post_title'   => wp_generate_uuid4(),
				'post_content' => $payload,
				'post_type'    => 'omniform_response',
				'post_status'  => 'omniform_unread',
				'post_parent'  => $form_id,
				'meta_input'   => $meta_input,
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			throw new \RuntimeException( $result->get_error_message() );
		}

		return (int) $result;
	}
}
