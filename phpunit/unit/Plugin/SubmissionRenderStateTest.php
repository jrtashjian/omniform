<?php
/**
 * Tests SubmissionRenderState.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\SubmissionRenderState;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests SubmissionRenderState.
 */
class SubmissionRenderStateTest extends BaseTestCase {
	/**
	 * Untouched state is neither failed nor succeeded.
	 */
	public function testInitialState() {
		$state = new SubmissionRenderState();

		$this->assertFalse( $state->validation_failed() );
		$this->assertFalse( $state->validation_succeeded() );
		$this->assertSame( array(), $state->messages() );
	}

	/**
	 * mark_succeeded sets success flags and clears messages.
	 */
	public function testMarkSucceeded() {
		$state = new SubmissionRenderState();
		$state->mark_failed( array( 'email' => 'Required' ) );
		$state->mark_succeeded();

		$this->assertTrue( $state->validation_succeeded() );
		$this->assertFalse( $state->validation_failed() );
		$this->assertSame( array(), $state->messages() );
	}

	/**
	 * mark_failed stores messages and failure flag.
	 */
	public function testMarkFailed() {
		$state    = new SubmissionRenderState();
		$messages = array( 'email' => 'Email is required' );
		$state->mark_failed( $messages );

		$this->assertTrue( $state->validation_failed() );
		$this->assertFalse( $state->validation_succeeded() );
		$this->assertSame( $messages, $state->messages() );
	}
}
