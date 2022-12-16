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
	public function renderField() {
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
			esc_attr( $this->attributes['fieldType'] ),
			$this->getControlAttributes()
		);
	}
}
