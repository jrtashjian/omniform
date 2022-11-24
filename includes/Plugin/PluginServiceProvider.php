<?php
/**
 * The PluginServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\ServiceProvider;
use WP_Block_Patterns_Registry;

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
					esc_html__( 'OmniForm', 'omniform' ),
					esc_html__( 'OmniForm', 'omniform' ),
					'manage_options',
					'omniform',
					function () {
						?>
						<div id="omniform" class="hide-if-no-js"></div>

						<?php // JavaScript is disabled. ?>
						<div class="wrap hide-if-js">
							<h1 class="wp-heading-inline">OmniForm</h1>
							<div class="notice notice-error notice-alt">
								<p><?php esc_html_e( 'OmniForm requires JavaScript. Please enable JavaScript in your browser settings.', 'omniform' ); ?></p>
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
		add_action( 'init', array( $this, 'filterBlockPatternsOnAdmin' ), PHP_INT_MAX );
		add_action( 'rest_api_init', array( $this, 'filterBlockPatternsOnRestApi' ), PHP_INT_MAX );
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

		if ( 'toplevel_page_omniform' !== $current_screen->base ) {
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
			window._loadOmniForm = new Promise( function( resolve ) {
				wp.domReady( function() {
					resolve( omniform.plugin.initialize( 'omniform', %s ) );
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
			'omniform_form',
			array(
				'labels'                => array(
					'name'                     => _x( 'OmniForm', 'post type general name', 'omniform' ),
					'singular_name'            => _x( 'Form', 'post type singular name', 'omniform' ),
					'add_new'                  => _x( 'Add New', 'Form', 'omniform' ),
					'add_new_item'             => __( 'Add new Form', 'omniform' ),
					'new_item'                 => __( 'New Form', 'omniform' ),
					'edit_item'                => __( 'Edit Form', 'omniform' ),
					'view_item'                => __( 'View Form', 'omniform' ),
					'all_items'                => __( 'All Forms', 'omniform' ),
					'search_items'             => __( 'Search Forms', 'omniform' ),
					'not_found'                => __( 'No Forms found.', 'omniform' ),
					'not_found_in_trash'       => __( 'No Forms found in Trash.', 'omniform' ),
					'filter_items_list'        => __( 'Filter Forms list', 'omniform' ),
					'items_list_navigation'    => __( 'Forms list navigation', 'omniform' ),
					'items_list'               => __( 'Forms list', 'omniform' ),
					'item_published'           => __( 'Form published.', 'omniform' ),
					'item_published_privately' => __( 'Form published privately.', 'omniform' ),
					'item_reverted_to_draft'   => __( 'Form reverted to draft.', 'omniform' ),
					'item_scheduled'           => __( 'Form scheduled.', 'omniform' ),
					'item_updated'             => __( 'Form updated.', 'omniform' ),
				),
				'public'                => false,
				'show_ui'               => true,
				'show_in_menu'          => true,
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="black"><path fill="#000" fill-rule="evenodd" d="M2.556 13.556a2.2 2.2 0 0 1 0-3.112L4.3 8.7V6.5a2.2 2.2 0 0 1 2.2-2.2h2.2l1.634-1.634a2.2 2.2 0 0 1 3.111 0L15.08 4.3H17.5a2.2 2.2 0 0 1 2.2 2.2v2.421l1.523 1.523a2.2 2.2 0 0 1 0 3.112L19.7 15.079V17.5a2.2 2.2 0 0 1-2.2 2.2h-2.421l-1.634 1.634a2.2 2.2 0 0 1-3.111 0L8.7 19.7H6.5a2.2 2.2 0 0 1-2.2-2.2v-2.2l-1.744-1.744ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z" clip-rule="evenodd"/></svg>' ),
				'rewrite'               => false,
				'show_in_rest'          => true,
				'rest_namespace'        => 'omniform/v1',
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

		register_post_type(
			'omniform_submission',
			array(
				'labels'                => array(
					'name'                     => _x( 'Submissions', 'post type general name', 'omniform' ),
					'singular_name'            => _x( 'Submission', 'post type singular name', 'omniform' ),
					'add_new'                  => _x( 'Add New', 'Submission', 'omniform' ),
					'add_new_item'             => __( 'Add new Submission', 'omniform' ),
					'new_item'                 => __( 'New Submission', 'omniform' ),
					'edit_item'                => __( 'Edit Submission', 'omniform' ),
					'view_item'                => __( 'View Submission', 'omniform' ),
					'all_items'                => __( 'View Submissions', 'omniform' ),
					'search_items'             => __( 'Search Submissions', 'omniform' ),
					'not_found'                => __( 'No Submissions found.', 'omniform' ),
					'not_found_in_trash'       => __( 'No Submissions found in Trash.', 'omniform' ),
					'filter_items_list'        => __( 'Filter Submissions list', 'omniform' ),
					'items_list_navigation'    => __( 'Submissions list navigation', 'omniform' ),
					'items_list'               => __( 'Submissions list', 'omniform' ),
					'item_published'           => __( 'Submission published.', 'omniform' ),
					'item_published_privately' => __( 'Submission published privately.', 'omniform' ),
					'item_reverted_to_draft'   => __( 'Submission reverted to draft.', 'omniform' ),
					'item_scheduled'           => __( 'Submission scheduled.', 'omniform' ),
					'item_updated'             => __( 'Submission updated.', 'omniform' ),
				),
				'public'                => false,
				'show_ui'               => true,
				'show_in_menu'          => 'edit.php?post_type=omniform_form',
				'menu_position'         => 1,
				'rewrite'               => false,
				'show_in_rest'          => true,
				'rest_namespace'        => 'omniform/v1',
				'rest_base'             => 'submissions',
				'rest_controller_class' => 'WP_REST_Blocks_Controller',
				'capability_type'       => 'block',
				'capabilities'          => array(
					'read'                   => 'edit_posts',
					'create_posts'           => 'edit_posts',
					'edit_posts'             => 'edit_posts',
					'edit_published_posts'   => 'edit_published_posts',
					'delete_published_posts' => 'delete_published_posts',
					'edit_others_posts'      => 'edit_others_posts',
					'delete_others_posts'    => 'delete_others_posts',
				),
				'map_meta_cap'          => true,
				'supports'              => array(
					'title',
				),
			)
		);
	}

	/**
	 * Filter block patterns for the CPT block editor.
	 *
	 * Removes block patterns not specifically registered for the custom post type.
	 */
	public function filterBlockPatternsOnAdmin() {
		if ( ! is_admin() ) {
			return;
		}

		if (
			'post.php' !== $GLOBALS['pagenow'] ||
			empty( $_GET['post'] ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			return;
		}

		if ( 'omniform_form' !== get_post_type( (int) $_GET['post'] ) ) {
			return;
		}

		$this->filterBlockPatterns();

		// Prevent Block Directory.
		remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );

		add_filter(
			'allowed_block_types_all',
			function() {
				return array(
					'omniform/button-submit',
					'omniform/field-input',
					'omniform/field-select',
					'omniform/field-textarea',
					'omniform/fieldset',
					'core/paragraph',
					'core/heading',
					'core/image',
					'core/group',
					'core/spacer',
				);
			}
		);
	}

	/**
	 * Filter block patterns in REST API responses.
	 *
	 * Removes block patterns not specifically registered for the custom post type.
	 */
	public function filterBlockPatternsOnRestApi() {
		if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
			return;
		}

		$url_parts = wp_parse_url( $_SERVER['HTTP_REFERER'] );

		if ( '/wp-admin/post.php' !== $url_parts['path'] ) {
			return;
		}

		$query_args      = array();
		$query_arg_parts = explode( '&', $url_parts['query'] );

		foreach ( $query_arg_parts as $arg ) {
			$arg_parts                   = explode( '=', $arg );
			$query_args[ $arg_parts[0] ] = $arg_parts[1];
		}

		if (
			empty( $query_args['post'] ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			return;
		}

		if ( 'omniform_form' !== get_post_type( (int) $query_args['post'] ) ) {
			return;
		}

		$this->filterBlockPatterns();
	}

	/**
	 * Removes block patterns not registered specifically for CPT.
	 */
	private function filterBlockPatterns() {
		// Prevent block patterns not explicitly registered for the custom post type.
		add_filter( 'should_load_remote_block_patterns', '__return_false' );

		$block_patterns_registry = WP_Block_Patterns_Registry::get_instance();
		$registered_patterns     = $block_patterns_registry->get_all_registered();

		foreach ( $registered_patterns as $pattern ) {
			$post_types_exists = array_key_exists( 'postTypes', $pattern );

			if (
			! $post_types_exists ||
			( $post_types_exists && in_array( 'omniform', $pattern['postTypes'], true ) )
			) {
				$block_patterns_registry->unregister( $pattern['name'] );
			}
		}
	}
}
