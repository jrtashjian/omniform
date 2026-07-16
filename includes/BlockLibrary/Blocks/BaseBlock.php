<?php
/**
 * Abstract base class for OmniForm block server-side renderers.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Shared foundation for OmniForm block rendering.
 *
 * Binds WordPress render-callback state (attributes, inner content, and block
 * instance), derives the block type name from the concrete class, and provides
 * accessors for attributes and context. Concrete blocks implement render().
 */
abstract class BaseBlock implements FormBlockInterface {

	/**
	 * Allowed HTML tags for block labels.
	 */
	private const ALLOWED_HTML_FOR_LABELS = array(
		'strong' => array(),
		'em'     => array(),
		'img'    => array(
			'class' => true,
			'style' => true,
			'src'   => true,
			'alt'   => true,
		),
	);

	/**
	 * The block attributes.
	 *
	 * @var array<string, mixed>
	 */
	protected array $attributes = array();

	/**
	 * The rendered block output (InnerBlocks).
	 *
	 * @var string
	 */
	protected string $content = '';

	/**
	 * The parsed WordPress block instance.
	 *
	 * @var \WP_Block
	 */
	protected \WP_Block $instance;

	/**
	 * The block type's name.
	 *
	 * @return string
	 */
	public function block_type_name(): string {
		$short_class_name = ( new \ReflectionClass( static::class ) )->getShortName();
		$with_dashes      = preg_replace( '/([A-Z])/', '-$0', lcfirst( $short_class_name ) );

		return strtolower( $with_dashes );
	}

	/**
	 * Renders the block on the server.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param string               $content    Rendered block output (InnerBlocks).
	 * @param \WP_Block            $block      Block instance.
	 *
	 * @return string
	 */
	public function render_block( array $attributes, string $content, \WP_Block $block ): string {
		$this->bind_render_state( $attributes, $content, $block );

		return $this->render();
	}

	/**
	 * Retrieve the block attribute with the given key.
	 *
	 * @param string $key The block attribute key.
	 *
	 * @return mixed
	 */
	public function get_block_attribute( string $key ): mixed {
		return $this->attributes[ $key ] ?? null;
	}

	/**
	 * Retrieve the block context with the given key.
	 *
	 * @param string $key The block context key.
	 *
	 * @return mixed
	 */
	public function get_block_context( string $key ): mixed {
		return $this->instance->context[ $key ] ?? null;
	}

	/**
	 * Allowed HTML tags for labels rendered by this block.
	 *
	 * @return array<string, array|bool>
	 */
	protected function allowed_html_for_labels(): array {
		return self::ALLOWED_HTML_FOR_LABELS;
	}

	/**
	 * Sanitize a field or group name for use as an HTML class, id, or name segment.
	 *
	 * @param string $name Raw field or group name.
	 *
	 * @return string
	 */
	protected function sanitize_field_name( string $name ): string {
		return sanitize_html_class( preg_replace( '/\s+/', '-', $name ) );
	}

	/**
	 * Bind the WordPress render callback arguments for this request.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param string               $content    Rendered block output (InnerBlocks).
	 * @param \WP_Block            $block      Block instance.
	 */
	protected function bind_render_state( array $attributes, string $content, \WP_Block $block ): void {
		$this->attributes = $attributes;
		$this->content    = $content;
		$this->instance   = $block;
	}

	/**
	 * Renders the block defined by the extending class.
	 *
	 * @return string
	 */
	abstract protected function render(): string;
}
