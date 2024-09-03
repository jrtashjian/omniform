<?php
/**
 * The PostCommentsFormTitle block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The PostCommentsFormTitle block class.
 */
class PostCommentsFormTitle extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render() {
		$tag_name = 'h3';
		if ( $this->get_block_attribute( 'level' ) ) {
			$tag_name = 'h' . $this->get_block_attribute( 'level' );
		}

		$no_reply_text = $this->get_block_attribute( 'noReplyText' );
		$reply_text    = trim( $this->get_block_attribute( 'replyText' ) );

		ob_start();
		comment_form_title(
			empty( $no_reply_text ) ? false : $no_reply_text,
			empty( $reply_text ) ? false : $reply_text,
			true,
			$this->get_block_context( 'postId' )
		);
		$comment_form_title = ob_get_clean();

		return sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			esc_html( $tag_name ),
			get_block_wrapper_attributes(
				array(
					'id'    => 'reply-title',
					'class' => 'comment-reply-title',
				)
			),
			$comment_form_title
		);
	}
}
