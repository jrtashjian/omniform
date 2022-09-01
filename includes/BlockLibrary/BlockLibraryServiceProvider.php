<?php
/**
 * The BlockLibraryServiceProvider class.
 *
 * @package PluginWP
 */

namespace PluginWP\BlockLibrary;

use PluginWP\ServiceProvider;

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
		register_block_type( $this->app->basePath( '/packages/block-library/test-block' ) );
	}

	/**
	 * Enqueue required scripts and styles.
	 */
	public function register() {}
}
