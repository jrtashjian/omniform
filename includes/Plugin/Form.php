<?php
/**
 * The Form class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\BlockLibrary\Blocks\BaseControlBlock;
use OmniForm\BlockLibrary\Blocks\Fieldset;
use OmniForm\Dependencies\Dflydev\DotAccessData;
use OmniForm\Dependencies\Respect\Validation;

/**
 * The Form class.
 */
class Form {
	/**
	 * Form ID.
	 *
	 * @var number
	 */
	protected $id = 0;

	/**
	 * Form post status.
	 *
	 * @var string
	 */
	protected $status = 'publish';

	/**
	 * Form title.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Form content.
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Request params.
	 *
	 * @var array
	 */
	protected $request_params;

	/**
	 * Validator object.
	 *
	 * @var Validation\Validator
	 */
	protected $validator;

	/**
	 * Validation messages.
	 *
	 * @var array
	 */
	protected $validation_messages;

	/**
	 * Validation passed.
	 *
	 * @var bool
	 */
	protected $validation_passed = null;

	/**
	 * The form's fields.
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * The form's field groups.
	 *
	 * @var array
	 */
	protected $groups = array();

	/**
	 * Form constructor.
	 *
	 * @param Validation\Validator $validator Validator object.
	 */
	public function __construct( Validation\Validator $validator ) {
		$this->validator = $validator;
	}

	/**
	 * Set the form's content.
	 *
	 * @param string $content The form content.
	 */
	public function set_content( $content ) {
		$this->content = $content;
	}

	/**
	 * Set the form's post data.
	 *
	 * @param \WP_Post $post_data The form post data.
	 */
	public function set_post_data( \WP_Post $post_data ) {
		$this->id      = $post_data->ID;
		$this->status  = $post_data->post_status;
		$this->title   = $post_data->post_title;
		$this->content = $post_data->post_content;
	}

	/**
	 * Set the form's request params.
	 *
	 * @param array $request_params The form request params.
	 */
	public function set_request_params( $request_params ) {
		$this->request_params = $this->sanitize_array( $request_params );
	}

	/**
	 * Get the form's request params.
	 *
	 * @return array
	 */
	public function get_request_params() {
		return $this->request_params;
	}

	/**
	 * Return the form's ID.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * The form's publish status.
	 *
	 * @return bool
	 */
	public function is_published() {
		return 'publish' === $this->status;
	}

	/**
	 * The form's password protection status.
	 *
	 * @return bool
	 */
	public function is_password_protected() {
		return post_password_required( $this->get_id() );
	}

	/**
	 * The form's post_title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * The form's post_content.
	 *
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * The form's fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * The form's field groups.
	 *
	 * @return array
	 */
	public function get_groups() {
		return $this->groups;
	}

	/**
	 * The form's type.
	 *
	 * @return string
	 */
	public function get_type() {
		$type_terms = get_the_terms( $this->get_id(), 'omniform_type' );

		return ! is_wp_error( $type_terms ) && false !== $type_terms
			? $type_terms[0]->slug
			: 'standard';
	}

	/**
	 * The form's submit method.
	 *
	 * @return string
	 */
	public function get_submit_method() {
		$submit_method = get_post_meta( $this->get_id(), 'submit_method', true );

		return empty( $submit_method ) || 'standard' === $this->get_type()
			? 'POST'
			: $submit_method;
	}

	/**
	 * The form's submit action.
	 *
	 * @return string
	 */
	public function get_submit_action() {
		$submit_action = get_post_meta( $this->get_id(), 'submit_action', true );

		return empty( $submit_action ) || 'standard' === $this->get_type()
			? rest_url( 'omniform/v1/forms/' . $this->get_id() . '/responses' )
			: $submit_action;
	}

	/**
	 * Parses blocks out of the form's `post_content`.
	 */
	protected function register_fields() {
		add_filter( 'render_block', array( $this, 'hook_render_block' ), 10, 3 );
		do_blocks( $this->get_content() );
		remove_filter( 'render_block', array( $this, 'hook_render_block' ), 10, 3 );
	}

	/**
	 * Filters the content of a single block. This is used to parse the form's
	 * blocks and add them to the form's fields for processing.
	 *
	 * @param string   $block_content The block content.
	 * @param array    $parsed_block The full block, including name and attributes.
	 * @param WP_Block $wp_block The block instance.
	 */
	public function hook_render_block( $block_content, $parsed_block, $wp_block ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		if ( empty( $wp_block->block_type->render_callback ) || ! is_array( $wp_block->block_type->render_callback ) ) {
			return $block_content;
		}

		$block = $wp_block->block_type->render_callback[0];

		// If the block is a fieldset, add it to the list of groups.
		if ( $block instanceof Fieldset ) {
			if ( $block->get_block_attribute( 'isRequired' ) ) {
				$validation_rules = new Validation\Rules\NotEmpty();
				$validation_rules->setName( $block->get_field_group_label() );

				$rule = new Validation\Rules\Key( $block->get_field_group_name(), $validation_rules );
				$this->validator->addRule( $rule );
			}

			$this->groups[ $block->get_field_group_name() ] = $block->get_field_group_label();
		}

		// If the block is a control, add it to the list of fields.
		if ( $block instanceof BaseControlBlock ) {
			$control_name_parts = $block->get_control_name_parts();
			$flat_control_name  = implode( '.', $control_name_parts );

			$validation_rules = new Validation\Rules\AllOf(
				...$block->get_validation_rules()
			);
			$validation_rules->setName( $block->get_field_label() );

			$rule = new Validation\Rules\Key( $flat_control_name, $validation_rules );

			// Ensure rules are properly nested for grouped fields.
			if ( count( $control_name_parts ) > 1 ) {
				$rule = new Validation\Rules\Key(
					$control_name_parts[0],
					new Validation\Rules\Key( $control_name_parts[1], $validation_rules )
				);
			}

			if ( $block->has_validation_rules() ) {
				$this->validator->addRule( $rule );
			}

			$this->fields[ $flat_control_name ] = $block->is_grouped() && in_array( $block->get_block_attribute( 'fieldType' ), array( 'radio', 'checkbox' ), true )
				? $block->get_field_group_label()
				: $block->get_field_label();
		}

		return $block_content;
	}

