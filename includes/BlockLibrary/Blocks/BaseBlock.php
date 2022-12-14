<?php
/**
 * The BaseBlock block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The BaseBlock block class.
 */
class BaseBlock implements FormBlockInterface {

	/**
	 * The block attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * The parsed instance of a block.
	 *
	 * @var \WP_Block
	 */
	protected $instance = array();

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
	 * @param array     $attributes Block attributes.
	 * @param string    $content    Block default content.
	 * @param \WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) {
		$this->instance   = $block;
		$this->attributes = $attributes;

		$this->field_name = empty( $this->getBlockAttribute( 'fieldName' ) )
			? sanitize_title( $this->getBlockAttribute( 'fieldLabel' ) )
			: $this->getBlockAttribute( 'fieldName' );

		return sprintf(
			'<div class="wp-block-omniform-%1$s omniform-%1$s">%2$s</div>',
			esc_attr( $this->blockTypeName() ),
			do_blocks( $content )
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
		return array_key_exists( $key, $this->attributes ) ? $this->attributes[ $key ] : '';
	}
}
