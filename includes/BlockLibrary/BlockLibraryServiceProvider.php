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
	}

	/**
	 * Register the blocks.
	 */
	public function registerBlocks() {
		$blocks = array(
			Blocks\Form::class,
			Blocks\FieldText::class,
			Blocks\FieldTextarea::class,
			Blocks\FieldSelect::class,
			Blocks\ButtonSubmit::class,
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
	 * Enqueue required scripts and styles.
	 */
	public function register() {}
}
