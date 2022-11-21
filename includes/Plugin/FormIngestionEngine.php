<?php
/**
 * The FormIngestionEngine class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\Plugin;

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
	protected $nonce_action = 'inquirywp_form_submission_';

	/**
	 * The form's nonce name.
	 *
	 * @var string
	 */
	protected $nonce_name = '_wpnonce';

	public function setFormId( $id ) {
		$this->id = $id;
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

	public function willProcess() {
		return $this->verifyNonce();
	}

	protected function registerFormData( $form_data ) {
		$this->form_data = $form_data;
	}

	public function formValue( $field_name ) {
		return empty( $this->form_data[ $field_name ] ) ? '' : $this->form_data[ $field_name ];
	}

	public function resetFormData() {
		$this->form_data = array();
	}
}
