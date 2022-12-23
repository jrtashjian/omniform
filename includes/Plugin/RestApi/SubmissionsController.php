<?php
/**
 * The SubmissionsController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\RestApi;

/**
 * The SubmissionsController class.
 */
class SubmissionsController extends \WP_REST_Posts_Controller {
	/**
	 * Registers the routes for attachments.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/submissions',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_submission' ),
				'permission_callback' => array( $this, 'create_submission_permissions_check' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to create a submission.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_submission_permissions_check( \WP_REST_Request $request ) {
		return rest_cookie_check_errors( null );
	}

	/**
	 * Creates a single submission.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_submission( \WP_REST_Request $request ) {
		$form = \OmniForm\Plugin\Form::getInstance( $request->get_param( 'id' ) );

		if ( ! $form ) {
			return new \WP_Error(
				'omniform_not_found',
				__( 'The requested form was not found.', 'omniform' ),
				array( 'status' => 404 )
			);
		}

		$errors = $form->validate( $request );

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

		$submission_data = array_filter(
			$request->get_params(),
			function( $key ) {
				return ! in_array( $key, array( 'rest_route', 'wp_rest', '_wp_http_referer', '_omniform_redirect' ), true );
			},
			ARRAY_FILTER_USE_KEY
		);

		$submission_id = wp_insert_post(
			array(
				'post_title'   => wp_generate_uuid4(),
				'post_content' => wp_json_encode( $submission_data ),
				'post_type'    => 'omniform_submission',
				'post_status'  => 'publish',
				'meta_input'   => array(
					'_omniform_id'      => $form->getId(),
					'_omniform_user_ip' => $_SERVER['REMOTE_ADDR'],
					'_wp_http_referer'  => $request->get_param( '_wp_http_referer' ),
				),
			),
			true
		);

		if ( is_wp_error( $submission_id ) ) {
			return rest_ensure_response( $submission_id );
		}

		// Incremement form submissions.
		$submission_count = get_post_meta( $form->getId(), '_omniform_submissions', true );
		update_post_meta( $form->getId(), '_omniform_submissions', (int) $submission_count + 1 );

		if ( $request->has_param( '_omniform_redirect' ) ) {
			wp_safe_redirect( add_query_arg( 'success', true, $request->get_param( '_omniform_redirect' ) ) );
			exit;
		}

		$response = array(
			'status'  => 201,
			'message' => 'submission_created',
		);

		return rest_ensure_response(
			new \WP_HTTP_Response( $response, $response['status'] )
		);
	}
}
