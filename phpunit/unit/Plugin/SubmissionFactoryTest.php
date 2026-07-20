<?php
/**
 * Tests the SubmissionFactory.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Plugin\SubmissionFactory;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the SubmissionFactory.
 */
class SubmissionFactoryTest extends BaseTestCase {
	/**
	 * @var SubmissionFactory
	 */
	private SubmissionFactory $factory;

	/**
	 * Sets up the test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->factory = new SubmissionFactory();

		WP_Mock::userFunction( 'sanitize_text_field' )->andReturnUsing(
			static function ( $value ) {
				if ( ! is_string( $value ) ) {
					return $value;
				}

				// Mirror core: collapse newlines and whitespace to single spaces, then trim.
				return trim( preg_replace( '/\s+/', ' ', $value ) );
			}
		);
		WP_Mock::userFunction( 'sanitize_textarea_field' )->andReturnUsing(
			static fn( $value ) => is_string( $value ) ? trim( $value ) : (string) $value
		);
		WP_Mock::userFunction( 'sanitize_email' )->andReturnUsing(
			static function ( $value ) {
				$value = is_string( $value ) ? trim( $value ) : (string) $value;

				// Mirror core: strip characters not allowed in an email address.
				return preg_replace( '/[^a-zA-Z0-9.@_+-]/', '', $value );
			}
		);
		WP_Mock::userFunction( 'sanitize_url' )->andReturnUsing(
			static function ( $value ) {
				$value = is_string( $value ) ? trim( $value ) : (string) $value;

				// Mirror core: block dangerous protocols like javascript: and data:.
				if ( preg_match( '#^\s*(javascript|data):#i', $value ) ) {
					return '';
				}

				return $value;
			}
		);
		WP_Mock::userFunction( 'sanitize_file_name' )->andReturnUsing(
			static fn( $value ) => preg_replace( '/[^A-Za-z0-9._-]/', '', (string) $value )
		);
		WP_Mock::userFunction( 'apply_filters' )->andReturnUsing(
			static fn( $hook, $value ) => $value
		);
	}

	/**
	 * Strips noise keys and sanitizes values.
	 */
	public function testFromRequestFiltersAndSanitizes() {
		$submission = $this->factory->from_request(
			array(
				'email'             => '  a@b.c  ',
				'message'           => "hello\nworld",
				'_wpnonce'          => 'secret',
				'rest_route'        => '/omniform/v1/forms/1/responses',
				'_omniform_user_ip' => '1.2.3.4',
			)
		);

		$this->assertSame( 'a@b.c', $submission->value( FieldPath::from_segments( array( 'email' ) ) ) );
		$this->assertSame( "hello\nworld", $submission->value( FieldPath::from_segments( array( 'message' ) ) ) );
		$this->assertArrayNotHasKey( '_wpnonce', $submission->values() );
		$this->assertArrayNotHasKey( 'rest_route', $submission->values() );
		$this->assertArrayNotHasKey( '_omniform_user_ip', $submission->values() );
	}

	/**
	 * Nested arrays are preserved and sanitized.
	 */
	public function testFromRequestSanitizesNestedValues() {
		$submission = $this->factory->from_request(
			array(
				'contact' => array(
					'first' => ' Jane ',
					'last'  => ' Doe ',
				),
			)
		);

		$this->assertSame(
			'Jane',
			$submission->value( FieldPath::from_segments( array( 'contact', 'first' ) ) )
		);
		$this->assertSame(
			'Doe',
			$submission->value( FieldPath::from_segments( array( 'contact', 'last' ) ) )
		);
	}

	/**
	 * Files become plain metadata without tmp_name.
	 */
	public function testFromRequestMergesFileMetadata() {
		if ( ! defined( 'UPLOAD_ERR_OK' ) ) {
			define( 'UPLOAD_ERR_OK', 0 );
		}

		$submission = $this->factory->from_request(
			array( 'email' => 'a@b.c' ),
			array(
				'resume' => array(
					'name'     => 'my cv.pdf',
					'type'     => 'application/pdf',
					'tmp_name' => '/tmp/phpsecret',
					'error'    => UPLOAD_ERR_OK,
					'size'     => 1024,
				),
			)
		);

		$file = $submission->value( FieldPath::from_segments( array( 'resume' ) ) );

		$this->assertSame( 'mycv.pdf', $file['name'] );
		$this->assertSame( 'application/pdf', $file['type'] );
		$this->assertSame( 1024, $file['size'] );
		$this->assertSame( UPLOAD_ERR_OK, $file['error'] );
		$this->assertArrayNotHasKey( 'tmp_name', $file );
	}

	/**
	 * Builds a schema covering one field per supported type.
	 *
	 * @return FormSchema
	 */
	private function typed_schema(): FormSchema {
		return new FormSchema(
			array(
				new Field( FieldPath::from_segments( array( 'email' ) ), 'Email', 'email' ),
				new Field( FieldPath::from_segments( array( 'website' ) ), 'Website', 'url' ),
				new Field( FieldPath::from_segments( array( 'age' ) ), 'Age', 'number' ),
				new Field( FieldPath::from_segments( array( 'message' ) ), 'Message', 'textarea' ),
				new Field( FieldPath::from_segments( array( 'name' ) ), 'Name', 'text' ),
			)
		);
	}