	/**
	 * Validate the form.
	 *
	 * @return array|bool The validation errors, false otherwise.
	 */
	public function validate() {
		$request_params = new \OmniForm\Dependencies\Dflydev\DotAccessData\Data( $this->request_params );

		$this->register_fields();

		try {
			$this->validator->assert( $request_params->export() );
			$this->validation_passed = true;
		} catch ( Validation\Exceptions\NestedValidationException $exception ) {
			$this->validation_passed   = false;
			$this->validation_messages = $exception->getMessages();

			return $this->validation_messages;
		}
	}

	/**
	 * Get the validation messages.
	 *
	 * @return array
	 */
	public function get_validation_messages() {
		return $this->validation_messages;
	}

	/**
	 * Check if validation failed.
	 *
	 * @return bool
	 */
	public function validation_failed() {
		return false === $this->validation_passed;
	}

	/**
	 * Check if validation succeeded.
	 *
	 * @return bool
	 */
	public function validation_succeeded() {
		return true === $this->validation_passed;
	}

	/**
	 * Get the response data.
	 *
	 * @param int $response_id Submission ID.
	 *
	 * @return array|false The message, false otherwise.
	 */
	public function get_response_data( $response_id ) {
		$response_id = (int) $response_id;
		if ( ! $response_id ) {
			return false;
		}

		$_response = get_post( $response_id );

		if ( ! $_response || 'omniform_response' !== $_response->post_type ) {
			return false;
		}

		$_data = json_decode( $_response->post_content, true );

		$response_data = new \OmniForm\Dependencies\Dflydev\DotAccessData\Data( $_data['response'] ?? $_data );

		$fields = array_combine(
			array_keys( $this->flatten( $response_data->export() ) ),
			array_keys( $this->flatten( $response_data->export() ) )
		);

		if ( ! empty( $_data['fields'] ) ) {
			$fields = $_data['fields'];
		}

		return array(
			'response' => $_response,
			'content'  => $response_data,
			'fields'   => $fields,
		);
	}


	/**
	 * Response to text content.
	 *
	 * @param int $response_id Submission ID.
	 *
	 * @return string|false The message, false otherwise.
	 */
	public function response_text_content( $response_id ) {
		$response_data = $this->get_response_data( $response_id );
		if ( empty( $response_data ) ) {
			return false;
		}

		foreach ( $response_data['fields'] as $name => $label ) {
			$value     = implode( ', ', (array) $response_data['content']->get( $name, '' ) );
			$message[] = sprintf(
				'<strong>%s:</strong> %s',
				esc_html( $label ),
				wp_kses( $value, array() )
			);
		}

		return implode( '<br />', $message );
	}

	/**
	 * Response to email message.
	 *
	 * @param int $response_id Submission ID.
	 *
	 * @return string|false The message, false otherwise.
	 */
	public function response_email_message( $response_id ) {
		$response_data = $this->get_response_data( $response_id );
		if ( empty( $response_data ) ) {
			return false;
		}

		$message = array();

		foreach ( $response_data['fields'] as $name => $label ) {
			$value     = implode( ', ', (array) $response_data['content']->get( $name, '' ) );
			$message[] = $label . ': ' . wp_kses( $value, array() );
		}

		$message[] = '';
		$message[] = '---';
		/* translators: %s: Site URL. */
		$message[] = sprintf( esc_html__( 'This email was sent to notify you of a response made through the contact form on %s.', 'omniform' ), esc_url( get_bloginfo( 'url' ) ) );
		$message[] = esc_html__( 'Time: ', 'omniform' ) . $response_data['response']->post_date;
		$message[] = esc_html__( 'IP Address: ', 'omniform' ) . sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
		$message[] = esc_html__( 'Form URL: ', 'omniform' ) . esc_url( get_post_meta( $response_id, '_wp_http_referer', true ) );

		return esc_html( implode( "\n", $message ) );
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
	 * Flatten an array.
	 *
	 * @link https://github.com/dflydev/dflydev-dot-access-data/issues/16#issuecomment-699638023
	 *
	 * @param array  $data The array to flatten.
	 * @param string $path_prefix The path prefix.
	 *
	 * @return array The flattened array.
	 */
	private function flatten( array $data, string $path_prefix = '' ) {
		$ret = array();

		foreach ( $data as $key => $value ) {
			$full_key = ltrim( $path_prefix . '.' . $key, '.' );
			if ( is_array( $value ) && DotAccessData\Util::isAssoc( $value ) ) {
				$ret += $this->flatten( $value, $full_key );
			} else {
				$ret[ $full_key ] = $value;
			}
		}

		return $ret;
	}
}
