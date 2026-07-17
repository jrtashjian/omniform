<?php
/**
 * Tests TextResponsePresenter.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;
use OmniForm\Form\TextResponsePresenter;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests TextResponsePresenter.
 */
class TextResponsePresenterTest extends BaseTestCase {
	/**
	 * Sets up translation mock.
	 */
	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction( '__' )->andReturnUsing( static fn( $v ) => $v );
	}

	/**
	 * Renders plain label: value lines.
	 */
	public function testPresentsFieldsAsText() {
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

		$text = ( new TextResponsePresenter() )->present( $response );

		$this->assertSame(
			"Name: Jane\nEmail: jane@example.com",
			$text
		);
	}

	/**
	 * Appends footer lines after a separator.
	 */
	public function testAppendsFooterLines() {
		$response = new Response(
			new FormSchema(
				array(
					new Field( FieldPath::from_segments( array( 'name' ) ), 'Name', 'text' ),
				)
			),
			new Submission( array( 'name' => 'Jane' ) )
		);

		$text = ( new TextResponsePresenter() )->present(
			$response,
			array(
				'IP Address: 1.2.3.4',
				'Form URL: https://example.com',
			)
		);

		$this->assertStringContainsString( "Name: Jane\n\n---\nIP Address: 1.2.3.4\nForm URL: https://example.com", $text );
	}

	/**
	 * Multi-value fields join with commas.
	 */
	public function testJoinsListValues() {
		$response = new Response(
			new FormSchema(
				array(
					new Field( FieldPath::from_segments( array( 'tags' ) ), 'Tags', 'select' ),
				)
			),
			new Submission( array( 'tags' => array( 'a', 'b' ) ) )
		);

		$text = ( new TextResponsePresenter() )->present( $response );

		$this->assertSame( 'Tags: a, b', $text );
	}
}
