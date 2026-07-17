<?php
/**
 * Tests RespectSubmissionValidator.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Submission;
use OmniForm\Plugin\RespectSubmissionValidator;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests RespectSubmissionValidator.
 */
class RespectSubmissionValidatorTest extends BaseTestCase {
	/**
	 * @var RespectSubmissionValidator
	 */
	private RespectSubmissionValidator $validator;

	/**
	 * Sets up the test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->validator = new RespectSubmissionValidator();
	}

	/**
	 * Required field missing fails.
	 */
	public function testRequiredFieldFailsWhenMissing() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'email' ) ),
					'Email',
					'email',
					array( 'required' )
				),
			)
		);

		$result = $this->validator->validate( $schema, new Submission( array() ) );

		$this->assertTrue( $result->is_invalid() );
		$this->assertNotEmpty( $result->messages() );
	}

	/**
	 * Required field present passes.
	 */
	public function testRequiredFieldPassesWhenPresent() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'email' ) ),
					'Email',
					'text',
					array( 'required' )
				),
			)
		);

		$result = $this->validator->validate(
			$schema,
			new Submission( array( 'email' => 'hello@example.com' ) )
		);

		$this->assertTrue( $result->is_valid() );
		$this->assertSame( array(), $result->messages() );
	}

	/**
	 * Email type validates format when present.
	 */
	public function testEmailTypeRejectsInvalidValue() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'email' ) ),
					'Email',
					'email'
				),
			)
		);

		$result = $this->validator->validate(
			$schema,
			new Submission( array( 'email' => 'not-an-email' ) )
		);

		$this->assertTrue( $result->is_invalid() );
	}

	/**
	 * Optional email may be omitted.
	 */
	public function testOptionalEmailMayBeOmitted() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'email' ) ),
					'Email',
					'email'
				),
			)
		);

		$result = $this->validator->validate( $schema, new Submission( array() ) );

		$this->assertTrue( $result->is_valid() );
	}

	/**
	 * Nested paths are validated.
	 */
	public function testValidatesNestedPaths() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'contact', 'email' ) ),
					'Email',
					'email',
					array( 'required' )
				),
			)
		);

		$fail = $this->validator->validate( $schema, new Submission( array() ) );
		$pass = $this->validator->validate(
			$schema,
			new Submission(
				array(
					'contact' => array( 'email' => 'a@b.c' ),
				)
			)
		);

		$this->assertTrue( $fail->is_invalid() );
		$this->assertTrue( $pass->is_valid() );
	}

	/**
	 * Explicit rule strings are applied.
	 */
	public function testAppliesExplicitEmailRule() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'work' ) ),
					'Work email',
					'text',
					array( 'required', 'email' )
				),
			)
		);

		$result = $this->validator->validate(
			$schema,
			new Submission( array( 'work' => 'nope' ) )
		);

		$this->assertTrue( $result->is_invalid() );
	}

	/**
	 * Min rule is applied.
	 */
	public function testAppliesMinRule() {
		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'age' ) ),
					'Age',
					'number',
					array( 'required', 'min:18' )
				),
			)
		);

		$fail = $this->validator->validate( $schema, new Submission( array( 'age' => 10 ) ) );
		$pass = $this->validator->validate( $schema, new Submission( array( 'age' => 21 ) ) );

		$this->assertTrue( $fail->is_invalid() );
		$this->assertTrue( $pass->is_valid() );
	}
}
