<?php
/**
 * The BaseFieldBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Plugin\FormIngestionEngine;

/**
 * The BaseFieldBlock block class.
 */
class BaseFieldBlock implements FormBlockInterface {

	/**
	 * The block attributes.
	 *
	 * @var array
	 */
	protected $block_attributes = array();

	/**
	 * The input's generated name.
	 *
	 * @var string
	 */
	protected $field_name;

	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return omniform()->basePath( '/build/block-library/' . $this->blockTypeName() );
	}

	/**
	 * Get the block type's name from the class name.
	 *
	 * @return string
	 */
	protected function blockTypeName() {
		$calling_class = substr( strrchr( static::class, '\\' ), 1 );
		return strtolower( preg_replace( '/([A-Z])/', '-$0', lcfirst( $calling_class ) ) );
	}

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
		$this->block_attributes = $attributes;

		$this->field_name = empty( $this->getBlockAttribute( 'fieldName' ) )
			? sanitize_title( $this->getBlockAttribute( 'fieldLabel' ) )
			: $this->getBlockAttribute( 'fieldName' );

		return sprintf(
			'<div class="wp-block-omniform-%1$s omniform-%1$s">%2$s</div>',
			esc_attr( $this->blockTypeName() ),
			$this->renderFieldLabel()
		);
	}

	/**
	 * Render the input's label element.
	 *
	 * @return string
	 */
	protected function renderFieldLabel() {
		return empty( $this->getBlockAttribute( 'fieldLabel' ) ) ? '' : sprintf(
			'<label class="omniform-field-label" for="%s">%s</label>',
			esc_attr( $this->field_name ),
			wp_kses_post( $this->getBlockAttribute( 'fieldLabel' ) )
		);
	}

	/**
	 * Render the input's error text element.
	 *
	 * @return string
	 */
	protected function renderFieldError() {
		$form_ingestion = omniform()->get( FormIngestionEngine::class );
		$errors         = $form_ingestion->fieldError( $this->field_name );
		return empty( $errors ) ? '' : sprintf(
			'<p class="omniform-field-support" style="color:red;">%s</p>',
			wp_kses_post( $errors )
		);
	}

	/**
	 * Retrieve the block attribute with the given $key
	 *
	 * @param string $key The block attribute key.
	 *
	 * @return string
	 */
	protected function getBlockAttribute( $key ) {
		return array_key_exists( $key, $this->block_attributes ) ? $this->block_attributes[ $key ] : '';
	}
}
