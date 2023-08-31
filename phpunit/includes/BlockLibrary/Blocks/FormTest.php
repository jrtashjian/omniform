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
		$this->register_block_type( new FormBlock() );
	}

	/**
	 * Make sure the block does not render markup if a form is not found.
	 */
	public function test_does_not_render_without_form() {
		$ref = 1;

		$this->assertEmpty( $this->render_block_with_attributes() );

		// Mock the \OmniForm\Plugin\Form::class.
		$mock = $this->getMockBuilder( \OmniForm\Plugin\Form::class )
			->disableOriginalConstructor()
			->getMock();
		omniform()->addShared( \OmniForm\Plugin\Form::class, $mock );

		// Visitors should not see anything.
		$this->assertEmpty( $this->render_block_with_attributes( array( 'ref' => $ref ) ) );

		// Editors should see a notice.
		wp_set_current_user( 1 );
		$this->assertStringContainsString(
			'Form ID &#8220;' . $ref . '&#8221; has been removed.',
			$this->render_block_with_attributes( array( 'ref' => $ref ) )
		);

		// create a WP_Post with a post_Type of omniform.
		$post = $this->factory()->post->create_and_get(
			array(
				'post_type' => 'omniform',
			)
		);

		// Expect the get_instance method to be called once with the post ID.
		$mock->expects( $this->once() )
			->method( 'get_instance' )
			->with( $post->ID )
			->willReturn( $mock );

		// Mock get_id to return the post ID.
		$mock->method( 'get_id' )->willReturn( $post->ID );

		// Mock is_published and is_private to fake a published form.
		$mock->expects( $this->once() )
			->method( 'is_published' )
			->willReturn( true );

		$mock->expects( $this->once() )
			->method( 'is_private' )
			->willReturn( false );

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