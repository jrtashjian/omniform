<?php
/**
 * The FieldInput block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

use InquiryWP\Plugin\FormIngestionEngine;

/**
 * The FieldInput block class.
 */
class FieldInput implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return inquirywp()->basePath( '/build/block-library/field-input' );
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
			'<label class="inquirywp-field-label" for="%s">%s</label>',
			esc_attr( $field_name ),
			wp_kses_post( $attributes['label'] )
		);

		$field_attributes = array(
			'id'          => esc_attr( $field_name ),
			'name'        => esc_attr( $field_name ),
			'placeholder' => empty( $attributes['placeholder'] ) ? '' : esc_attr( $attributes['placeholder'] ),
			'value'       => esc_attr( $form_ingestion->formValue( $field_name ) ),
		);

		if ( in_array( $attributes['type'], array( 'checkbox', 'radio' ) ) ) {
			unset( $field_attributes['placeholder'] );
			$field_attributes['value'] = esc_attr( $field_name );

			if ( ! empty( $form_ingestion->formValue( $field_name ) ) ) {
				$field_attributes['checked'] = 'checked';
			}
		}

		$field_control = sprintf(
			'<input class="inquirywp-field-control" type="%s" %s />',
			esc_attr( $attributes['type'] ),
			str_replace(
				array( '=', '&' ),
				array( '="', '" ' ),
				http_build_query( $field_attributes )
			) . '"'
		);

		$field_help = empty( $attributes['help'] ) ? '' : sprintf(
			'<p class="inquirywp-field-support">%s</p>',
			wp_kses_post( $attributes['help'] )
		);

		return sprintf(
			'<div class="wp-block-inquirywp-field-input inquirywp-field-%s">%s</div>',
			esc_attr( $attributes['type'] ),
			$field_label . $field_control . $field_help . $content
		);
	}
}
