<?php
/**
 * Tests FormRenderContext.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\FormRenderContext;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests FormRenderContext.
 */
class FormRenderContextTest extends BaseTestCase {
	/**
	 * Default required label is asterisk.
	 */
	public function testDefaultRequiredLabel() {
		$context = new FormRenderContext();

		$this->assertSame( '*', $context->required_label() );
	}

	/**
	 * set_required_label stores a non-empty value.
	 */
	public function testSetRequiredLabel() {
		$context = new FormRenderContext();
		$context->set_required_label( '(required)' );

		$this->assertSame( '(required)', $context->required_label() );
	}

	/**
	 * Empty string falls back to asterisk.
	 */
	public function testSetRequiredLabelEmptyFallsBack() {
		$context = new FormRenderContext();
		$context->set_required_label( '(required)' );
		$context->set_required_label( '' );

		$this->assertSame( '*', $context->required_label() );
	}
}
