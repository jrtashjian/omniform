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
class FieldTextarea implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return inquirywp()->basePath( '/build/block-library/field-textarea' );
	}

	/**
	 * Renders the block on the server.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block default content.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content ) {
		if ( empty( $attributes['label'] ) ) {
			return '';
		}

		$field_name     = sanitize_title( $attributes['label'] );
		$form_ingestion = inquirywp()->get( FormIngestionEngine::class );

		$field_label = sprintf(
			'<label class="field-label" for="%s">%s</label>',
			esc_attr( $field_name ),
			wp_kses_post( $attributes['label'] )
		);

		$field_control = sprintf(
			'<textarea class="field-control" type="%s" placeholder="%s" id="%s" name="%s" value="%s"></textarea>',
			esc_attr( $attributes['type'] ),
			empty( $attributes['placeholder'] ) ? '' : esc_attr( $attributes['placeholder'] ),
			esc_attr( $field_name ),
			esc_attr( $field_name ),
			esc_attr( $form_ingestion->formValue( $field_name ) )
		);

		$field_help = empty( $attributes['help'] ) ? '' : sprintf(
			'<p class="field-support">%s</p>',
			wp_kses_post( $attributes['help'] )
		);

		return sprintf(
			'<div class="wp-block-inquirywp-field-text">%s</div>',
			$field_label . $field_control . $field_help . $content
		);
	}
}
