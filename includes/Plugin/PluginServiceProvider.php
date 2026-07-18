<?php
/**
 * The PluginServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Analytics\AnalyticsManager;
use OmniForm\Application;
use OmniForm\Dependencies\Respect\Validation;
use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;
use OmniForm\Form\Form as DomainForm;
use OmniForm\Form\Response as DomainResponse;
use OmniForm\Plugin\Http\Request;
use WP_Block_Type;
use WP_Block_Type_Registry;
use WP_REST_Response;
use wpdb;

/**
 * The PluginServiceProvider class.
 */
class PluginServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return bool
	 */
	public function provides( string $id ): bool {
		$services = array(
			Validation\Validator::class,
			wpdb::class,
			Form::class,
			FormFactory::class,
			Response::class,
			ResponseFactory::class,
			QueryBuilder::class,
			Request::class,
			FormSubmitter::class,
		);

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()
			->add( wpdb::class, $GLOBALS['wpdb'] );

		$this->getContainer()
			->add( Validation\Validator::class );

		$this->getContainer()
			->add( Form::class )
			->addArgument( Validation\Validator::class );

		$this->getContainer()
			->add( FormFactory::class )
			->addArgument( $this->getContainer() );

		$this->getContainer()
			->add( Response::class );

		$this->getContainer()
			->add( ResponseFactory::class )
			->addArgument( $this->getContainer() );

		$this->getContainer()
			->add( QueryBuilder::class )
			->addArgument( wpdb::class );

		$this->getContainer()->add(
			Request::class,
			function () {
				// phpcs:ignore WordPress.Security.NonceVerification
				return new Request( $_GET, $_POST, $_FILES, $_SERVER );
			}
		);

		$this->getContainer()->add(
			FormSubmitter::class,
			static function () {
				return new FormSubmitter(
					new FormRepository(),
					new BlockFormSchemaParser(),
					new SubmissionFactory(),
					new RespectSubmissionValidator(),
					new ResponseRepository()
				);
			}
		);
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
		add_action( 'rest_api_init', array( $this, 'register_rest_fields' ) );
		add_filter( 'the_content', array( $this, 'render_singular_template' ) );

		// Send email notification when a response is created.
		add_action(
			'omniform_response_created',
			function ( $response, $form, $context = array() ) {
				if ( $response instanceof DomainResponse && $form instanceof DomainForm ) {
					( new ResponseNotificationMailer() )->send(
						$response,
						$form,
						is_array( $context ) ? $context : array()
					);
					return;
				}

				if ( $response instanceof Response && $form instanceof Form ) {
					wp_mail(
						$form->get_notify_email(),
						$form->get_notify_email_subject(),
						wp_kses( $response->email_content(), array() )
					);
				}
			},
			10,
			3
		);

		// Temporary: preview domain HTML presenter on classic response edit screen.
		add_action(
			'edit_form_top',
			function ( \WP_Post $post ) {
				if ( 'omniform_response' !== $post->post_type ) {
					return;
				}

				try {
					$response  = $this->domain_response_for_post( $post );
					$presenter = new HtmlResponsePresenter();
					$html      = $presenter->present( $response );
				} catch ( \Throwable $exception ) {
					echo '<div class="notice notice-error"><p>' . esc_html( $exception->getMessage() ) . '</p></div>';
					return;
				}

				echo '<div class="omniform-response-preview" style="margin:1em 0;padding:1em;background:#fff;border:1px solid #c3c4c7;">';
				echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- presenter escapes field output.
				printf('<pre>%s</pre>', esc_html( wp_json_encode( $response->to_array(), JSON_PRETTY_PRINT ) ) );
				echo '</div>';
			}
		);

		// Increment form impression count.
		add_action(
			'omniform_form_render',
			function ( $form_id ) {
				$form = $this->getContainer()->get( FormFactory::class )->create_with_id( (int) $form_id );

				if ( ! $form->is_published() || is_admin() || wp_is_serving_rest_request() ) {
					return;
				}

				$this->getContainer()->get( AnalyticsManager::class )->record_impression( $form_id );
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
					$registry = WP_Block_Type_Registry::get_instance();

					$omniform_blocks = array_filter(
						$registry->get_all_registered(),
						function ( WP_Block_Type $block ) {
							return str_starts_with( $block->name, 'omniform/' );
						}
					);

					return array_merge(
						array_keys( $omniform_blocks ),
						array(
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
						)
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

				$request = $this->getContainer()->get( Request::class );

				if (
					! empty( $request->query->get( 'post' ) ) &&
					'omniform' === get_post_type( (int) $request->query->get( 'post' ) )
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

		add_action(
			'admin_menu',
			function () {
				add_menu_page(
					esc_html__( 'OmniForm', 'omniform' ),
					esc_html__( 'OmniForm', 'omniform' ),
					'edit_pages',
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
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M3.33 15.424a4.842 4.842 0 0 1 0-6.848l.207-.208v-2.42a2.421 2.421 0 0 1 2.421-2.422H8.38l.086-.086a4.842 4.842 0 0 1 6.848 0l.086.086h2.665a2.421 2.421 0 0 1 2.421 2.421v2.665a4.842 4.842 0 0 1 0 6.776v2.665a2.421 2.421 0 0 1-2.421 2.42h-2.665l-.086.087a4.842 4.842 0 0 1-6.848 0l-.086-.086H5.96a2.421 2.421 0 0 1-2.422-2.421v-2.421l-.207-.208ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z" clip-rule="evenodd"/></svg>' ),
					2
				);

				add_submenu_page(
					'omniform',
					esc_html__( 'Overview', 'omniform' ),
					esc_html__( 'Overview', 'omniform' ),
					'edit_pages',
					'omniform',
				);

				add_submenu_page(
					'omniform',
					esc_html__( 'Forms', 'omniform' ),
					esc_html__( 'Forms', 'omniform' ),
					'edit_pages',
					'admin.php?page=omniform#/forms',
				);

				add_submenu_page(
					'omniform',
					esc_html__( 'Responses', 'omniform' ),
					esc_html__( 'Responses', 'omniform' ),
					'edit_pages',
					'admin.php?page=omniform#/responses',
				);
			}
		);

		add_action(
			'admin_enqueue_scripts',
			function () {
				$current_screen = get_current_screen();

				$admin_pages = array(
					'toplevel_page_omniform'           => 'dashboard',
					'omniform_page_omniform_forms'     => 'forms',
					'omniform_page_omniform_responses' => 'responses',
				);

				if ( ! array_key_exists( $current_screen->base, $admin_pages ) ) {
					return;
				}

				// Prevent default hooks rendering content to the page.
				remove_all_actions( 'network_admin_notices' );
				remove_all_actions( 'user_admin_notices' );
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );

				$application = $this->getContainer()->get( Application::class );
				$asset_file  = include $application->base_path( 'build/dashboard/index.asset.php' );

				wp_enqueue_script(
					'dashboard-script',
					$application->base_url( 'build/dashboard/index.js' ),
					$asset_file['dependencies'],
					$asset_file['version'],
					true
				);

				wp_enqueue_style(
					'dashboard-style',
					$application->base_url( 'build/dashboard/style-index.css' ),
					array( 'wp-components' ),
					$asset_file['version'],
				);

				$init_script = <<<'JS'
					( function() {
						window._loadOmniform = new Promise( function( resolve ) {
							wp.domReady( function() {
								resolve( omniform.dashboard.initialize( 'omniform', %s ) );
							} );
						} );
					} )();
				JS;

				$script = sprintf(
					$init_script,
					wp_json_encode( array( 'screen' => $admin_pages[ $current_screen->base ] ) )
				);

				wp_add_inline_script( 'dashboard-script', $script );
			}
		);

		// Backwards compatibility: Treat old 'publish' status as 'omniform_unread' for omniform_response post type.
		add_filter(
			'rest_omniform_response_query',
			function ( $args ) {
				$compat = array( 'publish', 'omniform_unread' );

				if ( array_intersect( $compat, $args['post_status'] ) ) {
					$args['post_status'] = array_unique( array_merge( $args['post_status'], $compat ) );
				}

				return $args;
			}
		);
		add_filter(
			'rest_prepare_omniform_response',
			function ( WP_REST_Response $response ) {
				$data = $response->get_data();

				if ( 'publish' === $data['status'] ) {
					$data['status'] = 'omniform_unread';
				}

				$response->set_data( $data );
				return $response;
			}
		);

	}

	/**
	 * Load a domain Response snapshot for an omniform_response post.
	 *
	 * Uses only data stored on the response — never requires the parent form.
	 *
	 * @param \WP_Post $post Response post.
	 *
	 * @throws \InvalidArgumentException If the payload cannot be interpreted.
	 */
	private function domain_response_for_post( \WP_Post $post ): DomainResponse {
		return ( new ResponseRepository() )->from_post( $post );
	}

	/**
	 * Register post type
	 */
	public function register_post_type() {
		register_post_type(
			'omniform',
			array(
				'labels'                => array(
					'name'                     => _x( 'OmniForms', 'post type general name', 'omniform' ),
					'singular_name'            => _x( 'OmniForm', 'post type singular name', 'omniform' ),
					'add_new'                  => _x( 'Create a Form', 'Form', 'omniform' ),
					'add_new_item'             => __( 'Create a Form', 'omniform' ),
					'new_item'                 => __( 'Create a Form', 'omniform' ),
					'edit_item'                => __( 'Edit Form', 'omniform' ),
					'view_item'                => __( 'View Form', 'omniform' ),
					'all_items'                => __( 'Forms', 'omniform' ),
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
				'exclude_from_search'   => true,
				'show_in_menu'          => false,
				'show_in_admin_bar'     => true,
				'show_in_rest'          => true,
				'rest_namespace'        => 'omniform/v1',
				'rest_base'             => 'forms',
				'rest_controller_class' => \OmniForm\Plugin\Api\FormsController::class,
				'map_meta_cap'          => true,
				'capabilities'          => array(
					'create_posts' => 'publish_posts',
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

		register_post_status(
			'omniform_read',
			array(
				'public' => true,
				'label'  => __( 'Read', 'omniform' ),
			)
		);
		register_post_status(
			'omniform_unread',
			array(
				'public' => true,
				'label'  => __( 'Unread', 'omniform' ),
			)
		);
		register_post_status(
			'omniform_spam',
			array(
				'public' => true,
				'label'  => __( 'Spam', 'omniform' ),
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

		// register_post_meta for form submission method and action attributes of the form element.
		register_post_meta(
			'omniform',
			'submit_method',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
				'default'      => '',
			)
		);

		register_post_meta(
			'omniform',
			'submit_action',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
				'default'      => '',
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
				'labels'                          => array(
					'name'               => _x( 'Responses', 'post type general name', 'omniform' ),
					'singular_name'      => _x( 'Response', 'post type singular name', 'omniform' ),
					'all_items'          => __( 'Responses', 'omniform' ),
					'filter_items_list'  => __( 'Filter responses list', 'omniform' ),
					'not_found'          => __( 'No responses found.', 'omniform' ),
					'not_found_in_trash' => __( 'No responses found in Trash.', 'omniform' ),
					'search_items'       => __( 'Search responses', 'omniform' ),
					'view_item'          => __( 'View response', 'omniform' ),
				),
				'public'                          => true,
				'rewrite'                         => false,
				'show_in_rest'                    => true,
				'rest_namespace'                  => 'omniform/v1',
				'rest_base'                       => 'responses',
				'rest_controller_class'           => \OmniForm\Plugin\Api\ResponsesController::class,
				'autosave_rest_controller_class'  => 'stdClass', // Disable autosave endpoints.
				'revisions_rest_controller_class' => 'stdClass', // Disable revisions endpoints.
				'capability_type'                 => 'page',
				'map_meta_cap'                    => true,
				'capabilities'                    => array(
					'create_posts' => 'do_not_allow',
				),
				'supports'                        => array(
					'custom-fields',
				),
			)
		);
	}

	/**
	 * Registers custom REST API fields.
	 */
	public function register_rest_fields() {
		register_rest_field(
			'omniform_response',
			'omniform_form',
			array(
				'get_callback' => array( $this, 'get_response_omniform_form_field' ),
				'schema'       => array(
					'description' => __( 'Form and response summary for the dashboard list and detail panel.', 'omniform' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
				),
			)
		);
	}

	/**
	 * REST field payload for omniform_response → omniform_form.
	 *
	 * Dual-reads domain and legacy post_content via ResponseRepository so the
	 * dashboard list (sender email) and detail panel (fields) work for both.
	 *
	 * @param array<string, mixed> $post Prepared post array from the REST controller.
	 * @return array{
	 *   form_id: int|null,
	 *   form_edit_url: string|null,
	 *   title: string,
	 *   sender_gravatar: string,
	 *   sender_email: string|null,
	 *   sender_ip: string|null,
	 *   fields: list<array{name: string, label: string, type: string, value: string}>
	 * }
	 */
	public function get_response_omniform_form_field( array $post ): array {
		$form_id    = (int) get_post_meta( $post['id'], '_omniform_id', true );
		$form_data  = $this->form_summary_for_response( $form_id );
		$view       = new ResponseViewData();
		$fields     = array();
		$sender_email = null;
		$sender_ip  = null;

		$post_object = get_post( (int) $post['id'] );

		if ( $post_object instanceof \WP_Post ) {
			$sender_ip = get_post_meta( $post_object->ID, '_omniform_user_ip', true );
			$sender_ip = is_string( $sender_ip ) && '' !== $sender_ip ? $sender_ip : null;

			try {
				$response     = ( new ResponseRepository() )->from_post( $post_object );
				$fields       = $view->fields( $response );
				$sender_email = $view->sender_email( $response );
			} catch ( \Throwable $_exception ) {
				// Leave fields/email empty when the snapshot cannot be decoded.
			}
		}

		$email_for_hash = $sender_email ?? '';

		return array(
			'form_id'         => $form_data['id'],
			'form_edit_url'   => $form_data['edit_url'],
			'title'           => $form_data['title'],
			'sender_gravatar' => sanitize_url(
				'https://www.gravatar.com/avatar/' . hash( 'sha256', strtolower( trim( $email_for_hash ) ) ) . '?d=mp'
			),
			'sender_email'    => $sender_email,
			'sender_ip'       => $sender_ip,
			'fields'          => $fields,
		);
	}

	/**
	 * Form title / edit URL for a response's parent form.
	 *
	 * @param int $form_id Parent form post ID.
	 * @return array{id: int|null, title: string, edit_url: string|null}
	 */
	private function form_summary_for_response( int $form_id ): array {
		try {
			$form = $this->getContainer()->get( FormFactory::class )->create_with_id( $form_id );

			return array(
				'id'       => $form->get_id(),
				'title'    => $form->get_title() ?: __( '(no title)', 'omniform' ),
				'edit_url' => sanitize_url(
					admin_url( sprintf( 'post.php?post=%d&action=edit', $form->get_id() ) )
				),
			);
		} catch ( \Exception $_exception ) {
			return array(
				'id'       => null,
				'title'    => __( '(form not found)', 'omniform' ),
				'edit_url' => null,
			);
		}
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

		$request = $this->getContainer()->get( Request::class );

		if (
			'post.php' !== $GLOBALS['pagenow'] ||
			! $request->query->has( 'post' )
		) {
			return;
		}

		if ( 'omniform' !== get_post_type( (int) $request->query->get( 'post' ) ) ) {
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
		$referer = wp_get_referer();

		if ( ! $referer ) {
			return;
		}

		$url_parts = wp_parse_url( $referer );

		if ( false === $url_parts || empty( $url_parts['path'] ) ) {
			return;
		}

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
