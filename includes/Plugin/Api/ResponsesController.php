<?php
/**
 * The ResponsesController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\Api;

/**
 * The ResponsesController class.
 */
class ResponsesController extends \WP_REST_Posts_Controller {
	/**
	 * Checks if a given request has access to read responses.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		if ( current_user_can( 'edit_pages' ) ) {
			return true;
		}

		return new \WP_Error(
			'rest_cannot_manage_responses',
			__( 'Sorry, you are not allowed to access the responses on this site.', 'omniform' ),
			array(
				'status' => rest_authorization_required_code(),
			)
		);
	}

	/**
	 * Checks if a given request has access to read a response.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		if ( current_user_can( 'edit_pages' ) ) {
			return true;
		}

		return new \WP_Error(
			'rest_cannot_manage_responses',
			__( 'Sorry, you are not allowed to access the responses on this site.', 'omniform' ),
			array(
				'status' => rest_authorization_required_code(),
			)
		);
	}
}
