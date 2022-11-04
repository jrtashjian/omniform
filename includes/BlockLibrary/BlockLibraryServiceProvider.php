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
		register_block_type(
			$this->app->basePath( '/packages/block-library/form' ),
			array(
				'render_callback' => array( $this, 'renderBlockForm' ),
			)
		);
		register_block_type( $this->app->basePath( '/packages/block-library/field-input' ) );
	}

	/**
	 * Enqueue required scripts and styles.
	 */
	public function register() {}

	/**
	 * Render the form block.
	 *
	 * @param array $attributes The block attributes.
	 * @return string Rendered HTML of the referenced block.
	 */
	public function renderBlockForm( $attributes ) {
		if ( empty( $attributes['ref'] ) ) {
			return '';
		}

		$form_block = get_post( $attributes['ref'] );
		if ( ! $form_block || 'inquirywp_form' !== $form_block->post_type ) {
			return '';
		}

		if ( 'publish' !== $form_block->post_status || ! empty( $form_block->post_password ) ) {
			return '';
		}

		$button_classes = array(
			'wp-block-button',
			wp_theme_get_element_class_name( 'button' ),
		);
		$button_markup  = sprintf(
			'<button type="submit" class="%s">%s</button>',
			esc_attr( implode( ' ', $button_classes ) ),
			'Submit'
		);

		$nonce   = wp_nonce_field( 'inquirywp_form_submission_' . $attributes['ref'] );
		$content = do_blocks( $form_block->post_content );

		return sprintf(
			'<form method="post" action="%s">%s</form>',
			esc_url( home_url( '/' ) ),
			$nonce . $content . $button_markup
		);
	}
}
