<?php
/**
 * The Form class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\BlockLibrary\Blocks\BaseFieldBlock;
use OmniForm\BlockLibrary\Blocks\SelectGroup;
use OmniForm\BlockLibrary\Blocks\SelectOption;

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

	protected $fields = array();

	/**
	 * Retrieve Form instance.
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return Form|false Form object, false otherwise.
	 */
	public function getInstance( $form_id ) {
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

		return $this;
	}

	/**
	 * Return the form's ID.
	 *
	 * @return number
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * The form's publish status.
	 *
	 * @return bool
	 */
	public function isPublished() {
		return 'publish' === $this->post_data->post_status;
	}

	/**
	 * The form's private status.
	 *
	 * @return bool
	 */
	public function isPrivate() {
		return ! empty( $this->post_data->post_password );
	}

	/**
	 * The form's post_title.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->post_data->post_title;
	}

	/**
	 * The form's post_content.
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->post_data->post_content;
	}

	/**
	 * Add a parsed field block to the form for processing.
	 *
	 * @param BaseFieldBlock $field The parsed field block.
	 */
	public function addField( BaseFieldBlock $field ) {
		$rules = array(
			'is_required' => $field->isRequired(),
		);

		$control_name = implode(
			'.',
			array_filter(
				array(
					$field->getFieldGroupName(),
					$field->isGrouped() && in_array( $field->getBlockAttribute( 'fieldType' ), array( 'radio', 'checkbox' ), true )
						? null
						: $field->getFieldName(),
				)
			)
		);

		$this->fields[ $control_name ] = $rules;
	}

	/**
	 * Parses blocks out of the form's `post_content`.
	 */
	protected function registerFields() {
		add_filter( 'render_block', array( $this, 'hookRenderBlock' ), 10, 3 );
		do_blocks( $this->getContent() );
		remove_filter( 'render_block', array( $this, 'hookRenderBlock' ), 10, 3 );
	}

	/**
	 * Filters the content of a single block .
	 *
	 * @param string   $block_content The block content .
	 * @param array    $parsed_block The full block, including name and attributes .
	 * @param WP_Block $wp_block The block instance.
	 */
	public function hookRenderBlock( $block_content, $parsed_block, $wp_block ) {
		if ( empty( $wp_block->block_type->render_callback ) || ! is_array( $wp_block->block_type->render_callback ) ) {
			return $block_content;
		}

		if (
			! $wp_block->block_type->render_callback[0] instanceof BaseFieldBlock ||
			$wp_block->block_type->render_callback[0] instanceof SelectGroup ||
			$wp_block->block_type->render_callback[0] instanceof SelectOption
		) {
			return $block_content;
		}

		$this->addField( $wp_block->block_type->render_callback[0] );

		return $block_content;
	}

	/**
	 * Validate the form.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 */
	public function validate( \WP_REST_Request $request ) {
		$errors = array();

		$this->registerFields();

		$params_flattened = $this->flatten( $request->get_params() );

		foreach ( $this->fields as $key => $def ) {
			if (
				! empty( $def['is_required'] ) &&
				( ! array_key_exists( $key, $params_flattened ) || empty( $params_flattened[ $key ] ) )
			) {
				$errors[] = array(
					'message'       => 'This fields is required',
					'control_name'  => esc_attr( $def['control_name'] ),
					'control_label' => esc_attr( $def['control_label'] ),
				);
			}
		}

		return $errors;
	}

	/**
	 * Response to text content.
	 *
	 * @param int $response_id Submission ID.
	 *
	 * @return string|false The message, false otherwise.
	 */
	public function response_text_content( $response_id ) {
		$response_id = (int) $response_id;
		if ( ! $response_id ) {
			return false;
		}

		$_response = get_post( $response_id );

		if ( ! $_response || 'omniform_response' !== $_response->post_type ) {
			return false;
		}

		$this->registerFields();

		$message = array();

		$response_data = $this->flatten( json_decode( $_response->post_content, true ) );

		foreach ( $this->fields as $key => $def ) {
			$value     = array_key_exists( $key, $response_data ) ? $response_data[ $key ] : '';
			$message[] = sprintf(
				'<strong>%s:</strong> %s',
				esc_attr( $key ),
				esc_attr( $value )
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
		$response_id = (int) $response_id;
		if ( ! $response_id ) {
			return false;
		}

		$_response = get_post( $response_id );

		if ( ! $_response || 'omniform_response' !== $_response->post_type ) {
			return false;
		}

		$this->registerFields();

		$message = array();

		$response_data = $this->flatten( json_decode( $_response->post_content, true ) );

		foreach ( $this->fields as $key => $def ) {
			$value     = array_key_exists( $key, $response_data ) ? $response_data[ $key ] : '';
			$message[] = $def['control_label'] . ': ' . $value;
		}

		$message[] = '';
		$message[] = '---';
		$message[] = sprintf( 'This email was sent to notify you of a response made through the contact form on %s.', get_bloginfo( 'url' ) );
		$message[] = 'Time: ' . $_response->post_date;
		$message[] = 'IP Address: ' . $_SERVER['REMOTE_ADDR'];
		$message[] = 'Form URL: ' . get_post_meta( $response_id, '_wp_http_referer', true );

		return implode( "\n", $message );
	}

	/**
	 * Flatten a multi-dimensional associative array with dots.
	 *
	 * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/Collections/Arr.php#L102-L122
	 *
	 * @param  iterable $array The array to flatten.
	 * @param  string   $prepend The existing chain of parents.
	 *
	 * @return array
	 */
	public function flatten( $array, $prepend = '' ) {
		$results = array();

		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) && ! empty( $value ) ) {
				$results = array_merge( $results, $this->flatten( $value, $prepend . $key . '.' ) );
			} else {
				$results[ $prepend . $key ] = $value;
			}
		}

		return $results;
	}
}
