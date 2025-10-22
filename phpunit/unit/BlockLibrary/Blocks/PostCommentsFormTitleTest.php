<?php
/**
 * Tests for PostCommentsFormTitle.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\PostCommentsFormTitle;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for PostCommentsFormTitle.
 */
class PostCommentsFormTitleTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var PostCommentsFormTitle
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new PostCommentsFormTitle();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		\WP_Mock::userFunction( 'comment_form_title' )->andReturnUsing(
			function () {
				echo 'Reply';
			}
		);
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'id="reply-title" class="comment-reply-title"' );
		\WP_Mock::userFunction( 'esc_html' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);

		$block = $this->createBlockWithContext( array( 'postId' => 1 ) );

		$result = $this->block->render_block(
			array(
				'level'     => 3,
				'replyText' => 'Reply',
			),
			'',
			$block
		);
		$this->assertEquals( '<h3 id="reply-title" class="comment-reply-title">Reply</h3>', $result );
	}
}
