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
		add_action( 'admin_enqueue_scripts', array( $this, 'disable_admin_notices' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'init', array( $this, 'filter_block_patterns_on_admin' ), PHP_INT_MAX );
		add_action( 'rest_api_init', array( $this, 'filter_block_patterns_on_rest_api' ), PHP_INT_MAX );
		add_filter( 'the_content', array( $this, 'render_singular_template' ) );

		add_action( 'admin_init', array( $this, 'dismiss_newsletter_notice' ) );

		// Send email notification when a response is created.
		add_action(
			'omniform_response_created',
			function ( $response_id, $form ) {
				$notify_email         = get_post_meta( $form->get_id(), 'notify_email', true );
				$notify_email_subject = get_post_meta( $form->get_id(), 'notify_email_subject', true );

				wp_mail(
					empty( $notify_email )
						? get_option( 'admin_email' )
						: $notify_email,
					empty( $notify_email_subject )
						// translators: %1$s represents the blog name, %2$s represents the form title.
						? esc_attr( sprintf( __( 'New Response: %1$s - %2$s', 'omniform' ), get_option( 'blogname' ), $form->get_title() ) )
						: esc_attr( $notify_email_subject ),
					wp_kses( $form->response_email_message( $response_id ), array() )
				);
			},
			10,
			2
		);

		// Increment form response count.
		add_action(
			'omniform_response_created',
			function ( $response_id, $form ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
				// Incremement form responses.
				$response_count = get_post_meta( $form->get_id(), '_omniform_responses', true );
				$response_count = $response_count ? $response_count : 0;

				update_post_meta( $form->get_id(), '_omniform_responses', (int) $response_count + 1 );
			},
			10,
			2
		);

		// Increment form impression count.
		add_action(
			'omniform_form_render',
			function ( $form_id ) {
				if ( is_admin() ) {
					return;
				}

				$impressions = get_post_meta( $form_id, '_omniform_impressions', true );
				$impressions = $impressions ? $impressions : 0;

				update_post_meta( $form_id, '_omniform_impressions', $impressions + 1 );
			}
		);

		// Add custom columns to CPT.
		add_filter(
			'manage_omniform_posts_columns',
			function ( $columns ) {
				$part_one = array_slice( $columns, 0, 2 );
				$part_two = array_slice( $columns, 2 );

				return array_merge(
					$part_one,
					array(
						'responses'   => esc_html__( 'Responses', 'omniform' ),
						'impressions' => esc_html__( 'Impressions', 'omniform' ),
						'conversion'  => esc_html__( 'Conversion Rate', 'omniform' ),
					),
					$part_two,
				);
			}
		);

		// Add "Conversion Rate" column to CPT.
		add_action(
			'manage_omniform_posts_custom_column',
			function ( $column_key, $post_id ) {
				if ( 'conversion' !== $column_key ) {
					return;
				}

				$impressions = (int) get_post_meta( $post_id, '_omniform_impressions', true );
				$responses   = (int) get_post_meta( $post_id, '_omniform_responses', true );

				if ( 0 === $impressions ) {
					echo esc_attr( '0%' );
					return;
				}

				if ( class_exists( '\NumberFormatter' ) ) {
					$formatter = new \NumberFormatter( 'en_US', \NumberFormatter::PERCENT );
					$formatted = $formatter->format( $responses / $impressions ?? 0 );
				} else {
					$formatted = $impressions ? round( $responses / $impressions * 100 ) . '%' : '0%';
				}

				echo esc_attr( $formatted );
			},
			10,
			2
		);

		// Add "Responses" column to CPT.
		add_action(
			'manage_omniform_posts_custom_column',
			function ( $column_key, $post_id ) {
				if ( 'responses' !== $column_key ) {
					return;
				}

				$responses = (int) get_post_meta( $post_id, '_omniform_responses', true );

				if ( class_exists( '\NumberFormatter' ) ) {
					$formatter = new \NumberFormatter( 'en_US', \NumberFormatter::PADDING_POSITION );
					$formatted = $formatter->format( $responses );
				} else {
					$formatted = number_format( $responses );
				}

				echo esc_attr( $formatted );
			},
			10,
			2
		);

		// Add "Impressions" column to CPT.
		add_action(
			'manage_omniform_posts_custom_column',
			function ( $column_key, $post_id ) {
				if ( 'impressions' !== $column_key ) {
					return;
				}

				$impressions = (int) get_post_meta( $post_id, '_omniform_impressions', true );

				if ( class_exists( '\NumberFormatter' ) ) {
					$formatter = new \NumberFormatter( 'en_US', \NumberFormatter::PADDING_POSITION );
					$formatted = $formatter->format( $impressions );
				} else {
					$formatted = number_format( $impressions );
				}

				echo esc_attr( $formatted );
			},
			10,
			2
		);

		// Add responses quick link on CPT table list.
		add_filter(
			'post_row_actions',
			function ( $actions, $post ) {
				if ( 'omniform' !== $post->post_type ) {
					return $actions;
				}

				$actions['responses'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( admin_url( sprintf( 'edit.php?post_type=omniform_response&omniform_id=%d', $post->ID ) ) ),
					/* translators: %s: Form title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221; responses', 'omniform' ), $post->post_title ) ),
					esc_html__( 'Responses', 'omniform' ),
				);

				return $actions;
			},
			10,
			2
		);

		// Add custom columns to Responses CPT.
		add_filter(
			'manage_omniform_response_posts_columns',
			function ( $columns ) {
				return array(
					'cb'       => $columns['cb'],
					'title'    => $columns['title'],
					'form'     => esc_html__( 'Form', 'omniform' ),
					'formdata' => esc_html__( 'Form Data', 'omniform' ),
					'date'     => $columns['date'],
				);
			}
		);

		// Add "Form" column to Responses CPT.
		add_action(
			'manage_omniform_response_posts_custom_column',
			function ( $column_key, $post_id ) {
				if ( 'form' !== $column_key ) {
					return;
				}

				$form_id = (int) get_post_meta( $post_id, '_omniform_id', true );
				$form    = $this->getContainer()->get( Form::class )->get_instance( $form_id );

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

				printf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( admin_url( sprintf( 'post.php?post=%d&action=edit', $form->get_id() ) ) ),
					/* translators: %s: Form title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221; responses', 'omniform' ), $form->get_title() ) ),
					esc_attr( $form->get_title() ),
				);
			},
			10,
			2
		);

		// Add "Form Data" column to Responses CPT.
		add_action(
			'manage_omniform_response_posts_custom_column',
			function ( $column_key, $post_id ) {
				if ( 'formdata' !== $column_key ) {
					return;
				}

				echo wp_kses_post(
					$this->getContainer()->get( Form::class )->response_text_content( $post_id )
				);
			},
			10,
			2
		);

		// Remove default meta boxes.
		add_action(
			'add_meta_boxes_omniform_response',
			function () {
				remove_meta_box( 'submitdiv', 'omniform_response', 'side' );
				remove_meta_box( 'postcustom', 'omniform_response', 'normal' );
				remove_meta_box( 'slugdiv', 'omniform_response', 'normal' );
			}
		);

		// Filter responses by form id.
		add_filter(
			'parse_query',
			function ( $query ) {
				if ( ! ( is_admin() && $query->is_main_query() ) ) {
					return $query;
				}

				if ( 'omniform_response' !== $query->query['post_type'] ) {
					return $query;
				}

				if ( empty( $_GET['omniform_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					return $query;
				}

				$query->set(
					'meta_query',
					array(
						array(
							'key'   => '_omniform_id',
							'value' => (int) $_GET['omniform_id'], // phpcs:ignore WordPress.Security.NonceVerification
						),
					)
				);

				return $query;
			}
		);

		// Render response data instead of the editor.
		add_action(
			'edit_form_after_editor',
			function ( $post ) {
				if ( 'omniform_response' !== $post->post_type ) {
					return;
				}

				echo wp_kses_post(
					$this->getContainer()->get( Form::class )->response_text_content( $post->ID )
				);
			}
		);

		// Filter allowed blocks in the editor.
		add_filter(
			'allowed_block_types_all',
			function ( $allowed_block_types, $block_editor_context ) {
				if (
					'core/edit-post' === $block_editor_context->name &&
					'omniform' === $block_editor_context->post->post_type
				) {
					return array(
						'omniform/button',
						'omniform/fieldset',
						'omniform/form',
						'omniform/field',
						'omniform/label',
						'omniform/input',
						'omniform/hidden',
						'omniform/textarea',
						'omniform/select',
						'omniform/select-group',
						'omniform/select-option',
						'omniform/captcha',
						'omniform/response-notification',
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
						'core/missing',
						'core/paragraph',
						'core/pattern',
						'core/preformatted',
						'core/separator',
						'core/site-logo',
						'core/site-tagline',
						'core/site-title',
						'core/spacer',
						'core/table',
						'core/video',
					);
				}
				return $allowed_block_types;
			},
			10,
			2
		);

		// Control when blocks can be inserted.
		add_filter(
			'block_type_metadata',
			function ( $metadata ) {
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

				$skip = array(
					'omniform/form',
					'omniform/input',
					'omniform/label',
					'omniform/select',
					'omniform/textarea',
					'omniform/select-option',
					'omniform/select-group',
				);

				if (
					str_starts_with( $metadata['name'], 'omniform' ) &&
					! in_array( $metadata['name'], $skip, true )
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
	public function register_post_type() {
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
				'show_ui'               => true,
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M3.33 15.424a4.842 4.842 0 0 1 0-6.848l.207-.208v-2.42a2.421 2.421 0 0 1 2.421-2.422H8.38l.086-.086a4.842 4.842 0 0 1 6.848 0l.086.086h2.665a2.421 2.421 0 0 1 2.421 2.421v2.665a4.842 4.842 0 0 1 0 6.776v2.665a2.421 2.421 0 0 1-2.421 2.42h-2.665l-.086.087a4.842 4.842 0 0 1-6.848 0l-.086-.086H5.96a2.421 2.421 0 0 1-2.422-2.421v-2.421l-.207-.208ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z" clip-rule="evenodd"/></svg>' ),
				'show_in_rest'          => true,
				'rest_namespace'        => 'omniform/v1',
				'rest_base'             => 'forms',
				'rest_controller_class' => \OmniForm\Plugin\RestApi\ResponsesController::class,
				'map_meta_cap'          => true,
				'capabilities'          => array(
					'create_posts'           => 'edit_theme_options',
					'delete_posts'           => 'edit_theme_options',
					'delete_others_posts'    => 'edit_theme_options',
					'delete_private_posts'   => 'edit_theme_options',
					'delete_published_posts' => 'edit_theme_options',
					'edit_posts'             => 'edit_theme_options',
					'edit_others_posts'      => 'edit_theme_options',
					'edit_private_posts'     => 'edit_theme_options',
					'edit_published_posts'   => 'edit_theme_options',
					'publish_posts'          => 'edit_theme_options',
					'read'                   => 'edit_theme_options',
					'read_private_posts'     => 'edit_theme_options',
				),
				'supports'              => array(
					'title',
					'slug',
					'editor',
					'revisions',
					'custom-fields',
				),
			)
		);

		// If the current user can't edit_theme_options, bail.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

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

		register_post_meta(
			'omniform',
			'notify_email',
			array(
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'string',
						),
					),

				),
				'single'       => true,
				'type'         => 'array',
				'default'      => array(),
			)
		);

		register_post_meta(
			'omniform',
			'notify_email_subject',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
				'default'      => '',
			)
		);

		register_post_type(
			'omniform_response',
			array(
				'labels'            => array(
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
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => 'edit.php?post_type=omniform',
				'show_in_admin_bar' => false,
				'rewrite'           => false,
				'show_in_rest'      => true,
				'rest_namespace'    => 'omniform/v1',
				'rest_base'         => 'responses',
				'map_meta_cap'      => true,
				'capabilities'      => array(
					'create_posts'           => 'do_not_allow',
					'delete_posts'           => 'edit_theme_options',
					'delete_others_posts'    => 'edit_theme_options',
					'delete_private_posts'   => 'edit_theme_options',
					'delete_published_posts' => 'edit_theme_options',
					'edit_posts'             => 'edit_theme_options',
					'edit_others_posts'      => 'edit_theme_options',
					'edit_private_posts'     => 'edit_theme_options',
					'edit_published_posts'   => 'edit_theme_options',
					'publish_posts'          => 'edit_theme_options',
					'read'                   => 'edit_theme_options',
					'read_private_posts'     => 'edit_theme_options',
				),
				'supports'          => array(
					'custom-fields',
				),
			)
		);
	}

	/**
	 * Register the plugin settings.
	 */
	public function register_settings() {
		// If the current user can't edit_theme_options, bail.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$options = array(
			'hcaptcha'    => 'hCaptcha',
			'recaptchav2' => 'reCAPTCHA v2',
			'recaptchav3' => 'reCAPTCHA v3',
			'turnstile'   => 'Turnstile',
		);

		foreach ( $options as $option => $label ) {
			register_setting(
				'omniform',
				'omniform_' . $option . '_site_key',
				array(
					'type'              => 'string',
					/* translators: %s: The name of the captcha service. */
					'description'       => sprintf( __( 'The site key for %s.', 'omniform' ), $label ),
					'sanitize_callback' => 'sanitize_text_field',
					'show_in_rest'      => true,
					'default'           => '',
				)
			);

			register_setting(
				'omniform',
				'omniform_' . $option . '_secret_key',
				array(
					'type'              => 'string',
					/* translators: %s: The name of the captcha service. */
					'description'       => sprintf( __( 'The secret key for %s.', 'omniform' ), $label ),
					'sanitize_callback' => 'sanitize_text_field',
					'show_in_rest'      => true,
					'default'           => '',
				)
			);
		}
	}

	/**
	 * Disable admin notices on omniform screens.
	 */
	public function disable_admin_notices() {
		$current_screen = get_current_screen();

		// Only disable notices on omniform screens.
		if ( strpos( $current_screen->id, 'omniform' ) === false ) {
			return;
		}

		// Prevent default hooks rendering content to the page.
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		// Add our own notices after this.

		add_action( 'admin_notices', array( $this, 'render_newsletter_notice' ) );
	}

	/**
	 * Render the newsletter notice.
	 *
	 * Retrieves the dismissed version of the newsletter notice for the current user and compares it with the current version of the plugin.
	 * If the dismissed version is greater than or equal to the current version, the notice is not shown.
	 */
	public function render_newsletter_notice() {
		$current_screen = get_current_screen();

		// Don't show the notice on response editor screens.
		if ( 'omniform_response' === $current_screen->id ) {
			return;
		}

		$dismissed_version = get_user_meta( get_current_user_id(), 'omniform_dismissed_newsletter_notice', true );

		$semver_regex      = '/(\d+\.\d+)\.\d+/';
		$dismissed_version = preg_replace( $semver_regex, '$1', $dismissed_version );
		$current_version   = preg_replace( $semver_regex, '$1', $this->getContainer()->version() );

		if ( $dismissed_version && version_compare( $dismissed_version, $current_version, '>=' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-info" style="position:relative;"><p>%s <a href="%s" target="_blank">%s</a></p><a style="position:absolute;top:0;right:0;padding:10px;color:#787c82;" href="%s">%s</a></div>',
			esc_html__( 'Want to stay up to date with OmniForm news?', 'omniform' ),
			esc_url( 'https://omniform.io/omniform/newsletter?utm_source=omniform&utm_medium=plugin&utm_content=admin_notice' ),
			esc_html__( 'Sign up for our newsletter!', 'omniform' ),
			esc_url( add_query_arg( 'dismiss_newsletter_notice', '' ) ),
			esc_html__( 'Dismiss', 'omniform' )
		);
	}

	/**
	 * Dismiss the newsletter notice.
	 */
	public function dismiss_newsletter_notice() {
		if ( isset( $_GET['dismiss_newsletter_notice'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			update_user_meta( get_current_user_id(), 'omniform_dismissed_newsletter_notice', $this->getContainer()->version() );
		}
	}

	/**
	 * Filter the_content on singular forms to render the form block.
	 *
	 * @param string $content The post content.
	 */
	public function render_singular_template( $content ) {
		return ( ! is_singular( 'omniform' ) || ! is_main_query() )
			? $content
			: do_blocks( '<!-- wp:omniform/form {"ref":' . get_the_ID() . '} /-->' );
	}

	/**
	 * Filter block patterns for the CPT block editor.
	 *
	 * Removes block patterns not specifically registered for the custom post type.
	 */
	public function filter_block_patterns_on_admin() {
		if ( ! is_admin() ) {
			return;
		}

		if (
			'post.php' !== $GLOBALS['pagenow'] ||
			empty( $_GET['post'] ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			return;
		}

		if ( 'omniform' !== get_post_type( (int) $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$this->filter_block_patterns();

		// Prevent Block Directory.
		remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
	}

	/**
	 * Filter block patterns in REST API responses.
	 *
	 * Removes block patterns not specifically registered for the custom post type.
	 */
	public function filter_block_patterns_on_rest_api() {
		if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
			return;
		}

		$url_parts = wp_parse_url( sanitize_url( $_SERVER['HTTP_REFERER'] ) );

		$query_args = array();
		if ( ! empty( $url_parts['query'] ) ) {
			parse_str( $url_parts['query'], $query_args );
		}

		$is_edit_post   = '/wp-admin/post.php' === $url_parts['path'];
		$is_create_post = '/wp-admin/post-new.php' === $url_parts['path'];

		if (
			// Only filter block patterns on edit and create post screens.
			( ! $is_edit_post && ! $is_create_post ) ||
			// Only filter block patterns for the omniform CPT.
			( $is_create_post && 'omniform' !== $query_args['post_type'] ) ||
			( $is_edit_post && 'omniform' !== get_post_type( (int) $query_args['post'] ) )
		) {
			return;
		}

		$this->filter_block_patterns();
	}

	/**
	 * Removes block patterns not registered specifically for CPT.
	 */
	private function filter_block_patterns() {
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
