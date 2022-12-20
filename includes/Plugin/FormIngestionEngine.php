<?php
/**
 * The FormIngestionEngine class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

/**
 * The FormIngestionEngine class.
 */
class FormIngestionEngine {
	/**
	 * The form id.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The submitted form data.
	 *
	 * @var array
	 */
	protected $form_data = array();

	/**
	 * The form's nonce action.
	 *
	 * @var string
	 */
	protected $nonce_action = 'omniform_submission_';

	/**
	 * The form's nonce name.
	 *
	 * @var string
	 */
	protected $nonce_name = '_wpnonce';

	public function setFormId( $id ) {
		$this->id = $id;
	}

	public function getFormId() {
		return $this->id;
	}

	public function getNonceField() {
		return wp_nonce_field( $this->nonce_action . $this->id, $this->nonce_name, true, false );
	}

	protected function verifyNonce() {
		if ( ! empty( $this->form_data ) ) {
			return true;
		}

		if (
			isset( $_POST[ $this->nonce_name ] ) &&
			wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action . $this->id )
		) {
			$this->registerFormData( $_POST );
			return true;
		}

		return false;
	}

	// https://stackoverflow.com/a/19277141
	// MAKE IT A BLOCK VARIATION OF field-input
	public function getHoneypotField() {
		$name        = esc_attr( 'honeypot' );
		$nonce_field = ''; // '<input id="' . $name . '" name="' . $name . '" value="" />';

		return $nonce_field;
	}

	public function willProcess() {
		return $this->verifyNonce();
	}

	protected function registerFormData( $form_data ) {
		$this->form_data = $form_data;
	}

	public function formValue( $key, $default = null ) {
		$key    = is_array( $key ) ? array_filter( $key ) : (array) $key;
		$target = $this->form_data;

		foreach ( $key as $segment ) {
			if ( is_null( $segment ) ) {
				return $target;
			}

			if ( is_array( $target ) && array_key_exists( $segment, $target ) ) {
				$target = $target[ $segment ];
			} else {
				return $default;
			}
		}

		return $target;
	}

	public function fieldError( $field_name ) {
		return $this->willProcess() && empty( $this->formValue( $field_name ) ) ? ' MISSING ' : null;
	}

	public function resetFormData() {
		$this->form_data = array();
	}

	public function savePostData() {
		$post_data = array_filter(
			$this->form_data,
			function( $key ) {
				return ! in_array( $key, array( '_wpnonce', '_wp_http_referer' ) );
			},
			ARRAY_FILTER_USE_KEY
		);

		wp_insert_post(
			array(
				'post_title'   => wp_generate_uuid4(),
				'post_content' => wp_json_encode( $post_data ),
				'post_type'    => 'omniform_submission',
				'post_status'  => 'publish',
				'meta_input'   => array(
					'omniform_id' => $this->id,
				),
			)
		);
	}
}
