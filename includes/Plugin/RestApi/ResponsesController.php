<?php
/**
 * The ResponsesController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\RestApi;

/**
 * The ResponsesController class.
 */
class ResponsesController extends \WP_REST_Posts_Controller {
	/**
	 * Registers the routes for attachments.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/responses',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_response' ),
				'permission_callback' => array( $this, 'create_response_permissions_check' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to create a response.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_response_permissions_check( \WP_REST_Request $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		return rest_cookie_check_errors( null );
	}

	/**
	 * Creates a single response.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_response( \WP_REST_Request $request ) {
		try {
			$form = omniform()->get( \OmniForm\Plugin\Form::class )->get_instance( absint( $request->get_param( 'id' ) ) );
		} catch ( \Exception $e ) {
			return new \WP_Error(
				'omniform_not_found',
				esc_html( $e->getMessage() ),
				array( 'status' => 404 )
			);
		}

		// Prepare the submitted data.
		$prepared_response_data = $this->sanitize_array(
			$request->get_params()
		);

		$errors = $form->validate( $prepared_response_data );

		if ( ! empty( $errors ) ) {
			$response = array(
				'status'         => 400,
				'message'        => 'validation_failed',
				'invalid_fields' => $errors,
			);

			return rest_ensure_response(
				new \WP_HTTP_Response( $response, $response['status'] )
			);
		}

		/**
		 * Filter out the fields we don't want to save.
		 *
		 * @param string[] $filtered_request_params The filtered request params.
		 */
		$filtered_request_params = apply_filters( 'omniform_filtered_request_params', array( 'id', 'rest_route', 'wp_rest', '_locale', '_wp_http_referer' ) );

		$filter_callback = function ( $key ) use ( $filtered_request_params ) {
			return ! in_array( $key, $filtered_request_params, true );
		};

		// Prepare the form data.
		$prepared_fields_data = $this->sanitize_array( $form->get_fields() );
		$prepared_groups_data = $this->sanitize_array( $form->get_groups() );

		$response_id = wp_insert_post(
			array(
				'post_title'   => wp_generate_uuid4(),
				'post_content' => wp_json_encode(
					array(
						'response' => array_filter( $prepared_response_data, $filter_callback, ARRAY_FILTER_USE_KEY ),
						'fields'   => array_filter( $prepared_fields_data, $filter_callback, ARRAY_FILTER_USE_KEY ),
						'groups'   => $prepared_groups_data,
					)
				),
				'post_type'    => 'omniform_response',
				'post_status'  => 'publish',
				'post_parent'  => $form->get_id(),
				'meta_input'   => array(
					'_omniform_id'      => $form->get_id(),
					'_omniform_user_ip' => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ),
					'_wp_http_referer'  => sanitize_url( $request->get_param( '_wp_http_referer' ) ),
				),
			),
			true
		);

		if ( is_wp_error( $response_id ) ) {
			return rest_ensure_response( $response_id );
		}

		/**
		 * Fires after a response has been created.
		 *
		 * @param int $response_id The response ID.
		 * @param \OmniForm\Plugin\Form $form The form instance.
		 */
		do_action( 'omniform_response_created', $response_id, $form );

		$response = array(
			'status'  => 201,
			'message' => 'response_created',
		);

		return rest_ensure_response(
			new \WP_HTTP_Response( $response, $response['status'] )
		);
	}

	/**
	 * Sanitizes an array of data.
	 *
	 * @param mixed $data The data to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_array( $data ) {
		return is_array( $data )
			? array_map( array( $this, 'sanitize_array' ), $data )
			: sanitize_textarea_field( $data );
	}
}
