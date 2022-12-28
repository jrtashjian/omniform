<?php
/**
 * The PluginServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\ServiceProvider;

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
				add_submenu_page(
					'edit.php?post_type=omniform',
					esc_html__( 'Dashboard', 'omniform' ),
					esc_html__( 'Dashboard', 'omniform' ),
					'manage_options',
					'dashboard',
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
					0
				);
			}
		);

		add_action( 'init', array( $this, 'registerPostType' ) );
		add_action( 'init', array( $this, 'filterBlockPatternsOnAdmin' ), PHP_INT_MAX );
		add_action( 'rest_api_init', array( $this, 'filterBlockPatternsOnRestApi' ), PHP_INT_MAX );

		add_filter(
			'manage_omniform_posts_columns',
			function( $columns ) {
				return array_merge(
					$columns,
					array(
						'activity' => __( 'Activity', 'omniform' ),
					)
				);
			}
		);

		add_action(
			'manage_omniform_posts_custom_column',
			function( $column_key, $post_id ) {
				if ( 'activity' !== $column_key ) {
					return;
				}

				$human_readable = new \NumberFormatter( 'en_US', \NumberFormatter::PADDING_POSITION );
				$percentage_num = new \NumberFormatter( 'en_US', \NumberFormatter::PERCENT );

				$impressions = (int) get_post_meta( $post_id, 'impressions', true );
				$submissions = (int) get_post_meta( $post_id, 'submissions', true );

				echo sprintf(
					'<p>%1$s Submissions<br/>%2$s Impressions<br />%3$s Conversion Rate</p>',
					esc_attr( $human_readable->format( $submissions ) ),
					esc_attr( $human_readable->format( $impressions ) ),
					esc_attr(
						empty( $impressions )
						? $percentage_num->format( 0 )
						: $percentage_num->format( $submissions / $impressions )
					)
				);
			},
			10,
			2
		);

		add_filter(
			'page_row_actions',
			function( $actions, $post ) {
				if ( 'omniform' !== $post->post_type ) {
					return $actions;
				}

				$actions['submissions'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					admin_url( sprintf( 'edit.php?post_type=omniform_submission&omniform_id=%d', $post->ID ) ),
					/* translators: %s: Form title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221; submissions', 'omniform' ), $post->post_title ) ),
					__( 'Submissions', 'omniform' ),
				);

				return $actions;
			},
			10,
			2
		);

		add_filter(
			'manage_omniform_submission_posts_columns',
			function( $columns ) {
				unset( $columns['title'] );
				return array_merge(
					$columns,
					array(
						'formdata' => __( 'Form Data', 'omniform' ),
					)
				);
			}
		);

		add_action(
			'manage_omniform_submission_posts_custom_column',
			function( $column_key, $post_id ) {
				if ( 'formdata' !== $column_key ) {
					return;
				}

				echo sprintf(
					'<pre style="overflow:auto;">%s</pre>',
					wp_kses_post( get_the_content( null, false, $post_id ) )
				);
			},
			10,
			2
		);

		// Filter submissions by form id.
		add_action(
			'restrict_manage_posts',
			function( $post_type ) {
				if ( 'omniform_submission' !== $post_type ) {
					return;
				}

				wp_dropdown_pages(
					array(
						'post_type'        => 'omniform',
						'name'             => 'omniform_id',
						'show_option_none' => 'All Forms',
						'echo'             => true,
						'selected'         => esc_attr( empty( $_GET['omniform_id'] ) ? 0 : (int) $_GET['omniform_id'] ),
					)
				);
			},
		);
		add_filter(
			'parse_query',
			function( $query ) {
				if ( ! ( is_admin() && $query->is_main_query() ) ) {
					return $query;
				}

				if ( 'omniform_submission' !== $query->query['post_type'] ) {
					return $query;
				}

				if ( empty( $_GET['omniform_id'] ) ) {
					return $query;
				}

				$query->set(
					'meta_query',
					array(
						array(
							'key'   => 'omniform_id',
							'value' => (int) $_GET['omniform_id'],
						),
					)
				);

				return $query;
			}
		);

		add_filter(
			'allowed_block_types_all',
			function( $allowed_block_types, $block_editor_context ) {
				if (
					'core/edit-post' === $block_editor_context->name &&
					'omniform' === $block_editor_context->post->post_type
				) {
					return array(
						'omniform/button',
						'omniform/field-input',
						'omniform/field-select',
						'omniform/field-textarea',
						'omniform/fieldset',
						'omniform/form',
						'omniform/select-group',
						'omniform/select-option',
						'core/audio',
						'core/block',
						'core/code',
						'core/column',
						'core/columns',
						'core/cover',
						'core/file',
						'core/gallery',
						'core/group',
						'core/heading',
						'core/image',
						'core/list-item',
						'core/list',
						'core/media-text',
						'core/missing',
						'core/paragraph',
						'core/pattern',
						'core/preformatted',
						'core/pullquote',
						'core/quote',
						'core/separator',
						'core/site-logo',
						'core/site-tagline',
						'core/site-title',
						'core/spacer',
						'core/table',
						'core/verse',
						'core/video',
					);
				}
				return $allowed_block_types;
			},
			10,
			2
		);

		// add_filter(
		// 	'block_type_metadata',
		// 	function( $metadata ) {
		// 		if ( ! is_admin() ) {
		// 			return $metadata;
		// 		}

		// 		if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'site-editor.php' ), true ) ) {
		// 			return $metadata;
		// 		}

		// 		if (
		// 			! empty( $_GET['post'] ) && // phpcs:ignore WordPress.Security.NonceVerification
		// 			'omniform' === get_post_type( (int) $_GET['post'] ) // phpcs:ignore WordPress.Security.NonceVerification
		// 		) {
		// 			return $metadata;
		// 		}

		// 		if (
		// 			str_starts_with( $metadata['name'], 'omniform' ) &&
		// 			! in_array( $metadata['name'], array( 'omniform/form', 'omniform/select-option', 'omniform/select-group' ), true )
		// 		) {
		// 			$metadata['ancestor'] = array( 'omniform/form' );
		// 			return $metadata;
		// 		}

		// 		return $metadata;
		// 	},
		// );
	}

	/**
	 * Register any application services.
	 */
	public function register() {}

	/**
	 * Enqueue required scripts and styles.
	 */
	public function registerScripts() {
		$current_screen = get_current_screen();

		if ( 'omniform_page_dashboard' !== $current_screen->base ) {
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
			'omniform',
			array(
				'labels'                => array(
					'name'                     => _x( 'OmniForm', 'post type general name', 'omniform' ),
					'singular_name'            => _x( 'OmniForm', 'post type singular name', 'omniform' ),
					'add_new'                  => _x( 'Create a Form', 'Form', 'omniform' ),
					'add_new_item'             => __( 'Create a Form', 'omniform' ),
					'new_item'                 => __( 'Create a Form', 'omniform' ),
					'edit_item'                => __( 'Edit Form', 'omniform' ),
					'view_item'                => __( 'View Form', 'omniform' ),
					'all_items'                => __( 'All Forms', 'omniform' ),
					'search_items'             => __( 'Search Forms', 'omniform' ),
					'not_found'                => __( 'No forms found.', 'omniform' ),
					'not_found_in_trash'       => __( 'No forms found in Trash.', 'omniform' ),
					'filter_items_list'        => __( 'Filter form list', 'omniform' ),
					'items_list_navigation'    => __( 'Form list navigation', 'omniform' ),
					'items_list'               => __( 'Form list', 'omniform' ),
					'item_published'           => __( 'Form published.', 'omniform' ),
					'item_published_privately' => __( 'Form published privately.', 'omniform' ),
					'item_reverted_to_draft'   => __( 'Form reverted to draft.', 'omniform' ),
					'item_scheduled'           => __( 'Form scheduled.', 'omniform' ),
					'item_updated'             => __( 'Form updated.', 'omniform' ),
				),
				'public'                => true,
				'hierarchical'          => true, // Literally just so I can use wp_dropdown_pages.
				'show_ui'               => true,
				// 'show_in_menu'          => false,
				// 'rewrite'               => false,
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M3.33 15.424a4.842 4.842 0 0 1 0-6.848l.207-.208v-2.42a2.421 2.421 0 0 1 2.421-2.422H8.38l.086-.086a4.842 4.842 0 0 1 6.848 0l.086.086h2.665a2.421 2.421 0 0 1 2.421 2.421v2.665a4.842 4.842 0 0 1 0 6.776v2.665a2.421 2.421 0 0 1-2.421 2.42h-2.665l-.086.087a4.842 4.842 0 0 1-6.848 0l-.086-.086H5.96a2.421 2.421 0 0 1-2.422-2.421v-2.421l-.207-.208ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z" clip-rule="evenodd"/></svg>' ),
				'show_in_rest'          => true,
				'rest_namespace'        => 'omniform/v1',
				'rest_base'             => 'forms',
				'rest_controller_class' => \OmniForm\Plugin\RestApi\SubmissionsController::class,
				'capability_type'       => 'block',
				'capabilities'          => array(
					// You need to be able to edit posts, in order to read blocks in their raw form.
					'read'                   => 'edit_posts',
					// You need to be able to publish posts, in order to create blocks.
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
					'slug',
					'editor',
					'revisions',
					'custom-fields',
				),
			)
		);

		register_taxonomy(
			'omniform_type',
			array( 'omniform' ),
			array(
				'public'       => true,
				'hierarchical' => false,
				'labels'       => array(
					'name'          => __( 'Form Types', 'omniform' ),
					'singular_name' => __( 'Form Type', 'omniform' ),
				),
				'query_var'    => false,
				'rewrite'      => false,
				// 'show_ui'           => false,
				// 'show_in_nav_menus' => false,
				// 'show_in_rest'      => false,
			)
		);

		register_post_meta(
			'omniform',
			'required_label',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
				'default'      => '*',
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
				'show_in_menu'          => 'edit.php?post_type=omniform',
				'show_in_admin_bar'     => false,
				'rewrite'               => false,
				'show_in_rest'          => true,
				'rest_namespace'        => 'omniform/v1',
				'rest_base'             => 'submissions',
				'rest_controller_class' => 'WP_REST_Blocks_Controller',
				'capability_type'       => 'post',
				'supports'              => array(
					'title',
					'editor',
					'custom-fields',
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

		if ( 'omniform' !== get_post_type( (int) $_GET['post'] ) ) {
			return;
		}

		$this->filterBlockPatterns();

		// Prevent Block Directory.
		remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
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

		if ( 'omniform' !== get_post_type( (int) $query_args['post'] ) ) {
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

		$block_patterns_registry = \WP_Block_Patterns_Registry::get_instance();
		$registered_patterns     = $block_patterns_registry->get_all_registered();

		foreach ( $registered_patterns as $pattern ) {
			$post_types_exists = array_key_exists( 'postTypes', $pattern );

			if (
				! $post_types_exists ||
				! in_array( 'omniform', $pattern['postTypes'], true )
			) {
				$block_patterns_registry->unregister( $pattern['name'] );
			}
		}
	}
}
