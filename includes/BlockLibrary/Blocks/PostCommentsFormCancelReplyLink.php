<?php
/**
 * The PostCommentsFormCancelReplyLink block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The PostCommentsFormCancelReplyLink block class.
 */
class PostCommentsFormCancelReplyLink extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render() {
		$link_text = $this->get_block_attribute( 'linkText' );

		$cancel_comment_reply_link = get_cancel_comment_reply_link(
			$this->get_block_attribute( 'linkText' ),
			$this->get_block_context( 'postId' )
		);

		return str_replace(
			'<a',
			'<a ' . get_block_wrapper_attributes(),
			$cancel_comment_reply_link
		);
	}
}
