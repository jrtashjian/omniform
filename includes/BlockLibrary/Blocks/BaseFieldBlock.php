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
class BaseFieldBlock extends BaseBlock {

	/**
	 * The input's generated name.
	 *
	 * @var string
	 */
	protected $field_name;

	/**
	 * Renders the block on the server.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) {
		parent::renderBlock( $attributes, $content, $block );

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
		return '';
		$form_ingestion = omniform()->get( FormIngestionEngine::class );
		$errors         = $form_ingestion->fieldError( $this->field_name );
		return empty( $errors ) ? '' : sprintf(
			'<p class="omniform-field-support" style="color:red;">%s</p>',
			wp_kses_post( $errors )
		);
	}
}
