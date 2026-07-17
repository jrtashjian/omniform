<?php
/**
 * Tests the Submission value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\FieldPath;
use OmniForm\Form\Submission;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the Submission value object.
 */
class SubmissionTest extends BaseTestCase {
	/**
	 * Reads nested values by path.
	 */
	public function testReadsNestedValuesByPath() {
		$submission = new Submission(
			array(
				'email'   => 'a@b.c',
				'contact' => array( 'message' => 'Hi' ),
				'resume'  => array(
					'name' => 'cv.pdf',
					'size' => 12,
					'type' => 'application/pdf',
				),
			)
		);

		$this->assertSame( 'a@b.c', $submission->value( FieldPath::from_segments( array( 'email' ) ) ) );
		$this->assertSame( 'Hi', $submission->value( FieldPath::from_segments( array( 'contact', 'message' ) ) ) );
		$this->assertSame( 'cv.pdf', $submission->value( FieldPath::from_segments( array( 'resume' ) ) )['name'] );
		$this->assertNull( $submission->value( FieldPath::from_segments( array( 'missing' ) ) ) );
		$this->assertTrue( $submission->has( FieldPath::from_segments( array( 'email' ) ) ) );
		$this->assertFalse( $submission->has( FieldPath::from_segments( array( 'missing' ) ) ) );
	}

	/**
	 * Empty string is a present value.
	 */
	public function testEmptyStringIsPresent() {
		$submission = new Submission( array( 'note' => '' ) );

		$this->assertTrue( $submission->has( FieldPath::from_segments( array( 'note' ) ) ) );
		$this->assertSame( '', $submission->value( FieldPath::from_segments( array( 'note' ) ) ) );
	}

	/**
	 * Rejects objects and other non-plain values.
	 */
	public function testRejectsNonPlainValues() {
		$this->expectException( \InvalidArgumentException::class );

		new Submission( array( 'bad' => new \stdClass() ) );
	}

	/**
	 * Round-trips through array serialization.
	 */
	public function testRoundTripsThroughArray() {
		$original = new Submission( array( 'a' => array( 'b' => 1 ) ) );
		$restored = Submission::from_array( $original->to_array() );

		$this->assertSame( $original->values(), $restored->values() );
	}
}
