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
	 * The rendered block output (InnerBlocks).
	 *
	 * @var string
	 */
	protected $content = '';

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
	public function block_type_metadata() {
		return omniform()->base_path( '/build/block-library/' . $this->block_type_name() );
	}

	/**
	 * The block type's name
	 *
	 * @return string
	 */
	protected function block_type_name() {
		$calling_class = substr( strrchr( static::class, '\\' ), 1 );
		return strtolower( preg_replace( '/([A-Z])/', '-$0', lcfirst( $calling_class ) ) );
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
	public function render_block( $attributes, $content, $block ) {
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
	public function get_block_attribute( $key ) {
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
	public function get_block_context( $key ) {
		return property_exists( $this->instance, 'context' ) && array_key_exists( $key, $this->instance->context )
			? $this->instance->context[ $key ]
			: null;
	}

	/**
	 * Renders the block defined by the extending class.
	 *
	 * @return string
	 */
	abstract protected function render();
}
