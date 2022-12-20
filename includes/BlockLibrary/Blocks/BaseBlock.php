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
abstract class BaseBlock implements FormBlockInterface {

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
	protected $instance;

	/**
	 * The path to the JSON file with metadata definition for the block.
	 *
	 * @return string path to the JSON file with metadata definition for the block.
	 */
	public function blockTypeMetadata() {
		return omniform()->basePath( '/build/block-library/' . $this->blockTypeName() );
	}

	/**
	 * The block type's name
	 *
	 * @return string
	 */
	protected function blockTypeName() {
		$calling_class = substr( strrchr( static::class, '\\' ), 1 );
		return strtolower( preg_replace( '/([A-Z])/', '-$0', lcfirst( $calling_class ) ) );
	}

	/**
	 * The classname applied to the block wrapper.
	 *
	 * @return string
	 */
	public function blockTypeClassname() {
		return 'wp-block-omniform-' . $this->blockTypeName();
	}

	/**
	 * Renders the block on the server.
	 *
	 * @param array     $attributes Block attributes.
	 * @param string    $content    Rendered block output (InnerBlocks).
	 * @param \WP_Block $block      Block instance.
	 *
	 * @return string Returns the block content.
	 */
	public function renderBlock( $attributes, $content, $block ) {
		$this->attributes = $attributes;
		$this->content    = $content;
		$this->instance   = $block;

		return $this->render();
	}

	/**
	 * Retrieve the block attribute with the given $key
	 *
	 * @param string $key The block attribute key.
	 *
	 * @return string
	 */
	public function getBlockAttribute( $key ) {
		return array_key_exists( $key, $this->attributes )
			? $this->attributes[ $key ]
			: null;
	}

	/**
	 * Retrieve the block context with the given $key.
	 *
	 * @param string $key The block context key.
	 *
	 * @return string
	 */
	public function getBlockContext( $key ) {
		return property_exists( $this->instance, 'context' ) && array_key_exists( $key, $this->instance->context )
			? $this->instance->context[ $key ]
			: null;
	}

	/**
	 * Generate element attributes and escape the attributes.
	 *
	 * @param string       $key The attribute key.
	 * @param string|array $value The attribute value.
	 *
	 * @return string
	 */
	public function getElementAttribute( $key, $value ) {
		if ( empty( $key ) || ! isset( $value ) ) {
			return;
		}

		$value = is_array( $value ) ? $value : (array) $value;
		return sprintf(
			'%s="%s"',
			esc_attr( $key ),
			esc_attr( trim( implode( ' ', $value ) ) )
		);
	}

	/**
	 * Renders the block defined by the extending class.
	 *
	 * @return string
	 */
	abstract protected function render();
}