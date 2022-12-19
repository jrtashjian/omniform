<?php
/**
 * The FieldInput block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The FieldInput block class.
 */
class FieldInput extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		if ( $this->isHiddenInput() ) {
			return sprintf(
				'<input type="hidden" %s />',
				$this->getControlName() . $this->getControlValue()
			);
		}

		return parent::render();
	}

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function renderControl() {
		// $form_ingestion = omniform()->get( FormIngestionEngine::class );

		// if ( $form_ingestion->formValue( $this->field_name ) && 'hidden' !== $this->attributes['fieldType'] ) {
		// $field_attributes['value'] = esc_attr( $form_ingestion->formValue( $this->field_name ) );
		// }

		// // Call WordPress functions for hidden inputs.
		// if ( false !== strpos( $field_attributes['value'], '{{' ) ) {
		// $fn = str_replace( array( '{', '}' ), '', $field_attributes['value'] );
		// $field_attributes['value'] = $fn();
		// }

		// if ( in_array( $this->attributes['fieldType'], array( 'checkbox', 'radio' ) ) ) {
		// unset( $field_attributes['placeholder'] );
		// $field_attributes['value'] = esc_attr( $this->field_name );

		// if ( ! empty( $form_ingestion->formValue( $this->field_name ) ) ) {
		// $field_attributes['checked'] = 'checked';
		// }
		// }

		return sprintf(
			'<input class="omniform-field-control" type="%s" %s />',
			esc_attr( $this->getBlockAttribute( 'fieldType' ) ),
			$this->getControlAttributes()
		);
	}

	/**
	 * Determine if the field type is a text input.
	 *
	 * @return bool
	 */
	protected function isTextInput() {
		return in_array(
			$this->getBlockAttribute( 'fieldType' ),
			array( 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' )
		);
	}

	/**
	 * Determine if the field type is a checbox or radio.
	 *
	 * @return bool
	 */
	protected function isCheckedInput() {
		return in_array(
			$this->getBlockAttribute( 'fieldType' ),
			array( 'checkbox', 'radio' )
		);
	}

	/**
	 * Determine if the field type is a hidden input.
	 *
	 * @return bool
	 */
	protected function isHiddenInput() {
		return 'hidden' === $this->getBlockAttribute( 'fieldType' );
	}

	/**
	 * The form control's name attribute.
	 *
	 * @return string
	 */
	protected function getControlName() {
		$name = parent::getControlName();

		if ( 'radio' === $this->getBlockAttribute( 'fieldType' ) && $this->isGrouped() ) {
			$name = $this->getBlockContext( 'omniform/fieldGroupName' );
		}

		return $name;
	}

}
