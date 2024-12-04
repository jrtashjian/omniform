<?php
/**
 * The ResponseFactory class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Dependencies\League\Container\Container;
use OmniForm\Exceptions\ResponseNotFoundException;
use OmniForm\Exceptions\InvalidResponseIdException;

/**
 * The ResponseFactory class.
 */
class ResponseFactory {
	/**
	 * The Container object.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * The ResponseFactory constructor.
	 *
	 * @param Container $container The Container object.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Create a new instance of the Response class.
	 *
	 * @param Form $form The form object.
	 *
	 * @return Response The newly created Response instance.
	 */
	public function create_with_form( Form $form ): Response {
		$response = new Response();

		$response->set_request_params(
			array_merge(
				$form->get_request_params(),
				array(
					'_omniform_user_ip' => $_SERVER['REMOTE_ADDR'],
				),
			)
		);

		$response->set_fields( $form->get_fields() );
		$response->set_groups( $form->get_groups() );
		$response->set_date( current_time( 'mysql' ) );

		return $response;
	}

	/**
	 * Create a new instance of the Response class.
	 *
	 * @param int $response_id The response ID.
	 *
	 * @return Response The newly created Response instance.
	 *
	 * @throws ResponseNotFoundException If the response does not exist.
	 * @throws InvalidResponseIdException If the response ID is invalid.
	 */
	public function create_with_id( $response_id ): Response {
		$response_id = (int) $response_id;

		if ( ! $response_id ) {
			throw new InvalidResponseIdException(
				/* translators: %d: Response ID. */
				esc_attr( sprintf( __( 'Response ID must be an integer. &#8220;%s&#8221; is not a valid integer.', 'omniform' ), $response_id ) )
			);
		}

		$_response = get_post( $response_id );

		if ( ! $_response || 'omniform_response' !== $_response->post_type ) {
			throw new ResponseNotFoundException(
				/* translators: %d: Response ID. */
				esc_attr( sprintf( __( 'Response ID &#8220;%d&#8221; does not exist.', 'omniform' ), $response_id ) )
			);
		}

		$response = new Response();

		$data = json_decode( $_response->post_content, true );

		// Fallback for old responses.
		if ( empty( $data['response'] ) ) {
			$data = array(
				'response' => $data,
				'fields'   => array_combine( array_keys( $data ), array_keys( $data ) ),
				'groups'   => array_combine( array_keys( $data ), array_keys( $data ) ),
			);
		}

		$post_meta = get_post_meta( $response_id );

		$response->set_request_params(
			array_merge(
				$data['response'],
				array(
					'_wp_http_referer'  => implode( '', $post_meta['_wp_http_referer'] ),
					'_omniform_user_ip' => implode( '', $post_meta['_omniform_user_ip'] ),
				),
			)
		);

		$response->set_fields( $data['fields'] );
		$response->set_groups( $data['groups'] );

		$response->set_date( $_response->post_date );

		return $response;
	}
}
