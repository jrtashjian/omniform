<?php
/**
 * Tests HtmlResponsePresenter.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;
use OmniForm\Plugin\HtmlResponsePresenter;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests HtmlResponsePresenter.
 */
class HtmlResponsePresenterTest extends BaseTestCase {
	/**
	 * Sets up escaping mocks.
	 */
	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction( 'esc_html' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'wp_kses_post' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( '__' )->andReturnUsing( static fn( $v ) => $v );
	}

	/**
	 * Renders labels and values as HTML lines.
	 */
	public function testPresentsFieldsAsHtml() {
		$response = $this->response(
			array(
				new Field( FieldPath::from_segments( array( 'name' ) ), 'Name', 'text' ),
				new Field( FieldPath::from_segments( array( 'email' ) ), 'Email', 'email' ),
			),
			array(
				'name'  => 'Jane',
				'email' => 'jane@example.com',
			)
		);

		$html = ( new HtmlResponsePresenter() )->present( $response );

		$this->assertStringContainsString( '<strong>Name:</strong> Jane', $html );
		$this->assertStringContainsString( '<strong>Email:</strong> jane@example.com', $html );
		$this->assertStringContainsString( '<br />', $html );
	}

	/**
	 * Lone checkbox shows Checked when value matches label.
	 */
	public function testFormatsLoneCheckbox() {
		$response = $this->response(
			array(
				new Field( FieldPath::from_segments( array( 'agree' ) ), 'I agree', 'checkbox' ),
			),
			array( 'agree' => 'I agree' )
		);

		$html = ( new HtmlResponsePresenter() )->present( $response );

		$this->assertStringContainsString( '<strong>I agree:</strong> Checked', $html );
	}

	/**
	 * File fields show the file name.
	 */
	public function testFormatsFileMeta() {
		$response = $this->response(
			array(
				new Field( FieldPath::from_segments( array( 'resume' ) ), 'Resume', 'file' ),
			),
			array(
				'resume' => array(
					'name'  => 'cv.pdf',
					'type'  => 'application/pdf',
					'size'  => 12,
					'error' => 0,
				),
			)
		);

		$html = ( new HtmlResponsePresenter() )->present( $response );

		$this->assertStringContainsString( '<strong>Resume:</strong> cv.pdf', $html );
	}

	/**
	 * Nested paths resolve values.
	 */
	public function testPresentsNestedValues() {
		$response = $this->response(
			array(
				new Field(
					FieldPath::from_segments( array( 'contact', 'email' ) ),
					'Email',
					'email'
				),
			),
			array(
				'contact' => array( 'email' => 'a@b.c' ),
			)
		);

		$html = ( new HtmlResponsePresenter() )->present( $response );

		$this->assertStringContainsString( '<strong>Email:</strong> a@b.c', $html );
	}

	/**
	 * @param list<Field>          $fields Field list.
	 * @param array<string, mixed> $values Submission values.
	 */
	private function response( array $fields, array $values ): Response {
		return new Response(
			new FormSchema( $fields ),
			new Submission( $values )
		);
	}
}
