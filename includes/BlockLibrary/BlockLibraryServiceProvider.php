<?php
/**
 * The BlockLibraryServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;
use WP_Query;
use WP_Theme_JSON_Data;

/**
 * The BlockLibraryServiceProvider class.
 */
class BlockLibraryServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface {
	/**
	 * Get the services provided by the provider.
	 *
	 * @param string $id The service to check.
	 *
	 * @return array
	 */
	public function provides( string $id ): bool {
		$services = array();

		return in_array( $id, $services, true );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register(): void {}

	/**
	 * Bootstrap any application services by hooking into WordPress with actions/filters.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'admin_init', array( $this, 'register_patterns' ) );

		add_filter( 'block_categories_all', array( $this, 'register_categories' ) );
		add_filter( 'block_type_metadata_settings', array( $this, 'update_layout_support' ), 10, 2 );

		add_filter( 'wp_theme_json_data_blocks', array( $this, 'register_global_block_styles' ) );
	}

	/**
	 * Get an array of block patterns from the BlockPatterns/ directory,.
	 *
	 * @return array
	 */
	private function get_block_patterns() {
		$patterns = glob( __DIR__ . '/BlockPatterns/*.php' );

		return array_map(
			function ( $pattern ) {
				$path_parts = pathinfo( $pattern );
				return array_merge(
					array( 'name' => $path_parts['filename'] ),
					include $pattern,
				);
			},
			$patterns
		);
	}

	/**
	 * Register the blocks.
	 */
	public function register_blocks() {
		$blocks = array(
			Blocks\Form::class,
			Blocks\Field::class,
			Blocks\Label::class,
			Blocks\Input::class,
			Blocks\Hidden::class,
			Blocks\Textarea::class,
			Blocks\Select::class,
			Blocks\Button::class,
			Blocks\Fieldset::class,
			Blocks\SelectGroup::class,
			Blocks\SelectOption::class,
			Blocks\Captcha::class,
			Blocks\ResponseNotification::class,
			Blocks\PostCommentsFormTitle::class,
			Blocks\PostCommentsFormCancelReplyLink::class,
			Blocks\ConditionalGroup::class,
		);

		foreach ( $blocks as $block ) {
			/** @var Blocks\BaseBlock */ // phpcs:ignore
			$block_object = new $block();

			$variations = array();

			if ( Blocks\Form::class === $block ) {
				$wp_query_args   = array(
					'post_status'    => array( 'draft', 'publish' ),
					'post_type'      => 'omniform',
					'posts_per_page' => -1,
					'no_found_rows'  => true,
				);
				$variation_query = new WP_Query( $wp_query_args );

				foreach ( $variation_query->posts as $post ) {
					$variations[] = array(
						'name'       => 'omniform//' . $post->post_name,
						'title'      => $post->post_title,
						'attributes' => array(
							'ref' => $post->ID,
						),
						'scope'      => array( 'inserter', 'transform' ),
						'example'    => array(
							'attributes'    => array(
								'ref' => $post->ID,
							),
							'viewportWidth' => (int) ( $GLOBALS['content_width'] ?? 768 ),
						),
					);
				}
			}

			wp_reset_postdata();

			register_block_type(
				$block_object->block_type_metadata(),
				array(
					'render_callback' => array( $block_object, 'render_block' ),
					'variations'      => $variations,
				)
			);
		}

		/** @var \OmniForm\Application */ // phpcs:ignore
		$container = $this->getContainer();

		wp_add_inline_script(
			'omniform-form-editor-script',
			'const omniform = ' . wp_json_encode(
				array(
					'assetsUrl' => esc_url( $container->base_url( 'assets/' ) ),
				)
			)
		);
	}

	/**
	 * Registers the form block patterns.
	 */
	public function register_patterns() {
		register_block_pattern_category(
			'omniform',
			array(
				'label'       => esc_attr__( 'OmniForm', 'omniform' ),
				'description' => esc_attr__( 'Common form templates to get you started quickly.', 'omniform' ),
			)
		);

		$pattern_defaults = array(
			'categories'    => array( 'omniform' ),
			'viewportWidth' => (int) ( $GLOBALS['content_width'] ?? 768 ),
		);

		foreach ( $this->get_block_patterns() as $pattern ) {
			// Ensure these patterns are not registered on the site editor.
			if ( ! in_array( $GLOBALS['pagenow'], array( 'site-editor.php' ), true ) ) {
				register_block_pattern(
					'omniform/standard-' . $pattern['name'],
					wp_parse_args(
						array_merge(
							$pattern,
							array(
								'postTypes'  => array( 'omniform' ),
								'blockTypes' => array( 'omniform/form', 'core/post-content' ),
							),
						),
						$pattern_defaults
					)
				);
			}

			$block_attributes = array_merge(
				array(
					'form_title' => $pattern['title'],
				),
				isset( $pattern['settings'] )
					? array_map( 'esc_attr', $pattern['settings'] )
					: array()
			);

			register_block_pattern(
				'omniform/standalone' . $pattern['name'],
				wp_parse_args(
					array_merge(
						$pattern,
						array(
							'postTypes' => array( 'post', 'page', 'wp_template', 'wp_template_part' ),
							'content'   => sprintf(
								'<!-- wp:omniform/form %s -->%s<!-- /wp:omniform/form -->',
								wp_json_encode( $block_attributes ),
								$pattern['content']
							),
						),
					),
					$pattern_defaults
				)
			);
		}
	}

	/**
	 * Filters the default array of categories for block types.
	 *
	 * @param array[] $block_categories     Array of categories for block types.
	 */
	public function register_categories( $block_categories ) {

		$block_categories[] = array(
			'slug'  => 'omniform',
			'title' => esc_attr__( 'OmniForm', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-standard-fields',
			'title' => esc_attr__( 'Standard Fields', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-advanced-fields',
			'title' => esc_attr__( 'Advanced Fields', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-grouped-fields',
			'title' => esc_attr__( 'Grouped Fields', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-conditional-groups',
			'title' => esc_attr__( 'Conditional Groups', 'omniform' ),
			'icon'  => null,
		);

		return $block_categories;
	}

	/**
	 * Stabilize layout support for blocks.
	 *
	 * @param array $settings Array of determined settings for registering a block type.
	 * @param array $metadata Metadata provided for registering a block type.
	 *
	 * @return array
	 */
	public function update_layout_support( $settings, $metadata ) {
		global $wp_version;
		if (
			// Layout support was stabilized in WP 6.3.
			version_compare( $wp_version, '6.3', '>=' ) &&
			false !== strpos( $metadata['name'], 'omniform' ) &&
			isset( $settings['supports']['__experimentalLayout'] )
		) {
			// Rename '__experimentalLayout' to 'layout'.
			$settings['supports']['layout'] = $settings['supports']['__experimentalLayout'];
			unset( $settings['supports']['__experimentalLayout'] );
		}

		return $settings;
	}

	/**
	 * Get the default global styles.
	 *
	 * @return array
	 */
	private function default_global_styles() {
		$input_styles = array(
			'border'     => array(
				'radius' => '0.25em',
				'width'  => '1px',
				'style'  => 'solid',
			),
			'spacing'    => array(
				'padding' => array(
					'top'    => '0.5em',
					'bottom' => '0.5em',
					'left'   => '0.75em',
					'right'  => '0.75em',
				),
			),
			'typography' => array(
				'fontFamily' => 'inherit',
				'fontSize'   => 'inherit',
				'lineHeight' => 'inherit',
			),
			'css'        => '&:is(:focus, :focus-visible) { outline-color:currentColor !important; }',
		);

		return array(
			'omniform/field'                 => array(
				'spacing' => array(
					'blockGap' => '0.5em',
				),
			),
			'omniform/label'                 => array(
				'typography' => array(
					'fontSize' => 'inherit',
				),
			),
			'omniform/input'                 => $input_styles,
			'omniform/select'                => $input_styles,
			'omniform/textarea'              => array_merge_recursive(
				$input_styles,
				array(
					'dimensions' => array(
						'minHeight' => '230px',
					),
				),
			),
			'omniform/response-notification' => array(
				'border'     => array(
					'left' => array(
						'style' => 'solid',
						'width' => '6px',
						'color' => 'var(--wp--preset--color--vivid-cyan-blue,#0693e3)',
					),
				),
				'spacing'    => array(
					'padding' => array(
						'bottom' => '0.5em',
						'left'   => '1.5em',
						'right'  => '1.5em',
						'top'    => '0.5em',
					),
				),
				'variations' => array(
					'success' => array(
						'border' => array(
							'left' => array(
								'color' => 'var(--wp--preset--color--vivid-green-cyan,#00d084)',
							),
						),
					),
					'error'   => array(
						'border' => array(
							'left' => array(
								'color' => 'var(--wp--preset--color--vivid-red,#cf2e2e)',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Get global styles for the current theme.
	 *
	 * @return array
	 */
	private function global_styles_for_current_theme() {
		$theme = wp_get_theme();

		$theme_slug = $theme->get_stylesheet();
		$method     = 'global_styles_for_' . $theme_slug;

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}

		return array();
	}

	/**
	 * Register global block styles.
	 *
	 * @param WP_Theme_JSON_Data $theme_json The theme JSON data.
	 *
	 * @return WP_Theme_JSON_Data
	 */
	public function register_global_block_styles( WP_Theme_JSON_Data $theme_json ) {
		$data = $theme_json->update_with(
			array(
				'version' => 3,
				'styles'  => array(
					'blocks' => array_replace_recursive(
						$this->default_global_styles(),
						$this->global_styles_for_current_theme(),
					),
				),
			)
		);

		return $data;
	}

	/**
	 * Get global styles for the Twenty Twenty Four theme.
	 *
	 * @see https://wordpress.org/themes/twentytwentyfour/
	 *
	 * @return array
	 */
	public function global_styles_for_twentytwentyfour() {
		$input_styles = array(
			'border'     => array(
				'radius' => '0.33em',
				'color'  => 'var(--wp--preset--color--contrast-2)',
			),
			'color'      => array(
				'background' => 'var(--wp--preset--color--base-2)',
			),
			'spacing'    => array(
				'padding' => array(
					'top'    => 'calc(.667em + 2px)',
					'bottom' => 'calc(.667em + 2px)',
					'left'   => 'calc(.667em + 2px)',
					'right'  => 'calc(.667em + 2px)',
				),
			),
			'typography' => array(
				'fontFamily' => 'var(--wp--preset--font-family--body)',
				'fontSize'   => 'var(--wp--preset--font-size--medium)',
				'lineHeight' => 'normal',
			),
		);

		return array(
			'omniform/field'    => array(
				'spacing' => array(
					'blockGap' => '0.25em',
				),
			),
			'omniform/input'    => $input_styles,
			'omniform/select'   => $input_styles,
			'omniform/textarea' => $input_styles,
			'omniform/button'   => array(
				'css' => '&:is(:focus, :focus-visible) { outline-color:var(--wp--preset--color--contrast); }',
			),
		);
	}

	/**
	 * Get global styles for the Twenty Twenty Five theme.
	 *
	 * @see https://wordpress.org/themes/twentytwentyfive/
	 *
	 * @return array
	 */
	public function global_styles_for_twentytwentyfive() {
		$input_styles = array(
			'border'     => array(
				'radius' => '1.5625rem',
				'color'  => 'var(--wp--preset--color--accent-6)',
			),
			'color'      => array(
				'background' => 'var(--wp--preset--color--accent-5)',
			),
			'spacing'    => array(
				'padding' => array(
					'left'  => '1.5625rem',
					'right' => '1.5625rem',
				),
			),
			'typography' => array(
				'fontSize'   => 'var(--wp--preset--font-size--medium)',
				'lineHeight' => '1.6',
			),
			'css'        => '&:is(:focus, :focus-visible) { outline-color:var(--wp--preset--color--contrast) !important; }',
		);

		return array(
			'omniform/input'    => $input_styles,
			'omniform/select'   => $input_styles,
			'omniform/textarea' => $input_styles,
			'omniform/button'   => array(
				'css' => '&:is(:focus, :focus-visible) { outline-color:var(--wp--preset--color--contrast); }',
			),
		);
	}

	/**
	 * Get global styles for the Kanso theme.
	 *
	 * @see https://wordpress.org/themes/kanso/
	 *
	 * @return array
	 */
	public function global_styles_for_kanso() {
		$input_styles = array(
			'border'  => array(
				'radius' => 'var(--wp--preset--spacing--10)',
				'color'  => 'var(--wp--preset--color--theme-4)',
			),
			'color'   => array(
				'text'       => 'var(--wp--preset--color--theme-6)',
				'background' => 'color-mix(in srgb, var(--wp--preset--color--theme-1) 98%, var(--wp--preset--color--theme-6) 2%)',
			),
			'spacing' => array(
				'padding' => array(
					'top'    => '8px',
					'bottom' => '8px',
					'left'   => '12px',
					'right'  => '12px',
				),
			),
			'css'     => '&:is(:focus, :focus-visible) { outline-color:var(--wp--preset--color--theme-5) !important; }',
		);

		return array(
			'omniform/field'    => array(
				'spacing' => array(
					'blockGap' => '.25em',
				),
			),
			'omniform/label'    => array(
				'color'      => array(
					'text' => 'var(--wp--preset--color--theme-6)',
				),
				'typography' => array(
					'fontSize' => 'var(--wp--preset--font-size--small)',
				),
			),
			'omniform/input'    => $input_styles,
			'omniform/select'   => $input_styles,
			'omniform/textarea' => $input_styles,
			'omniform/button'   => array(
				'css' => '&:is(:focus, :focus-visible) { outline-color:var(--wp--preset--color--theme-5); }',
			),
		);
	}

	/**
	 * Get global styles for the Ollie theme.
	 *
	 * @see https://wordpress.org/themes/ollie/
	 *
	 * @return array
	 */
	public function global_styles_for_ollie() {
		$input_styles = array(
			'border'  => array(
				'radius' => '5px',
				'color'  => 'var(--wp--preset--color--main-accent)',
			),
			'color'   => array(
				'text'       => 'var(--wp--preset--color--main)',
				'background' => 'var(--wp--preset--color--base)',
			),
			'spacing' => array(
				'padding' => array(
					'top'    => '.5em',
					'bottom' => '.5em',
					'left'   => '1em',
					'right'  => '1em',
				),
			),
			'css'     => '&:is(:focus, :focus-visible) { outline-color:var(--wp--preset--color--primary); }',
		);

		return array(
			'omniform/field'    => array(
				'spacing' => array(
					'blockGap' => '.25em',
				),
			),
			'omniform/input'    => $input_styles,
			'omniform/select'   => $input_styles,
			'omniform/textarea' => $input_styles,
			'omniform/button'   => array(
				'css' => '&:is(:focus, :focus-visible) { outline-color:var(--wp--preset--color--primary); }',
			),
		);
	}

	/**
	 * Get global styles for the Rockbase theme.
	 *
	 * @see https://rockbase.co/
	 *
	 * @return array
	 */
	public function global_styles_for_rockbase() {
		$input_styles = array(
			'border'     => array(
				'radius' => '8px',
				'color'  => 'var(--wp--preset--color--foreground-4)',
			),
			'spacing'    => array(
				'padding' => array(
					'top'    => '0.7em',
					'bottom' => '0.6em',
					'left'   => 'clamp(1.5rem, 1.75vw, 3.5rem)',
					'right'  => 'clamp(1.5rem, 1.75vw, 3.5rem)',
				),
			),
			'typography' => array(
				'fontFamily' => 'var(--wp--preset--font-family--primary)',
				'fontSize'   => 'var(--wp--preset--font-size--small)',
			),
			'css'        => '&:is(:focus, :focus-visible) { outline: 1px solid var(--wp--preset--color--primary) !important; }',
		);

		return array(
			'omniform/input'    => $input_styles,
			'omniform/select'   => $input_styles,
			'omniform/textarea' => $input_styles,
			'omniform/button'   => array(
				'css' => '&:is(:focus, :focus-visible) { outline: 1px solid var(--wp--preset--color--primary) !important; }',
			),
		);
	}
}
