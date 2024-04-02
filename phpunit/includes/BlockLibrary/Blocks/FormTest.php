<?php
/**
 * Tests the Form class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Form;

/**
 * Tests the Form class.
 */
class FormTest extends FormBlockTestCase {
	/**
	 * The block instance to test against.
	 *
	 * @var \OmniForm\BlockLibrary\Blocks\Form
	 */
	protected $block_instance;

	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		omniform()->addShared( \OmniForm\Plugin\Form::class );

		$this->register_block_type( new FormBlock() );
	}

	/**
	 * Make sure the block does not render markup if a form is not found.
	 */
	public function test_does_not_render_without_form() {
		$ref = 1;

		$this->assertEmpty( $this->render_block_with_attributes() );

		// Visitors should not see anything.
		$this->assertEmpty( $this->render_block_with_attributes( array( 'ref' => $ref ) ) );

		// Editors should see a notice.
		wp_set_current_user( 1 );

		$this->assertStringContainsString(
			'<p style="color:var(--wp--preset--color--vivid-red,#cf2e2e);">Form ID &#8220;' . $ref . '&#8221; does not exist.</p>',
			$this->render_block_with_attributes( array( 'ref' => $ref ) )
		);

		// create a WP_Post with a post_Type of omniform.
		$post = $this->factory()->post->create_and_get(
			array(
				'post_type' => 'omniform',
			)
		);

		$this->assertStringContainsString(
			'<form method="post" action="' . rest_url( '/omniform/v1/forms/' . $post->ID . '/responses' ) . '" class="wp-block-omniform-form-block">',
			$this->render_block_with_attributes( array( 'ref' => $post->ID ) )
		);
	}
}

// phpcs:disable
class FormBlock extends Form {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}
