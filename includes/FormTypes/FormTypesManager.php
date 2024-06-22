<?php
/**
 * The FormTypesManager class.
 *
 * @package OmniForm
 */

namespace OmniForm\FormTypes;

/**
 * The FormTypesManager class.
 */
class FormTypesManager {
	/**
	 * The form types.
	 *
	 * @var array
	 */
	protected $form_types = array();

	/**
	 * The default form type.
	 *
	 * @var string
	 */
	protected $default_form_type;

	/**
	 * FormTypesManager constructor.
	 */
	public function __construct() {
		$this->form_types = array(
			array(
				'type'        => 'standard',
				'label'       => __( 'Standard', 'omniform' ),
				'description' => __( 'A standard form.', 'omniform' ),
				'icon'        => '',
			),
		);

		// Set up the default form type.
		$this->default_form_type = 'standard';
	}

	/**
	 * Add a form type.
	 *
	 * @param array $form_type The form type.
	 */
	public function add_form_type( $form_type ) {
		$this->form_types[] = $form_type;
	}

	/**
	 * Get the form types.
	 *
	 * @return array The form types.
	 */
	public function get_form_types() {
		return $this->form_types;
	}

	/**
	 * Get the default form type.
	 *
	 * @return string The default form type.
	 */
	public function get_default_form_type() {
		return $this->default_form_type;
	}

	/**
	 * Validate the form type.
	 *
	 * @param string $form_type The form type.
	 * @return string The validated form type.
	 */
	public function validate_form_type( $form_type ) {
		$form_types = array_column( $this->form_types, 'type' );

		if ( in_array( $form_type, $form_types, true ) ) {
			return $form_type;
		}

		return $this->default_form_type;
	}
}
