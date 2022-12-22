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
	 * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_submission_permissions_check( \WP_REST_Request $request ) {
		return rest_cookie_check_errors( null );
	}

	/**
	 * Creates a single submission.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_submission( \WP_REST_Request $request ) {
		return rest_ensure_response( $request->get_params() );
	}
}
