<?php
/**
 * The FormsController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\Api;

use OmniForm\Analytics\AnalyticsManager;
use OmniForm\FormTypes\FormTypesManager;
use OmniForm\Plugin\FormSubmitResult;
use OmniForm\Plugin\FormSubmitter;

/**
 * The FormsController class.
 */
class FormsController extends \WP_REST_Posts_Controller {
	/**
	 * Registers the routes for the objects of the controller.
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
	 * @param \WP_REST_Request $_request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_response_permissions_check( \WP_REST_Request $_request ) {
		return rest_cookie_check_errors( null );
	}

	/**
	 * Checks if a given request has access to read forms.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		return $this->manage_forms_permissions_check();
	}

	/**
	 * Checks if a given request has access to read a form.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		return $this->manage_forms_permissions_check();
	}

	/**
	 * Shared capability check for reading form resources.
	 *
	 * @return true|\WP_Error
	 */
	private function manage_forms_permissions_check() {
		if ( current_user_can( 'edit_pages' ) ) {
			return true;
		}

		return new \WP_Error(
			'rest_cannot_manage_forms',
			__( 'Sorry, you are not allowed to access the forms on this site.', 'omniform' ),
			array(
				'status' => rest_authorization_required_code(),
			)
		);
	}

	/**
	 * Creates a single response via the domain FormSubmitter path.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_response( \WP_REST_Request $request ) {
		$form_id = absint( $request->get_param( 'id' ) );

		$analytics = omniform()->container()->get( AnalyticsManager::class );

		// Rate limit: up to 10 submissions per hour, per unique visitor, per form.
		if ( $analytics->get_recent_submissions_count( $form_id, 3600 ) >= 10 ) {
			return new \WP_Error(
				'rate_limit_exceeded',
				esc_html__( 'Too many form submissions. Please try again later.', 'omniform' ),
				array( 'status' => 429 )
			);
		}

		$user_ip = filter_var( $_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP );
		$referer = $request->get_param( '_wp_http_referer' );

		$result = omniform()->container()->get( FormSubmitter::class )->submit(
			$form_id,
			$request->get_params(),
			$request->get_file_params(),
			array(
				'user_ip' => $user_ip ? $user_ip : '',
				'referer' => is_string( $referer ) ? sanitize_url( $referer ) : '',
			)
		);

		return $this->rest_response_from_submit_result( $result, $form_id, $analytics );
	}

	/**
	 * Map a FormSubmitResult to a REST response and record analytics.
	 *
	 * @param FormSubmitResult $result    Domain submit outcome.
	 * @param int              $form_id   Form post ID.
	 * @param AnalyticsManager $analytics Analytics recorder.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	private function rest_response_from_submit_result(
		FormSubmitResult $result,
		int $form_id,
		AnalyticsManager $analytics
	) {
		if ( $result->is_success() ) {
			$analytics->record_submission_success( $form_id );

			return rest_ensure_response(
				new \WP_HTTP_Response(
					array(
						'status'  => 201,
						'message' => 'response_created',
					),
					201
				)
			);
		}

		if ( $result->is_validation_failure() ) {
			$analytics->record_submission_failure( $form_id );

			return rest_ensure_response(
				new \WP_HTTP_Response(
					array(
						'status'         => 400,
						'message'        => 'validation_failed',
						'invalid_fields' => $result->invalid_fields(),
					),
					400
				)
			);
		}

		return $this->rest_error_from_submit_result( $result );
	}

	/**
	 * Map non-validation submit failures to WP_Error.
	 *
	 * @param FormSubmitResult $result Domain submit outcome.
	 */
	private function rest_error_from_submit_result( FormSubmitResult $result ): \WP_Error {
		$code    = $result->error_code() ?? 'submit_failed';
		$message = $result->error_message() ?? __( 'Form submission failed.', 'omniform' );

		$error_map = array(
			'form_not_found'     => array( 'omniform_not_found', 404 ),
			'form_not_published' => array( 'omniform_not_published', 400 ),
		);

		[ $rest_code, $status ] = $error_map[ $code ] ?? array( 'omniform_submit_failed', 500 );

		return new \WP_Error(
			$rest_code,
			esc_html( $message ),
			array( 'status' => $status )
		);
	}

	/**
	 * Prepares a single post for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \stdClass|\WP_Error Post object or WP_Error.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_post = parent::prepare_item_for_database( $request );

		if ( isset( $request['omniform_type'] ) ) {
			$prepared_post->tax_input['omniform_type'] = $this->form_types_manager()->validate_form_type( $request['omniform_type'] );
		}

		/** This filter is documented in wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php */
		return apply_filters( "rest_pre_insert_{$this->post_type}", $prepared_post, $request ); // phpcs:ignore WordPress.NamingConventions
	}

	/**
	 * Adds the schema from additional fields to a schema array.
	 *
	 * @param array $schema Schema array.
	 *
	 * @return array Modified Schema array.
	 */
	protected function add_additional_fields_schema( $schema ) {
		$schema['properties']['omniform_type'] = array(
			'description' => __( 'omniform_type', 'omniform' ),
			'type'        => 'string',
			'enum'        => array_column( $this->form_types_manager()->get_form_types(), 'type' ),
			'context'     => array( 'view', 'edit', 'embed' ),
		);

		return $schema;
	}

	/**
	 * Adds the values from additional fields to a data object.
	 *
	 * @param array            $response_data Prepared response array.
	 * @param \WP_REST_Request $request       Full details about the request.
	 *
	 * @return array Modified data object with additional fields.
	 */
	protected function add_additional_fields_to_object( $response_data, $request ) {
		$form_types_manager = $this->form_types_manager();
		$form_type_terms    = get_the_terms( $response_data['id'], 'omniform_type' );

		$response_data['omniform_type'] = ( ! is_wp_error( $form_type_terms ) && false !== $form_type_terms )
			? $form_types_manager->validate_form_type( $form_type_terms[0]->slug )
			: $form_types_manager->get_default_form_type();

		return $response_data;
	}

	/**
	 * Resolve the form types manager from the container.
	 */
	private function form_types_manager(): FormTypesManager {
		return omniform()->container()->get( FormTypesManager::class );
	}
}
