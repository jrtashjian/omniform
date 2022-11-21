<?php
/**
 * The PluginServiceProvider class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\Plugin;

use InquiryWP\ServiceProvider;

/**
 * The PluginServiceProvider class.
 */
class PluginServiceProvider extends ServiceProvider {

	/**
	 * This method will be used for hooking into WordPress with actions/filters.
	 */
	public function boot() {
		add_action( 'admin_enqueue_scripts', array( $this, 'registerScripts' ) );

		add_action(
			'admin_menu',
			function () {
				add_menu_page(
					esc_html__( 'InquiryWP', 'inquirywp' ),
					esc_html__( 'InquiryWP', 'inquirywp' ),
					'manage_options',
					'inquirywp',
					function () {
						?>
						<div id="inquirywp" class="hide-if-no-js"></div>

						<?php // JavaScript is disabled. ?>
						<div class="wrap hide-if-js">
							<h1 class="wp-heading-inline">InquiryWP</h1>
							<div class="notice notice-error notice-alt">
								<p><?php esc_html_e( 'InquiryWP requires JavaScript. Please enable JavaScript in your browser settings.', 'inquirywp' ); ?></p>
							</div>
						</div>
						<?php
					},
					'',
					2
				);
			}
		);

		add_action( 'init', array( $this, 'registerPostType' ) );
		add_action( 'current_screen', array( $this, 'prepareEditors' ) );
	}

	/**
	 * Register any application services.
	 */
	public function register() {
		$this->app->singleton( FormIngestionEngine::class );
	}

	/**
	 * Enqueue required scripts and styles.
	 */
	public function registerScripts() {
		$current_screen = get_current_screen();

		if ( 'toplevel_page_inquirywp' !== $current_screen->base ) {
			return;
		}

		// Prevent default hooks rendering content to the page.
		remove_all_actions( 'network_admin_notices' );
		remove_all_actions( 'user_admin_notices' );
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

		$asset_loader = $this->app->makeWith(
			Asset::class,
			array( 'handle' => strtolower( str_replace( '\\', '-', __NAMESPACE__ ) ) )
		);

		$asset_loader->setPackageName( strtolower( basename( __DIR__ ) ) );
		$asset_loader->enqueueScript();
		$asset_loader->enqueueStyle();

		$init_script = <<<JS
		( function() {
			window._loadInquiryWP = new Promise( function( resolve ) {
				wp.domReady( function() {
					resolve( inquirywp.plugin.initialize( 'inquirywp', %s ) );
				} );
			} );
		} )();
		JS;

		$script = sprintf(
			$init_script,
			wp_json_encode( array() )
		);
		wp_add_inline_script( $asset_loader->getHandle(), $script );
	}

	/**
	 * Register post type
	 */
	public function registerPostType() {
		register_post_type(
			'inquirywp_form',
			array(
				'labels'                => array(
					'name'                     => _x( 'Forms', 'post type general name', 'inquirywp' ),
					'singular_name'            => _x( 'Form', 'post type singular name', 'inquirywp' ),
					'add_new'                  => _x( 'Add New', 'Form', 'inquirywp' ),
					'add_new_item'             => __( 'Add new Form', 'inquirywp' ),
					'new_item'                 => __( 'New Form', 'inquirywp' ),
					'edit_item'                => __( 'Edit Form', 'inquirywp' ),
					'view_item'                => __( 'View Form', 'inquirywp' ),
					'all_items'                => __( 'All Forms', 'inquirywp' ),
					'search_items'             => __( 'Search Forms', 'inquirywp' ),
					'not_found'                => __( 'No Forms found.', 'inquirywp' ),
					'not_found_in_trash'       => __( 'No Forms found in Trash.', 'inquirywp' ),
					'filter_items_list'        => __( 'Filter Forms list', 'inquirywp' ),
					'items_list_navigation'    => __( 'Forms list navigation', 'inquirywp' ),
					'items_list'               => __( 'Forms list', 'inquirywp' ),
					'item_published'           => __( 'Form published.', 'inquirywp' ),
					'item_published_privately' => __( 'Form published privately.', 'inquirywp' ),
					'item_reverted_to_draft'   => __( 'Form reverted to draft.', 'inquirywp' ),
					'item_scheduled'           => __( 'Form scheduled.', 'inquirywp' ),
					'item_updated'             => __( 'Form updated.', 'inquirywp' ),
				),
				'public'                => false,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'rewrite'               => false,
				'show_in_rest'          => true,
				'rest_namespace'        => 'inquirywp/v1',
				'rest_base'             => 'forms',
				'rest_controller_class' => 'WP_REST_Blocks_Controller',
				'capability_type'       => 'block',
				'capabilities'          => array(
					'read'                   => 'edit_posts',
					'create_posts'           => 'publish_posts',
					'edit_posts'             => 'edit_posts',
					'edit_published_posts'   => 'edit_published_posts',
					'delete_published_posts' => 'delete_published_posts',
					'edit_others_posts'      => 'edit_others_posts',
					'delete_others_posts'    => 'delete_others_posts',
				),
				'map_meta_cap'          => true,
				'supports'              => array(
					'title',
					'editor',
					'revisions',
				),
			)
		);
	}

	/**
	 * Prepare the block edit screens.
	 */
	public function prepareEditors() {
		if ( ! is_admin() ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'inquirywp_form' === $screen->post_type ) {
			remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
			add_filter( 'should_load_remote_block_patterns', '__return_false' );

			add_filter(
				'allowed_block_types_all',
				function() {
					return array(
						'inquirywp/button-submit',
						'inquirywp/field-input',
						'inquirywp/field-select',
						'inquirywp/field-textarea',
						'inquirywp/fieldset',
						'core/paragraph',
						'core/heading',
						'core/image',
						'core/group',
						'core/spacer',
					);
				}
			);
		}
	}
}
