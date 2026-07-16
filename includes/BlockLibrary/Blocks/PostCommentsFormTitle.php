<?php
/**
 * Server-side renderer for the comment form title block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * Renders the comment reply title heading for post comment forms.
 *
 * Builds the title via comment_form_title() using optional no-reply and reply
 * text attributes, then wraps the result in a configurable heading level.
 */
class PostCommentsFormTitle extends BaseBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render(): string {
		return sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			esc_html( $this->heading_tag() ),
			get_block_wrapper_attributes( $this->extra_wrapper_attributes() ),
			$this->comment_form_title()
		);
	}

	/**
	 * Heading tag name derived from the level attribute.
	 *
	 * @return string
	 */
	private function heading_tag(): string {
		$level = $this->get_block_attribute( 'level' );

		return $level ? 'h' . $level : 'h3';
	}

	/**
	 * Extra attributes for get_block_wrapper_attributes().
	 *
	 * @return array<string, string>
	 */
	private function extra_wrapper_attributes(): array {
		return array(
			'id'    => 'reply-title',
			'class' => 'comment-reply-title',
		);
	}

	/**
	 * Comment form title text from WordPress core.
	 *
	 * @return string
	 */
	private function comment_form_title(): string {
		$no_reply_text = $this->get_block_attribute( 'noReplyText' );
		$reply_text    = trim( (string) ( $this->get_block_attribute( 'replyText' ) ?? '' ) );

		ob_start();
		comment_form_title(
			empty( $no_reply_text ) ? false : $no_reply_text,
			empty( $reply_text ) ? false : $reply_text,
			true,
			$this->get_block_context( 'postId' )
		);

		return (string) ob_get_clean();
	}
}
