<?php
/**
 * The FormsController class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\Api;

use OmniForm\Plugin\ResponseFactory;

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
			/** @var \OmniForm\Plugin\Form */ // phpcs:ignore
			$form = omniform()->get( \OmniForm\Plugin\FormFactory::class )
				->create_with_id( absint( $request->get_param( 'id' ) ) );
		} catch ( \Exception $e ) {
			return new \WP_Error(
				'omniform_not_found',
				esc_html( $e->getMessage() ),
				array( 'status' => 404 )
			);
		}

		if ( ! $form->is_published() ) {
			return new \WP_Error(
				'omniform_not_published',
				esc_html__( 'The form is not published.', 'omniform' ),
				array( 'status' => 400 )
			);
		}

		// Validate the form.
		$form->set_request_params( $request->get_params() );
		$errors = $form->validate();

		if ( ! empty( $errors ) ) {
			$api_response = array(
				'status'         => 400,
				'message'        => 'validation_failed',
				'invalid_fields' => $errors,
			);

			if ( $form->is_published() ) {
				omniform()->get( \OmniForm\Analytics\AnalyticsManager::class )->record_submission_failure( $form->get_id() );
			}

			return rest_ensure_response(
				new \WP_HTTP_Response( $api_response, $api_response['status'] )
			);
		}

		/** @var \OmniForm\Plugin\Response */ // phpcs:ignore
		$response = omniform()->get( ResponseFactory::class )->create_with_form( $form );

		$response_id = wp_insert_post(
			array(
				'post_title'   => wp_generate_uuid4(),
				'post_content' => addslashes( wp_json_encode( $response, JSON_UNESCAPED_UNICODE ) ),
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
		 * @param \OmniForm\Plugin\Response $response The response instance.
		 * @param \OmniForm\Plugin\Form $form The form instance.
		 */
		do_action( 'omniform_response_created', $response, $form );

		$api_response = array(
			'status'  => 201,
			'message' => 'response_created',
		);

		return rest_ensure_response(
			new \WP_HTTP_Response( $api_response, $api_response['status'] )
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

	/**
	 * Prepares a single post for create or update.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return stdClass|WP_Error Post object or WP_Error.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_post = parent::prepare_item_for_database( $request );

		$form_types_manager = omniform()->get( \OmniForm\FormTypes\FormTypesManager::class );

		if ( isset( $request['omniform_type'] ) ) {
			$prepared_post->tax_input['omniform_type'] = $form_types_manager->validate_form_type( $request['omniform_type'] );
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
		$form_types_manager = omniform()->get( \OmniForm\FormTypes\FormTypesManager::class );

		// Define the schema for the omniform_type field.
		$schema['properties']['omniform_type'] = array(
			'description' => __( 'omniform_type', 'omniform' ),
			'type'        => 'string',
			'enum'        => array_column( $form_types_manager->get_form_types(), 'type' ),
			'context'     => array( 'view', 'edit', 'embed' ),
		);

		return $schema;
	}

	/**
	 * Adds the values from additional fields to a data object.
	 *
	 * @param array           $response_data Prepared response array.
	 * @param WP_REST_Request $request       Full details about the request.
	 *
	 * @return array Modified data object with additional fields.
	 */
	protected function add_additional_fields_to_object( $response_data, $request ) {
		$form_types_manager = omniform()->get( \OmniForm\FormTypes\FormTypesManager::class );

		$form_type_terms = get_the_terms( $response_data['id'], 'omniform_type' );

		if ( ! is_wp_error( $form_type_terms ) && false !== $form_type_terms ) {
			$response_data['omniform_type'] = $form_types_manager->validate_form_type( $form_type_terms[0]->slug );
		} else {
			$response_data['omniform_type'] = $form_types_manager->get_default_form_type();
		}

		return $response_data;
	}
}
