<?php
/**
 * The ConditionalGroup block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Traits\CallbackSupport;

/**
 * The ConditionalGroup block class.
 */
class ConditionalGroup extends BaseBlock {
	use CallbackSupport;

	/**
	 * Renders the block on the server.
	 *
	 * @return string
	 */
	public function render() {
		if ( ! $this->has_callback( $this->get_block_attribute( 'callback' ) ?? '' ) ) {
			return $this->content;
		}

		return empty( $this->process_callbacks( $this->get_block_attribute( 'callback' ) ) ) === $this->get_block_attribute( 'reverseCondition' )
			? $this->content : '';
	}
}
