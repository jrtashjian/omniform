<?php
/**
 * Tests the SubmissionFactory.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Form\FieldPath;
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
			static fn( $value ) => is_string( $value ) ? trim( $value ) : $value
		);
		WP_Mock::userFunction( 'sanitize_textarea_field' )->andReturnUsing(
			static fn( $value ) => is_string( $value ) ? trim( $value ) : (string) $value
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
				'email'            => '  a@b.c  ',
				'message'          => "hello\nworld",
				'_wpnonce'         => 'secret',
				'rest_route'       => '/omniform/v1/forms/1/responses',
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
}
