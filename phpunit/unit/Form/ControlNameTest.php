<?php
/**
 * Tests ControlName composition.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\ControlName;
use OmniForm\Form\FieldName;
use OmniForm\Form\FieldPath;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests ControlName composition.
 */
class ControlNameTest extends BaseTestCase {
	/**
	 * Simple control with no fieldset prefix.
	 */
	public function testComposeRootControl() {
		$path = ControlName::compose(
			FieldPath::empty(),
			FieldName::of( 'email' ),
			'email',
		);

		$this->assertSame( 'email', $path->key() );
		$this->assertSame( 'email', $path->html_name() );
	}

	/**
	 * Nested control under a fieldset path.
	 */
	public function testComposeNestedControl() {
		$path = ControlName::compose(
			FieldPath::from_segments( array( 'contact' ) ),
			FieldName::of( 'email' ),
			'email',
		);

		$this->assertSame( 'contact.email', $path->key() );
		$this->assertSame( 'contact[email]', $path->html_name() );
	}

	/**
	 * Choice group radios share the fieldset path.
	 */
	public function testComposeChoiceGroupDropsControlName() {
		$path = ControlName::compose(
			FieldPath::from_segments( array( 'rating' ) ),
			FieldName::of( 'Good' ),
			'radio',
			true,
		);

		$this->assertSame( 'rating', $path->key() );
		$this->assertSame( 'rating', $path->html_name() );
	}

	/**
	 * Choice group without a prefix is invalid.
	 */
	public function testComposeChoiceGroupRequiresPrefix() {
		$this->expectException( \InvalidArgumentException::class );

		ControlName::compose(
			FieldPath::empty(),
			FieldName::of( 'Yes' ),
			'radio',
			true,
		);
	}

	/**
	 * Multi-value HTML name rules.
	 */
	public function testIsMultiple() {
		$this->assertTrue( ControlName::is_multiple( 'select', false, true ) );
		$this->assertTrue( ControlName::is_multiple( 'checkbox', true, false ) );
		$this->assertFalse( ControlName::is_multiple( 'radio', true, false ) );
		$this->assertFalse( ControlName::is_multiple( 'text', false, false ) );
	}
}
