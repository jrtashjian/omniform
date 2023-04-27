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
		add_action( 'omniform_activate', array( $this, 'activation' ) );
		add_action( 'init', array( $this, 'registerBlocks' ) );
		add_action( 'init', array( $this, 'registerPatterns' ) );
		add_filter( 'block_categories_all', array( $this, 'registerCategories' ) );
	}

	/**
	 * Create the default forms.
	 */
	public function activation() {
		$existing_forms = get_posts(
			array(
				'post_status' => 'any',
				'post_type'   => 'omniform',
			)
		);

		if ( ! empty( $existing_forms ) ) {
			return;
		}

		foreach ( $this->getBlockPatterns() as $form ) {
			wp_insert_post(
				array(
					'post_type'    => 'omniform',
					'post_status'  => 'draft',
					'post_name'    => $form['name'],
					'post_title'   => $form['title'],
					'post_content' => $form['content'],
				)
			);
		}
	}

	/**
	 * Get an array of block patterns from the BlockPatterns/ directory,.
	 *
	 * @return array
	 */
	private function getBlockPatterns() {
		$patterns = glob( __DIR__ . '/BlockPatterns/*.php' );

		return array_map(
			function( $pattern ) {
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
	public function registerBlocks() {
		$blocks = array(
			Blocks\Form::class,
			Blocks\Field::class,
			Blocks\Label::class,
			Blocks\Input::class,
			Blocks\Textarea::class,
			Blocks\Select::class,
			Blocks\Button::class,
			Blocks\Fieldset::class,
			Blocks\SelectGroup::class,
			Blocks\SelectOption::class,
			Blocks\Captcha::class,
		);

		foreach ( $blocks as $block ) {
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
							'viewportWidth' => 768,
						),
					);
				}
			}

			wp_reset_postdata();

			register_block_type(
				$block_object->blockTypeMetadata(),
				array(
					'render_callback' => array( $block_object, 'renderBlock' ),
					'variations'      => $variations,
				)
			);
		}
	}

	/**
	 * Registers the form block patterns.
	 */
	public function registerPatterns() {
		register_block_pattern_category(
			'omniform',
			array(
				'label'       => __( 'Omniform', 'omniform' ),
				'description' => __( 'Common form templates to get you started quickly.', 'omniform' ),
			)
		);

		$pattern_defaults = array(
			'postTypes'     => array( 'omniform' ),
			'blockTypes'    => array( 'omniform/form' ),
			'categories'    => array( 'omniform' ),
			'viewportWidth' => 768,
		);

		foreach ( $this->getBlockPatterns() as $pattern ) {
			register_block_pattern(
				'omniform/' . $pattern['name'],
				wp_parse_args(
					$pattern,
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
	public function registerCategories( $block_categories ) {

		$block_categories[] = array(
			'slug'  => 'omniform',
			'title' => __( 'Forms', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-control-simple',
			'title' => __( 'Simple Controls', 'omniform' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'omniform-control-group',
			'title' => __( 'Grouped Controls', 'omniform' ),
			'icon'  => null,
		);

		return $block_categories;
	}
}
