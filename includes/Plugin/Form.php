<?php
/**
 * The Form class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\BlockLibrary\Blocks\BaseControlBlock;
use OmniForm\BlockLibrary\Blocks\Fieldset;
use OmniForm\BlockLibrary\Blocks\Input;
use OmniForm\BlockLibrary\Blocks\Select;
use OmniForm\BlockLibrary\Blocks\Textarea;
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
	 * The form's required label.
	 *
	 * @var string
	 */
	protected $required_label;

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
		// For backward compatibility, return only labels.
		$labels = array();
		foreach ( $this->fields as $key => $field_data ) {
			if ( is_array( $field_data ) && isset( $field_data['label'] ) ) {
				$labels[ $key ] = sanitize_text_field( $field_data['label'] );
			} elseif ( is_string( $field_data ) ) {
				// Handle legacy format where fields were stored as strings.
				$labels[ $key ] = sanitize_text_field( $field_data );
			}
		}
		return $labels;
	}

	/**
	 * The form's field groups.
	 *
	 * @return array
	 */
	public function get_groups() {
		// Sanitize group labels as they may be displayed.
		return array_map( 'sanitize_text_field', $this->groups );
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
	 * The form's required label.
	 *
	 * @return string
	 */
	public function get_required_label() {
		$required_label = $this->required_label ?? get_post_meta( $this->get_id(), 'required_label', true );
		return empty( $required_label ) ? '*' : $required_label;
	}

	/**
	 * Set the form's required label.
	 *
	 * @param string $required_label The form's required label.
	 */
	public function set_required_label( $required_label ) {
		$this->required_label = $required_label;
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
	 * The form's notify email.
	 *
	 * @return string
	 */
	public function get_notify_email() {
		$notify_email = get_post_meta( $this->get_id(), 'notify_email', true );

		return empty( $notify_email )
			? get_option( 'admin_email' )
			: $notify_email;
	}

	/**
	 * The form's notify email subject.
	 *
	 * @return string
	 */
	public function get_notify_email_subject() {
		$notify_email_subject = get_post_meta( $this->get_id(), 'notify_email_subject', true );

		return empty( $notify_email_subject )
			// translators: %1$s represents the blog name, %2$s represents the form title.
			? esc_attr( sprintf( __( 'New Response: %1$s - %2$s', 'omniform' ), get_option( 'blogname' ), $this->get_title() ) )
			: esc_attr( $notify_email_subject );
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

			// Skip if the control name is empty.
			if ( empty( $control_name_parts ) ) {
				return $block_content;
			}

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

			// Determine field type based on block type.
			$field_type = $this->determine_field_type( $block );

			$this->fields[ $flat_control_name ] = array(
				'label' => $block->is_grouped() && in_array( $block->get_block_attribute( 'fieldType' ), array( 'radio', 'checkbox' ), true )
					? $block->get_field_group_label()
					: $block->get_field_label(),
				'type'  => $field_type,
			);
		}

		return $block_content;
	}

	/**
	 * Determine the field type from the block instance.
	 *
	 * @param BaseControlBlock $block The block instance.
	 *
	 * @return string The field type.
	 */
	protected function determine_field_type( BaseControlBlock $block ) {
		// For Input blocks, use the fieldType attribute.
		if ( $block instanceof Input ) {
			return $block->get_block_attribute( 'fieldType' ) ?? 'text';
		}

		// For Textarea blocks.
		if ( $block instanceof Textarea ) {
			return 'textarea';
		}

		// For Select blocks.
		if ( $block instanceof Select ) {
			return 'select';
		}

		// Default to text for other control blocks.
		return 'text';
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
	 * Get the field type for a given field key.
	 *
	 * @param string $key The field key.
	 *
	 * @return string The field type, defaulting to 'text' if not found.
	 */
	protected function get_field_type( $key ) {
		// Handle nested field keys (e.g., "group.field").
		if ( isset( $this->fields[ $key ] ) && is_array( $this->fields[ $key ] ) ) {
			return $this->fields[ $key ]['type'] ?? 'text';
		}

		return 'text';
	}

	/**
	 * Sanitize a field value based on its type.
	 *
	 * @param mixed  $value The value to sanitize.
	 * @param string $type  The field type.
	 *
	 * @return mixed The sanitized value.
	 */
	private function sanitize_field_value( $value, $type ) {
		switch ( $type ) {
			case 'email':
				return sanitize_email( $value );
			case 'url':
				return esc_url_raw( $value );
			case 'number':
			case 'range':
				return is_numeric( $value ) ? (float) $value : 0;
			case 'textarea':
				return sanitize_textarea_field( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Sanitizes an array of data.
	 *
	 * @param mixed  $data   The data to sanitize.
	 * @param string $prefix The prefix for nested keys.
	 *
	 * @return array
	 */
	public function sanitize_array( $data, $prefix = '' ) {
		if ( ! is_array( $data ) ) {
			return $this->sanitize_field_value( $data, 'text' );
		}

		return array_map(
			function ( $value, $key ) use ( $prefix ) {
				// Build the full field path for nested fields.
				$full_key   = $prefix ? $prefix . '.' . $key : $key;
				$field_type = $this->get_field_type( $full_key );

				if ( is_array( $value ) ) {
					return $this->sanitize_array( $value, $full_key );
				}
				return $this->sanitize_field_value( $value, $field_type );
			},
			$data,
			array_keys( $data )
		);
	}
}
