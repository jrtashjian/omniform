<?php
/**
 * The FieldSelect block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

use InquiryWP\Plugin\FormIngestionEngine;

/**
 * The FieldSelect block class.
 */
class FieldSelect implements FormBlockInterface {
	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return inquirywp()->basePath( '/build/block-library/field-select' );
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

		$field_name = sanitize_title( $attributes['label'] );
		// phpcs:ignore
		// $form_ingestion = inquirywp()->get( FormIngestionEngine::class );

		$field_label = sprintf(
			'<label class="field-label" for="%s">%s</label>',
			esc_attr( $field_name ),
			wp_kses_post( $attributes['label'] )
		);

		$field_control = sprintf(
			'<select class="field-control" id="%s" name="%s"><option value="1">One</option><option value="2">Two</option><option value="3">Three</option></select>',
			esc_attr( $field_name ),
			esc_attr( $field_name )
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
