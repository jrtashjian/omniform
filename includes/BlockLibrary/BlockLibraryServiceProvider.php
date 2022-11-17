<?php
/**
 * The BlockLibraryServiceProvider class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary;

use InquiryWP\ServiceProvider;

/**
 * The BlockLibraryServiceProvider class.
 */
class BlockLibraryServiceProvider extends ServiceProvider {
	/**
	 * This method will be used for hooking into WordPress with actions/filters.
	 */
	public function boot() {
		add_action( 'init', array( $this, 'registerBlocks' ) );
		add_filter( 'render_block_data', array( $this, 'groupFields' ), 10, 3 );
	}

	/**
	 * Register the blocks.
	 */
	public function registerBlocks() {
		$blocks = array(
			Blocks\ButtonSubmit::class,
			Blocks\FieldInput::class,
			Blocks\FieldSelect::class,
			Blocks\FieldTextarea::class,
			Blocks\Form::class,
			Blocks\Fieldset::class,
		);

		foreach ( $blocks as $block ) {
			$block_object = $this->app->make( $block );

			register_block_type(
				$block_object->blockTypeMetadata(),
				array( 'render_callback' => array( $block_object, 'renderBlock' ) )
			);
		}
	}

	/**
	 * Filters the block being rendered in render_block(), before it's processed.
	 *
	 * @since 5.1.0
	 * @since 5.9.0 The `$parent_block` parameter was added.
	 *
	 * @param array          $parsed_block The block being rendered.
	 * @param array          $source_block An un-modified copy of $parsed_block, as it appeared in the source content.
	 * @param \WP_Block|null $parent_block If this is a nested block, a reference to the parent block.
	 */
	public function groupFields( $parsed_block, $source_block, $parent_block ) {
		if (
			false !== strpos( $source_block['blockName'], 'inquirywp' ) &&
			! empty( $parent_block ) &&
			'inquirywp/fieldset' === $parent_block->name
		) {
			$parsed_block['attrs']['group'] = sanitize_title( $parent_block->attributes['legend'] );
		}

		return $parsed_block;
	}

	/**
	 * Enqueue required scripts and styles.
	 */
	public function register() {}
}
