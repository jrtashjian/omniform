<?php
/**
 * Tests the FieldPath value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\FieldName;
use OmniForm\Form\FieldPath;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the FieldPath value object.
 */
class FieldPathTest extends BaseTestCase {
	/**
	 * Multi-segment path keys and HTML names.
	 */
	public function testFromSegments() {
		$path = FieldPath::from_segments( array( 'contact', 'email' ) );

		$this->assertSame( 'contact.email', $path->key() );
		$this->assertSame( 'contact[email]', $path->html_name() );
		$this->assertSame( 'contact[email][]', $path->html_name( true ) );
		$this->assertSame( array( 'contact', 'email' ), $path->segments() );
		$this->assertFalse( $path->is_empty() );
	}

	/**
	 * Root path from a control name.
	 */
	public function testRoot() {
		$path = FieldPath::root( FieldName::of( 'email' ) );

		$this->assertSame( 'email', $path->key() );
		$this->assertSame( 'email', $path->html_name() );
	}

	/**
	 * Append extends the path.
	 */
	public function testAppend() {
		$base = FieldPath::from_segments( array( 'contact' ) );

		$this->assertSame( 'contact.email', $base->append( FieldName::of( 'email' ) )->key() );
		$this->assertSame( 'contact', $base->key() );
	}

	/**
	 * Empty path is allowed as a prefix only.
	 */
	public function testEmpty() {
		$path = FieldPath::empty();

		$this->assertTrue( $path->is_empty() );
		$this->assertSame( array(), $path->segments() );
	}

	/**
	 * Empty path cannot produce keys or HTML names.
	 */
	public function testEmptyRejectsKey() {
		$this->expectException( \InvalidArgumentException::class );

		FieldPath::empty()->key();
	}

	/**
	 * Rejects empty segment lists for from_segments.
	 */
	public function testRejectsEmptySegments() {
		$this->expectException( \InvalidArgumentException::class );

		FieldPath::from_segments( array() );
	}
}
