<?php
/**
 * The FieldTextarea block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

use InquiryWP\Plugin\FormIngestionEngine;

/**
 * The FieldTextarea block class.
 */
class FieldTextarea extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block default content.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content ) {
		parent::renderBlock( $attributes, $content );

		if ( empty( $this->renderFieldLabel() ) ) {
			return '';
		}

		$form_ingestion = inquirywp()->get( FormIngestionEngine::class );

		$field_attributes = array(
			'id'          => esc_attr( $this->field_name ),
			'name'        => esc_attr( $this->field_name ),
			'placeholder' => empty( $attributes['placeholder'] ) ? '' : esc_attr( $attributes['placeholder'] ),
		);

		$field_control = sprintf(
			'<textarea class="inquirywp-field-control" rows="10" %s>%s</textarea>',
			str_replace(
				array( '=', '&' ),
				array( '="', '" ' ),
				http_build_query( $field_attributes )
			) . '"',
			esc_textarea( $form_ingestion->formValue( $this->field_name ) )
		);

		return sprintf(
			'<div class="wp-block-inquirywp-%1$s inquirywp-%1$s">%2$s</div>',
			esc_attr( $this->blockTypeName() ),
			$this->renderFieldLabel() . $field_control . $this->renderFieldHelpText() . $content
		);
	}
}
