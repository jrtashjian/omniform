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
	protected $id;

	/**
	 * Form post object.
	 *
	 * @var \WP_Post
	 */
	protected $post_data;

	/**
	 * Validator object.
	 *
	 * @var Validation\Validator
	 */
	protected $validator;

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
	 * Retrieve Form instance.
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return Form|false Form object, false otherwise.
	 */
	public function get_instance( $form_id ) {
		$form_id = (int) $form_id;
		if ( ! $form_id ) {
			return false;
		}

		$_form = get_post( $form_id );

		if ( ! $_form || 'omniform' !== $_form->post_type ) {
			return false;
		}

		$this->id        = $_form->ID;
		$this->post_data = $_form;

		$this->validator = new Validation\Validator();

		return $this;
	}

	/**
	 * Return the form's ID.
	 *
	 * @return number
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
		return 'publish' === $this->post_data->post_status;
	}

	/**
	 * The form's private status.
	 *
	 * @return bool
	 */
	public function is_private() {
		return ! empty( $this->post_data->post_password );
	}

	/**
	 * The form's post_title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->post_data->post_title;
	}

	/**
	 * The form's post_content.
	 *
	 * @return string
	 */
	public function get_content() {
		return $this->post_data->post_content;
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
	 * @param \WP_REST_Request $request Full details about the request.
	 */
	public function validate( \WP_REST_Request $request ) {
		$request_params = new \OmniForm\Dependencies\Dflydev\DotAccessData\Data( $request->get_params() );

		$this->register_fields();

		try {
			$this->validator->assert( $request_params->export() );
		} catch ( Validation\Exceptions\NestedValidationException $exception ) {
			return $exception->getMessages();
		}
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
				esc_html( $value )
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
			$message[] = $label . ': ' . $value;
		}

		$message[] = '';
		$message[] = '---';
		$message[] = sprintf( 'This email was sent to notify you of a response made through the contact form on %s.', get_bloginfo( 'url' ) );
		$message[] = 'Time: ' . $response_data['response']->post_date;
		$message[] = 'IP Address: ' . $_SERVER['REMOTE_ADDR'];
		$message[] = 'Form URL: ' . get_post_meta( $response_id, '_wp_http_referer', true );

		return implode( "\n", $message );
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
