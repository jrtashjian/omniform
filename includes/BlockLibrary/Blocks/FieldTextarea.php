<?php
/**
 * The FieldTextarea block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Plugin\FormIngestionEngine;

/**
 * The FieldTextarea block class.
 */
class FieldTextarea extends BaseFieldBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		parent::renderBlock( $attributes, $content, $block );

		if ( empty( $this->renderFieldLabel() ) ) {
			return '';
		}

		$form_ingestion = omniform()->get( FormIngestionEngine::class );

		$field_attributes = array(
			'id'          => esc_attr( $this->field_name ),
			'name'        => esc_attr( $this->field_name ),
			'placeholder' => empty( $attributes['fieldPlaceholder'] ) ? '' : esc_attr( str_replace( '<br>', "\n", $attributes['fieldPlaceholder'] ) ),
		);

		// Nest form data within a fieldset.
		if ( ! empty( $block->context['omniform/fieldGroupName'] ) ) {
			$field_attributes['name'] = $block->context['omniform/fieldGroupName'] . '[' . sanitize_title( $field_attributes['name'] ) . ']';
		}

		// Stitch together the input's attributes.
		$field_attributes = array_map(
			function( $attr, $val ) {
				return sprintf( '%1$s="%2$s"', esc_attr( $attr ), esc_attr( $val ) );
			},
			array_keys( $field_attributes ),
			$field_attributes
		);

		$field_control = sprintf(
			'<textarea class="omniform-field-control" rows="10" %s>%s</textarea>',
			implode( ' ', $field_attributes ),
			esc_textarea( $form_ingestion->formValue( $this->field_name ) )
		);

		return sprintf(
			'<div class="wp-block-omniform-%1$s omniform-%1$s">%2$s</div>',
			esc_attr( $this->blockTypeName() ),
			$this->renderFieldLabel() . $field_control . $this->renderFieldError()
		);
	}
}
