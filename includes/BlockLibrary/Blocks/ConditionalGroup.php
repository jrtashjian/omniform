<?php
/**
 * Server-side renderer for the conditional group block.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Traits\CallbackSupport;

/**
 * Conditionally renders inner blocks based on a callback result.
 *
 * Without a recognized callback, content always renders. With a callback, the
 * group shows content when emptiness of the callback result matches the
 * reverseCondition attribute.
 */
class ConditionalGroup extends BaseBlock {
	use CallbackSupport;

	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render(): string {
		if ( ! $this->has_callback( $this->callback() ) ) {
			return $this->content;
		}

		return $this->condition_is_met() ? $this->content : '';
	}

	/**
	 * Callback expression from block attributes.
	 *
	 * @return string
	 */
	private function callback(): string {
		return (string) ( $this->get_block_attribute( 'callback' ) ?? '' );
	}

	/**
	 * Whether the callback result satisfies the reverseCondition setting.
	 *
	 * Content is shown when empty(callback result) strictly equals reverseCondition.
	 *
	 * @return bool
	 */
	private function condition_is_met(): bool {
		$callback_result_is_empty = empty( $this->process_callbacks( $this->callback() ) );

		return $callback_result_is_empty === $this->get_block_attribute( 'reverseCondition' );
	}
}