	/**
	 * Each field type dispatches to the matching sanitizer.
	 */
	public function testFromRequestDispatchesByFieldType() {
		$submission = $this->factory->from_request(
			array(
				'email'   => '  a@b.c  ',
				'website' => 'javascript:alert(1)',
				'age'     => '42',
				'message' => "hello\nworld",
				'name'    => '  Jane  ',
			),
			array(),
			$this->typed_schema()
		);

		$this->assertSame( 'a@b.c', $submission->value( FieldPath::from_segments( array( 'email' ) ) ) );
		// javascript: URLs are stripped by sanitize_url to block stored XSS.
		$this->assertSame( '', $submission->value( FieldPath::from_segments( array( 'website' ) ) ) );
		$this->assertSame( 42, $submission->value( FieldPath::from_segments( array( 'age' ) ) ) );
		// textarea preserves newlines; text gets single-line treatment.
		$this->assertSame( "hello\nworld", $submission->value( FieldPath::from_segments( array( 'message' ) ) ) );
		$this->assertSame( 'Jane', $submission->value( FieldPath::from_segments( array( 'name' ) ) ) );
	}

	/**
	 * Non-numeric number input passes through for the validator to reject.
	 */
	public function testFromRequestNumberFieldPassesNonNumericToValidator() {
		$schema = new FormSchema(
			array(
				new Field( FieldPath::from_segments( array( 'age' ) ), 'Age', 'number' ),
			)
		);

		$submission = $this->factory->from_request(
			array( 'age' => 'not-a-number' ),
			array(),
			$schema
		);

		$this->assertSame( 'not-a-number', $submission->value( FieldPath::from_segments( array( 'age' ) ) ) );
	}

	/**
	 * Numeric values cast to int or float depending on the source string.
	 */
	public function testFromRequestNumberFieldCastsIntAndFloat() {
		$schema = new FormSchema(
			array(
				new Field( FieldPath::from_segments( array( 'age' ) ), 'Age', 'number' ),
				new Field( FieldPath::from_segments( array( 'rating' ) ), 'Rating', 'range' ),
				new Field( FieldPath::from_segments( array( 'price' ) ), 'Price', 'number' ),
			)
		);

		$submission = $this->factory->from_request(
			array(
				'age'    => '42',
				'rating' => '7',
				'price'  => '3.14',
			),
			array(),
			$schema
		);

		$this->assertSame( 42, $submission->value( FieldPath::from_segments( array( 'age' ) ) ) );
		$this->assertSame( 7, $submission->value( FieldPath::from_segments( array( 'rating' ) ) ) );
		$this->assertSame( 3.14, $submission->value( FieldPath::from_segments( array( 'price' ) ) ) );
	}

	/**
	 * Without a schema the legacy textarea default stays byte-identical.
	 */
	public function testFromRequestFallsBackWhenSchemaNull() {
		$submission = $this->factory->from_request(
			array( 'message' => "hello\nworld" )
		);

		$this->assertSame( "hello\nworld", $submission->value( FieldPath::from_segments( array( 'message' ) ) ) );
	}

	/**
	 * Fields present in the request but missing from the schema use single-line text.
	 */
	public function testFromRequestFallsBackWhenFieldNotInSchema() {
		$schema = new FormSchema(
			array(
				new Field( FieldPath::from_segments( array( 'email' ) ), 'Email', 'email' ),
			)
		);

		$submission = $this->factory->from_request(
			array( 'unexpected' => "line one\nline two" ),
			array(),
			$schema
		);

		// sanitize_text_field collapses newlines for unknown fields.
		$this->assertSame( 'line one line two', $submission->value( FieldPath::from_segments( array( 'unexpected' ) ) ) );
	}

	/**
	 * Nested request paths resolve to the matching dotted schema key.
	 */
	public function testFromRequestDispatchesNestedFieldByType() {
		$schema = new FormSchema(
			array(
				new Field( FieldPath::from_segments( array( 'contact', 'email' ) ), 'Email', 'email' ),
			)
		);

		$submission = $this->factory->from_request(
			array( 'contact' => array( 'email' => '  a@b.c  ' ) ),
			array(),
			$schema
		);

		$this->assertSame( 'a@b.c', $submission->value( FieldPath::from_segments( array( 'contact', 'email' ) ) ) );
	}

	/**
	 * Choice-group arrays sanitize each leaf without losing the array shape.
	 */
	public function testFromRequestSanitizesChoiceGroupArrayLeaves() {
		$submission = $this->factory->from_request(
			array( 'tags' => array( 'a', ' b ' ) )
		);

		$values = $submission->value( FieldPath::from_segments( array( 'tags' ) ) );

		$this->assertSame( array( 'a', 'b' ), $values );
	}
}
