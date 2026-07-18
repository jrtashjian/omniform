<?php
/**
 * Tests ResponseViewData.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;
use OmniForm\Plugin\ResponseRepository;
use OmniForm\Plugin\ResponseViewData;
use OmniForm\Tests\Unit\BaseTestCase;
use Mockery;
use WP_Mock;

/**
 * Tests ResponseViewData.
 */
class ResponseViewDataTest extends BaseTestCase {
	/**
	 * @var ResponseViewData
	 */
	private ResponseViewData $view;

	/**
	 * Sets up the test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->view = new ResponseViewData();

		WP_Mock::userFunction( '__' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'is_email' )->andReturnUsing(
			static fn( $v ) => is_string( $v ) && false !== filter_var( $v, FILTER_VALIDATE_EMAIL )
		);
		WP_Mock::userFunction( 'esc_html' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'wp_json_encode' )->andReturnUsing(
			static fn( $data ) => json_encode( $data ) // phpcs:ignore WordPress.WP.AlternativeFunctions
		);
	}

	/**
	 * Tears down the test environment.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Domain responses expose ordered field rows.
	 */
	public function testFieldsFromDomainResponse() {
		$response = new Response(
			new FormSchema(
				array(
					new Field( FieldPath::from_segments( array( 'name' ) ), 'Name', 'text' ),
					new Field( FieldPath::from_segments( array( 'email' ) ), 'Email', 'email' ),
				)
			),
			new Submission(
				array(
					'name'  => 'Jane',
					'email' => 'jane@example.com',
				)
			)
		);

		$this->assertSame(
			array(
				array(
					'name'  => 'name',
					'label' => 'Name',
					'type'  => 'text',
					'value' => 'Jane',
				),
				array(
					'name'  => 'email',
					'label' => 'Email',
					'type'  => 'email',
					'value' => 'jane@example.com',
				),
			),
			$this->view->fields( $response )
		);
	}

	/**
	 * Prefers email-type fields for sender email.
	 */
	public function testSenderEmailPrefersEmailTypeField() {
		$response = new Response(
			new FormSchema(
				array(
					new Field( FieldPath::from_segments( array( 'note' ) ), 'Note', 'text' ),
					new Field( FieldPath::from_segments( array( 'work' ) ), 'Work', 'email' ),
				)
			),
			new Submission(
				array(
					'note' => 'not-an-email@example.com',
					'work' => 'work@example.com',
				)
			)
		);

		$this->assertSame( 'work@example.com', $this->view->sender_email( $response ) );
	}

	/**
	 * Finds nested emails in domain submissions.
	 */
	public function testSenderEmailFindsNestedDomainValues() {
		$response = new Response(
			new FormSchema(
				array(
					new Field(
						FieldPath::from_segments( array( 'contact', 'email' ) ),
						'Email',
						'email'
					),
				)
			),
			new Submission(
				array(
					'contact' => array( 'email' => 'nested@example.com' ),
				)
			)
		);

		$this->assertSame( 'nested@example.com', $this->view->sender_email( $response ) );
	}

	/**
	 * Legacy payloads dual-read into the same view shape.
	 */
	public function testLegacyPayloadViaRepository() {
		$payload = array(
			'response' => array(
				'name'  => 'Jane',
				'email' => 'jane@example.com',
			),
			'fields'   => array(
				'name'  => 'Name',
				'email' => 'Email',
			),
			'groups'   => array(),
		);

		$post               = Mockery::mock( 'WP_Post' );
		$post->ID           = 42;
		$post->post_type    = 'omniform_response';
		$post->post_content = wp_json_encode( $payload );

		$response = ( new ResponseRepository() )->from_post( $post );

		$this->assertSame( 'jane@example.com', $this->view->sender_email( $response ) );
		$this->assertSame(
			array(
				array(
					'name'  => 'name',
					'label' => 'Name',
					'type'  => 'text',
					'value' => 'Jane',
				),
				array(
					'name'  => 'email',
					'label' => 'Email',
					'type'  => 'text',
					'value' => 'jane@example.com',
				),
			),
			$this->view->fields( $response )
		);
	}

	/**
	 * Nested legacy emails are found for the list UI.
	 */
	public function testLegacyNestedSenderEmail() {
		$payload = array(
			'response' => array(
				'contact' => array(
					'email' => 'nested@example.com',
				),
			),
			'fields'   => array(
				'contact.email' => 'Email',
			),
			'groups'   => array(),
		);

		$post               = Mockery::mock( 'WP_Post' );
		$post->ID           = 43;
		$post->post_type    = 'omniform_response';
		$post->post_content = wp_json_encode( $payload );

		$response = ( new ResponseRepository() )->from_post( $post );

		$this->assertSame( 'nested@example.com', $this->view->sender_email( $response ) );
	}
}
