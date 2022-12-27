<?php
/**
 * The BlockLibraryServiceProvider class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary;

use OmniForm\ServiceProvider;
use WP_Query;

/**
 * The BlockLibraryServiceProvider class.
 */
class BlockLibraryServiceProvider extends ServiceProvider {
	/**
	 * This method will be used for hooking into WordPress with actions/filters.
	 */
	public function boot() {
		add_action( 'init', array( $this, 'registerBlocks' ) );
		add_action( 'init', array( $this, 'registerPatterns' ) );
		add_filter( 'block_categories_all', array( $this, 'registerCategories' ) );
	}

	/**
	 * Register the blocks.
	 */
	public function registerBlocks() {
		$blocks = array(
			Blocks\Button::class,
			Blocks\FieldInput::class,
			Blocks\FieldSelect::class,
			Blocks\SelectOption::class,
			Blocks\SelectGroup::class,
			Blocks\FieldTextarea::class,
			Blocks\Form::class,
			Blocks\Fieldset::class,
		);

		foreach ( $blocks as $block ) {
			$block_object = $this->app->make( $block );

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
							'attributes' => array(
								'ref' => $post->ID,
							),
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
			'form',
			array( 'label' => __( 'Forms', 'omniform' ) )
		);

		register_block_pattern(
			'omniform/form-contact-me',
			array(
				'title'         => 'Contact Me',
				'postTypes'     => array( 'omniform' ),
				'blockTypes'    => array( 'omniform/form' ),
				'categories'    => array( 'form' ),
				'content'       => '<!-- wp:heading -->
				<h2>' . __( 'Contact Us', 'omniform' ) . '</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>' . __( "If you have any questions or comments, or if you'd like to work with me or collaborate on a project, please don't hesitate to get in touch. I look forward to hearing from you!", 'omniform' ) . '</p>
				<!-- /wp:paragraph -->

				<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Your email address","fieldName":"your-email-address","isRequired":true} /-->

				<!-- wp:omniform/field-textarea {"fieldLabel":"Your message","fieldName":"your-message","height":230,"isRequired":true} /-->

				<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Message"} /-->',
				'viewportWidth' => 640,
			)
		);

		register_block_pattern(
			'omniform/form-pattern-one',
			array(
				'title'         => 'Pattern one',
				'postTypes'     => array( 'omniform' ),
				'blockTypes'    => array( 'omniform/form' ),
				'categories'    => array( 'form' ),
				'content'       => '<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Your email address"} /-->

				<!-- wp:omniform/field-textarea {"fieldLabel":"Your message","height":230} /-->

				<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Message"} /-->',
				'viewportWidth' => 640,
			),
		);

		register_block_pattern(
			'omniform/form-pattern-two',
			array(
				'title'         => 'Pattern two',
				'postTypes'     => array( 'omniform' ),
				'blockTypes'    => array( 'omniform/form' ),
				'categories'    => array( 'form' ),
				'content'       => '<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Your email address"} /-->

				<!-- wp:omniform/field-textarea {"fieldLabel":"Your message","height":230} /-->

				<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Message"} /-->',
				'viewportWidth' => 640,
			),
		);

		register_block_pattern(
			'omniform/form-pattern-three',
			array(
				'title'         => 'Pattern three',
				'postTypes'     => array( 'omniform' ),
				'blockTypes'    => array( 'omniform/form' ),
				'categories'    => array( 'form' ),
				'content'       => '<!-- wp:omniform/field-input {"fieldType":"email","fieldLabel":"Your email address"} /-->

				<!-- wp:omniform/field-textarea {"fieldLabel":"Your message","height":230} /-->

				<!-- wp:omniform/button {"buttonType":"submit","buttonLabel":"Send Message"} /-->',
				'viewportWidth' => 640,
			),
		);
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

	/**
	 * Enqueue required scripts and styles.
	 */
	public function register() {}
}
