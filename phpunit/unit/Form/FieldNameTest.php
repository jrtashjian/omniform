<?php
/**
 * Tests the FieldName value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\FieldName;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the FieldName value object.
 */
class FieldNameTest extends BaseTestCase {
	/**
	 * Stores a single sanitized segment.
	 */
	public function testOfSanitizesValue() {
		$this->assertSame( 'Your-email', FieldName::of( 'Your email' )->value() );
		$this->assertSame( 'Attending', FieldName::of( 'Attending?' )->value() );
		$this->assertSame( 'first_name', FieldName::of( 'first_name' )->value() );
	}

	/**
	 * Prefers explicit name over label.
	 */
	public function testFromNameOrLabel() {
		$this->assertSame( 'email', FieldName::from_name_or_label( 'email', 'Email Address' )->value() );
		$this->assertSame( 'Email-Address', FieldName::from_name_or_label( null, 'Email Address' )->value() );
		$this->assertSame( 'Email-Address', FieldName::from_name_or_label( '', 'Email Address' )->value() );
	}

	/**
	 * Rejects empty resolution.
	 */
	public function testFromNameOrLabelRejectsEmpty() {
		$this->expectException( \InvalidArgumentException::class );

		FieldName::from_name_or_label( null, '' );
	}

	/**
	 * Sanitize is available without constructing.
	 */
	public function testSanitize() {
		$this->assertSame( 'Your-email', FieldName::sanitize( 'Your email' ) );
	}
}
