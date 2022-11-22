<?php
/**
 * The BlockLibraryServiceProvider class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\BlockLibrary;

use InquiryWP\ServiceProvider;
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
		add_filter( 'render_block_data', array( $this, 'groupFields' ), 10, 3 );
	}

	/**
	 * Register the blocks.
	 */
	public function registerBlocks() {
		$blocks = array(
			Blocks\ButtonSubmit::class,
			Blocks\FieldInput::class,
			Blocks\FieldSelect::class,
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
					'post_type'      => 'inquirywp_form',
					'posts_per_page' => -1,
					'no_found_rows'  => true,
				);
				$variation_query = new WP_Query( $wp_query_args );

				foreach ( $variation_query->posts as $post ) {
					$variations[] = array(
						'name'        => 'inquirywp//' . $post->post_name,
						'title'       => $post->post_title,
						'description' => $post->post_name,
						'attributes'  => array(
							'ref' => $post->ID,
						),
						'scope'       => array( 'inserter' ),
						'example'     => array(
							'attributes' => array(
								'ref' => $post->ID,
							),
						),
					);
				}
			}

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
	 * Filters the block being rendered in render_block(), before it's processed.
	 *
	 * @since 5.1.0
	 * @since 5.9.0 The `$parent_block` parameter was added.
	 *
	 * @param array          $parsed_block The block being rendered.
	 * @param array          $source_block An un-modified copy of $parsed_block, as it appeared in the source content.
	 * @param \WP_Block|null $parent_block If this is a nested block, a reference to the parent block.
	 */
	public function groupFields( $parsed_block, $source_block, $parent_block ) {
		if (
			false !== strpos( $source_block['blockName'], 'inquirywp' ) &&
			! empty( $parent_block ) &&
			'inquirywp/fieldset' === $parent_block->name
		) {
			$parsed_block['attrs']['group'] = sanitize_title( $parent_block->attributes['legend'] );
		}

		return $parsed_block;
	}

	/**
	 * Registers the form block patterns.
	 */
	public function registerPatterns() {

		register_block_pattern_category(
			'form',
			array( 'label' => __( 'Forms', 'inquirywp' ) )
		);

		register_block_pattern(
			'inquirywp/form-pattern-one',
			array(
				'title'         => 'Pattern one',
				'postTypes'     => array( 'inquirywp_form' ),
				'categories'    => array( 'form' ),
				'content'       => '<!-- wp:inquirywp/field-input {"label":"field-text label","help":"field-text help text"} /-->

				<!-- wp:inquirywp/field-textarea {"label":"field-textarea label"} /-->

				<!-- wp:group {"className":"is-layout-flex wp-block-buttons","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
				<div class="wp-block-group is-layout-flex wp-block-buttons"><!-- wp:inquirywp/button-submit /--></div>
				<!-- /wp:group -->',
				'viewportWidth' => 640,
			),
		);

		register_block_pattern(
			'inquirywp/form-pattern-two',
			array(
				'title'         => 'Pattern two',
				'postTypes'     => array( 'inquirywp_form' ),
				'categories'    => array( 'form' ),
				'content'       => '<!-- wp:inquirywp/field-input {"label":"field-text label","help":"field-text help text"} /-->

				<!-- wp:inquirywp/field-textarea {"label":"field-textarea label"} /-->

				<!-- wp:group {"className":"is-layout-flex wp-block-buttons","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
				<div class="wp-block-group is-layout-flex wp-block-buttons"><!-- wp:inquirywp/button-submit /--></div>
				<!-- /wp:group -->',
				'viewportWidth' => 640,
			),
		);

		register_block_pattern(
			'inquirywp/form-pattern-three',
			array(
				'title'         => 'Pattern three',
				'postTypes'     => array( 'inquirywp_form' ),
				'categories'    => array( 'form' ),
				'content'       => '<!-- wp:inquirywp/field-input {"label":"field-text label","help":"field-text help text"} /-->

				<!-- wp:inquirywp/field-textarea {"label":"field-textarea label"} /-->

				<!-- wp:group {"className":"is-layout-flex wp-block-buttons","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
				<div class="wp-block-group is-layout-flex wp-block-buttons"><!-- wp:inquirywp/button-submit /--></div>
				<!-- /wp:group -->',
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
			'slug'  => 'form',
			'title' => __( 'Forms', 'inquirywp' ),
			'icon'  => null,
		);

		$block_categories[] = array(
			'slug'  => 'form-control',
			'title' => __( 'Form Controls', 'inquirywp' ),
			'icon'  => null,
		);

		return $block_categories;
	}

	/**
	 * Enqueue required scripts and styles.
	 */
	public function register() {}
}
