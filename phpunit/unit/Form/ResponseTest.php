<?php
/**
 * Tests the domain Response value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\Field;
use OmniForm\Form\FieldGroup;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the domain Response value object.
 */
class ResponseTest extends BaseTestCase {
	/**
	 * Round-trips schema and submission through array form.
	 */
	public function testRoundTripsThroughArray() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'email' ) ),
					'Email',
					'email',
					array( 'required', 'email' )
				),
				new Field(
					FieldPath::from_segments( array( 'resume' ) ),
					'Resume',
					'file'
				),
			),
			array(
				new FieldGroup(
					FieldPath::from_segments( array( 'rating' ) ),
					'Rating',
					array( 'required' ),
					true
				),
			)
		);

		$submission = new Submission(
			array(
				'email'  => 'a@b.c',
				'resume' => array(
					'name' => 'cv.pdf',
					'size' => 10,
					'type' => 'application/pdf',
				),
			)
		);

		$response = new Response( $schema, $submission );
		$restored = Response::from_array( $response->to_array() );

		$this->assertSame( 1, $restored->version() );
		$this->assertSame( 'Email', $restored->schema()->field( 'email' )->label() );
		$this->assertTrue( $restored->schema()->field( 'email' )->has_rule( 'required' ) );
		$this->assertSame( 'file', $restored->schema()->field( 'resume' )->type() );
		$this->assertTrue( $restored->schema()->group( 'rating' )->is_choice_group() );
		$this->assertSame(
			'a@b.c',
			$restored->submission()->value( FieldPath::from_segments( array( 'email' ) ) )
		);
		$this->assertSame(
			'cv.pdf',
			$restored->submission()->value( FieldPath::from_segments( array( 'resume' ) ) )['name']
		);
	}

	/**
	 * Rejects non-positive versions.
	 */
	public function testRejectsInvalidVersion() {
		$this->expectException( \InvalidArgumentException::class );

		new Response( new FormSchema(), new Submission(), 0 );
	}
}
