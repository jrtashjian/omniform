<?php
/**
 * Server-side renderer for the cancel comment reply link block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders WordPress's cancel-reply link with block wrapper attributes.
 *
 * Delegates link generation to get_cancel_comment_reply_link(), then injects
 * block supports attributes onto the anchor element.
 */
class PostCommentsFormCancelReplyLink extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render(): string {
		return str_replace(
			'<a',
			'<a ' . get_block_wrapper_attributes(),
			$this->cancel_reply_link()
		);
	}

	/**
	 * Cancel reply link markup from WordPress core.
	 *
	 * @return string
	 */
	private function cancel_reply_link(): string {
		return get_cancel_comment_reply_link(
			$this->get_block_attribute( 'linkText' ),
			$this->get_block_context( 'postId' )
		);
	}
}
