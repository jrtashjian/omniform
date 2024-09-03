<?php
/**
 * The BlockLibraryServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary;

use OmniForm\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use OmniForm\Dependencies\League\Container\ServiceProvider\BootableServiceProviderInterface;
use OmniForm\FormTypes\FormTypesManager;
use WP_Query;

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
								'blockTypes' => array( 'omniform/form' ),
							),
						),
						$pattern_defaults
					)
				);
			}

			$block_attributes = array_merge(
				array(
					'form_title' => $pattern['title'],
					'align'      => 'full',
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
}
