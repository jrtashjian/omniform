<?php
/**
 * The Form class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\BlockLibrary\Blocks\BaseFieldBlock;

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
	public static function getInstance( $form_id ) {
		$form_id = (int) $form_id;
		if ( ! $form_id ) {
			return false;
		}

		$_form = get_post( $form_id );

		if ( ! $_form || 'omniform' !== $_form->post_type ) {
			return false;
		}

		omniform()->singleton(
			self::class,
			function ( $app ) use ( $_form ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
				return new Form( $_form );
			}
		);

		return omniform()->get( self::class );
	}

	/**
	 * Constructor.
	 *
	 * @param WP_Post|null $post Post object.
	 */
	public function __construct( $post = null ) {
		if ( $post ) {
			$this->id        = $post->ID;
			$this->post_data = $post;
		}
	}

	/**
	 * Set the form's ID.
	 *
	 * @param number $id The form's ID.
	 */
	public function setId( $id ) {
		$this->id = $id;
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
		$data = array(
			'control_name'  => $field->getControlName(),
			'control_label' => $field->getBlockAttribute( 'fieldLabel' ),
			'is_required'   => $field->isRequired(),
		);

		$control_name = str_replace( array( '[', ']' ), array( '.', '' ), $field->getControlName() );

		$this->fields[ $control_name ] = $data;
	}

	/**
	 * Parses blocks out of the form's `post_content`.
	 */
	protected function registerFields() {
		do_blocks( $this->getContent() );
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
	 * Response to message.
	 *
	 * @param int $submission_id Submission ID.
	 *
	 * @return string|false The message, false otherwise.
	 */
	public function response_email_message( $submission_id ) {
		$submission_id = (int) $submission_id;
		if ( ! $submission_id ) {
			return false;
		}

		$_submission = get_post( $submission_id );

		if ( ! $_submission || 'omniform_response' !== $_submission->post_type ) {
			return false;
		}

		$this->registerFields();

		$message = array();

		$submission_data = $this->flatten( json_decode( $_submission->post_content, true ) );

		foreach ( $this->fields as $key => $def ) {
			$message[] = $def['control_label'] . ': ' . $submission_data[ $key ];
		}

		$message[] = '';
		$message[] = '---';
		$message[] = sprintf( 'This email was sent to notify you of a submission made through the contact form on %s.', get_bloginfo( 'url' ) );
		$message[] = 'Time: ' . $_submission->post_date;
		$message[] = 'IP Address: ' . $_SERVER['REMOTE_ADDR'];
		$message[] = 'Form URL: ' . get_post_meta( $submission_id, '_wp_http_referer', true );

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
