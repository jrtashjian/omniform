<?php
/**
 * The PluginServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;

/**
 * The PluginServiceProvider class.
 */
class PluginServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return array
	 */
	public function provides( string $id ): bool {
		$services = array(
			Form::class,
		);

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()->addShared( Form::class );
	}

	/**
	 * Bootstrap any application services by hooking into WordPress with actions/filters.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'init', array( $this, 'registerPostType' ) );
		add_action( 'init', array( $this, 'filterBlockPatternsOnAdmin' ), PHP_INT_MAX );
		add_action( 'rest_api_init', array( $this, 'filterBlockPatternsOnRestApi' ), PHP_INT_MAX );
		add_filter( 'the_content', array( $this, 'renderSingularTemplate' ) );

		// Add custom columns to CPT.
		add_filter(
			'manage_omniform_posts_columns',
			function( $columns ) {
				$part_one = array_slice( $columns, 0, 2 );
				$part_two = array_slice( $columns, 2 );

				return array_merge(
					$part_one,
					array(
						'responses'   => __( 'Responses', 'omniform' ),
						'impressions' => __( 'Impressions', 'omniform' ),
						'conversion'  => __( 'Conversion Rate', 'omniform' ),
					),
					$part_two,
				);
			}
		);

		// Add "Conversion Rate" column to CPT.
		add_action(
			'manage_omniform_posts_custom_column',
			function( $column_key, $post_id ) {
				if ( 'conversion' !== $column_key ) {
					return;
				}

				$percentage_num = new \NumberFormatter( 'en_US', \NumberFormatter::PERCENT );

				$impressions = (int) get_post_meta( $post_id, '_omniform_impressions', true );
				$responses   = (int) get_post_meta( $post_id, '_omniform_responses', true );

				echo esc_attr(
					empty( $impressions )
						? $percentage_num->format( 0 )
						: $percentage_num->format( $responses / $impressions )
				);
			},
			10,
			2
		);

		// Add "Responses" column to CPT.
		add_action(
			'manage_omniform_posts_custom_column',
			function( $column_key, $post_id ) {
				if ( 'responses' !== $column_key ) {
					return;
				}

				$human_readable = new \NumberFormatter( 'en_US', \NumberFormatter::PADDING_POSITION );
				$responses      = (int) get_post_meta( $post_id, '_omniform_responses', true );

				echo esc_attr( $human_readable->format( $responses ) );
			},
			10,
			2
		);

		// Add "Impressions" column to CPT.
		add_action(
			'manage_omniform_posts_custom_column',
			function( $column_key, $post_id ) {
				if ( 'impressions' !== $column_key ) {
					return;
				}

				$human_readable = new \NumberFormatter( 'en_US', \NumberFormatter::PADDING_POSITION );
				$impressions    = (int) get_post_meta( $post_id, '_omniform_impressions', true );

				echo esc_attr( $human_readable->format( $impressions ) );
			},
			10,
			2
		);

		// Add responses quick link on CPT table list.
		add_filter(
			'page_row_actions',
			function( $actions, $post ) {
				if ( 'omniform' !== $post->post_type ) {
					return $actions;
				}

				$actions['responses'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( admin_url( sprintf( 'edit.php?post_type=omniform_response&omniform_id=%d', $post->ID ) ) ),
					/* translators: %s: Form title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221; responses', 'omniform' ), $post->post_title ) ),
					__( 'Responses', 'omniform' ),
				);

				return $actions;
			},
			10,
			2
		);

		// Add custom columns to Responses CPT.
		add_filter(
			'manage_omniform_response_posts_columns',
			function( $columns ) {
				unset( $columns['title'] );
				return array_merge(
					$columns,
					array(
						'form'     => __( 'Form', 'omniform' ),
						'formdata' => __( 'Form Data', 'omniform' ),
					)
				);
			}
		);

		// Add "Form" column to Responses CPT.
		add_action(
			'manage_omniform_response_posts_custom_column',
			function( $column_key, $post_id ) {
				if ( 'form' !== $column_key ) {
					return;
				}

				$form_id = (int) get_post_meta( $post_id, '_omniform_id', true );
				$form    = $this->getContainer()->get( Form::class )->getInstance( $form_id );

				if ( ! $form ) {
					echo esc_html(
						sprintf(
							/* translators: %d: Form ID. */
							__( 'Form ID &#8220;%d&#8221; has been removed.', 'omniform' ),
							$form_id
						)
					);

					return;
				}

				echo sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( admin_url( sprintf( 'post.php?post=%d&action=edit', $form->getId() ) ) ),
					/* translators: %s: Form title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221; responses', 'omniform' ), $form->getTitle() ) ),
					esc_attr( $form->getTitle() ),
				);
			},
			10,
			2
		);

		// Add "Form Data" column to Responses CPT.
		add_action(
			'manage_omniform_response_posts_custom_column',
			function( $column_key, $post_id ) {
				if ( 'formdata' !== $column_key ) {
					return;
				}

				$form_id = (int) get_post_meta( $post_id, '_omniform_id', true );
				$form    = $this->getContainer()->get( Form::class )->getInstance( $form_id );

				echo wp_kses_post( $form->response_text_content( $post_id ) );
			},
			10,
			2
		);

		// Hide row actions for Responses CPT.
		add_filter(
			'post_row_actions',
			function( $actions, $post ) {
				return 'omniform_response' === $post->post_type ? array() : $actions;
			},
			10,
			2
		);

		// Filter responses by form id.
		add_action(
			'restrict_manage_posts',
			function( $post_type ) {
				if ( 'omniform_response' !== $post_type ) {
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

				if ( 'omniform_response' !== $query->query['post_type'] ) {
					return $query;
				}

				if ( empty( $_GET['omniform_id'] ) ) {
					return $query;
				}

				$query->set(
					'meta_query',
					array(
						array(
							'key'   => '_omniform_id',
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

		add_filter(
			'block_type_metadata',
			function( $metadata ) {
				if ( ! is_admin() ) {
					return $metadata;
				}

				if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'site-editor.php' ), true ) ) {
					return $metadata;
				}

				if (
					! empty( $_GET['post'] ) && // phpcs:ignore WordPress.Security.NonceVerification
					'omniform' === get_post_type( (int) $_GET['post'] ) // phpcs:ignore WordPress.Security.NonceVerification
				) {
					return $metadata;
				}

				if (
					str_starts_with( $metadata['name'], 'omniform' ) &&
					! in_array( $metadata['name'], array( 'omniform/form', 'omniform/select-option', 'omniform/select-group' ), true )
				) {
					$metadata['ancestor'] = array( 'omniform/form' );
					return $metadata;
				}

				return $metadata;
			},
		);
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
				'rest_controller_class' => \OmniForm\Plugin\RestApi\ResponsesController::class,
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

					'delete_posts'           => 'delete_posts',
					'read_private_posts'     => 'read_private_posts',
					'edit_post'              => 'edit_post',
					'delete_post'            => 'delete_post',
					'read_post'              => 'read_post',
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
			'omniform_response',
			array(
				'labels'                => array(
					'name'               => _x( 'Responses', 'post type general name', 'omniform' ),
					'singular_name'      => _x( 'Response', 'post type singular name', 'omniform' ),
					'all_items'          => __( 'View responses', 'omniform' ),
					'edit_item'          => __( 'Edit response', 'omniform' ),
					'filter_items_list'  => __( 'Filter responses list', 'omniform' ),
					'not_found_in_trash' => __( 'No responses found in Trash.', 'omniform' ),
					'not_found'          => __( 'No responses found.', 'omniform' ),
					'search_items'       => __( 'Search responses', 'omniform' ),
					'view_item'          => __( 'View response', 'omniform' ),
				),
				'public'                => false,
				'show_ui'               => true,
				'show_in_menu'          => 'edit.php?post_type=omniform',
				'show_in_admin_bar'     => false,
				'rewrite'               => false,
				'show_in_rest'          => true,
				'rest_namespace'        => 'omniform/v1',
				'rest_base'             => 'responses',
				'rest_controller_class' => 'WP_REST_Blocks_Controller',
				'capability_type'       => 'page',
				'capabilities'          => array(
					'create_posts'        => 'do_not_allow',
					'publish_posts'       => 'publish_pages',
					'edit_posts'          => 'edit_pages',
					'edit_others_posts'   => 'edit_others_pages',
					'delete_posts'        => 'delete_pages',
					'delete_others_posts' => 'delete_others_pages',
					'read_private_posts'  => 'read_private_pages',
					'edit_post'           => 'edit_page',
					'delete_post'         => 'delete_page',
					'read_post'           => 'read_page',
				),
				'map_meta_cap'          => true,
				'supports'              => array(
					'title',
					'editor',
					'custom-fields',
				),
			)
		);
	}

	/**
	 * Filter the_content on singular forms to render the form block.
	 *
	 * @param string $content The post content.
	 */
	public function renderSingularTemplate( $content ) {
		return ( ! is_singular( 'omniform' ) || ! is_main_query() )
			? $content
			: do_blocks( '<!-- wp:omniform/form {"ref":' . get_the_ID() . '} /-->' );
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

		if (
			'/wp-admin/post.php' !== $url_parts['path'] &&
			'/wp-admin/post-new.php' !== $url_parts['path']
		) {
			return;
		}

		$query_args      = array();
		$query_arg_parts = explode( '&', $url_parts['query'] );

		foreach ( $query_arg_parts as $arg ) {
			$arg_parts                   = explode( '=', $arg );
			$query_args[ $arg_parts[0] ] = $arg_parts[1];
		}

		if (
			empty( $query_args['post'] ) &&
			empty( $query_args['post_type'] )
		) {
			return;
		}

		if (
			( key_exists( 'post', $query_args ) && 'omniform' !== get_post_type( (int) $query_args['post'] ) ) &&
			( key_exists( 'post_type', $query_args ) && 'omniform' !== $query_args['post_type'] )
		) {
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
