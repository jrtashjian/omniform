<?php
/**
 * The BaseFieldBlock block class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary\Blocks;

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
		return inquirywp()->basePath( '/build/block-library/' . $this->blockTypeName() );
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
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block default content.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content ) {
		$this->block_attributes = $attributes;

		$this->field_name = sanitize_title( $this->getBlockAttribute( 'label' ) );

		return sprintf(
			'<div class="wp-block-inquirywp-%1$s inquirywp-%1$s">%2$s</div>',
			esc_attr( $this->blockTypeName() ),
			$this->renderFieldLabel() . $this->renderFieldHelpText() . $content
		);
	}

	/**
	 * Render the input's label element.
	 *
	 * @return string
	 */
	protected function renderFieldLabel() {
		return empty( $this->getBlockAttribute( 'label' ) ) ? '' : sprintf(
			'<label class="inquirywp-field-label" for="%s">%s</label>',
			esc_attr( $this->field_name ),
			wp_kses_post( $this->field_name )
		);
	}

	/**
	 * Render the input's help text element.
	 *
	 * @return string
	 */
	protected function renderFieldHelpText() {
		return empty( $this->getBlockAttribute( 'help' ) ) ? '' : sprintf(
			'<p class="inquirywp-field-support">%s</p>',
			wp_kses_post( $this->getBlockAttribute( 'help' ) )
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
