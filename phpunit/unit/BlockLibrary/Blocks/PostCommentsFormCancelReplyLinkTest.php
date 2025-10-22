<?php
/**
 * Tests for PostCommentsFormCancelReplyLink.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\PostCommentsFormCancelReplyLink;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for PostCommentsFormCancelReplyLink.
 */
class PostCommentsFormCancelReplyLinkTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var PostCommentsFormCancelReplyLink
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new PostCommentsFormCancelReplyLink();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		\WP_Mock::userFunction( 'get_cancel_comment_reply_link' )->andReturn( '<a href="#">Cancel</a>' );
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="cancel-link"' );

		$block = $this->createBlockWithContext( array( 'postId' => 1 ) );

		$result = $this->block->render_block( array( 'linkText' => 'Cancel' ), '', $block );
		$this->assertEquals( '<a class="cancel-link" href="#">Cancel</a>', $result );
	}
}
