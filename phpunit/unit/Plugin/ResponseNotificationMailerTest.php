<?php
/**
 * Tests ResponseNotificationMailer.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\Form;
use OmniForm\Form\FormNotificationSettings;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;
use OmniForm\Plugin\ResponseNotificationMailer;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests ResponseNotificationMailer.
 */
class ResponseNotificationMailerTest extends BaseTestCase {
	/**
	 * Sets up WP mocks.
	 */
	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction( '__' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'get_bloginfo' )->andReturn( 'https://example.com' );
		WP_Mock::userFunction( 'current_time' )->andReturn( '2026-07-17 12:00:00' );
	}

	/**
	 * Sends mail with presented body when recipients exist.
	 */
	public function testSendsMail() {
		$response = new Response(
			new FormSchema(
				array(
					new Field( FieldPath::from_segments( array( 'email' ) ), 'Email', 'email' ),
				)
			),
			new Submission( array( 'email' => 'jane@example.com' ) )
		);

		$form = new Form(
			content: '<!-- form -->',
			title: 'Contact',
			status: 'publish',
			id: 10,
			notifications: new FormNotificationSettings(
				array( 'admin@example.com' ),
				'New Response: Site - Contact'
			)
		);

		WP_Mock::userFunction( 'wp_mail' )
			->once()
			->with(
				array( 'admin@example.com' ),
				'New Response: Site - Contact',
				\Mockery::on(
					static function ( string $body ): bool {
						return str_contains( $body, 'Email: jane@example.com' )
							&& str_contains( $body, 'IP Address: 1.2.3.4' )
							&& str_contains( $body, 'Form URL: https://example.com/form' );
					}
				)
			)
			->andReturn( true );

		( new ResponseNotificationMailer() )->send(
			$response,
			$form,
			array(
				'user_ip' => '1.2.3.4',
				'referer' => 'https://example.com/form',
				'time'    => '2026-07-17 12:00:00',
			)
		);

		$this->assertTrue( true );
	}

	/**
	 * Skips send when there are no recipients.
	 */
	public function testSkipsWhenNoRecipients() {
		$response = new Response( new FormSchema(), new Submission() );
		$form     = new Form(
			content: 'x',
			notifications: new FormNotificationSettings( array(), 'Subject' )
		);

		WP_Mock::userFunction( 'wp_mail' )->never();

		( new ResponseNotificationMailer() )->send( $response, $form );

		$this->assertTrue( true );
	}

	/**
	 * Skips send when notifications were not loaded.
	 */
	public function testSkipsWhenNotificationsMissing() {
		$response = new Response( new FormSchema(), new Submission() );
		$form     = Form::from_content( 'x' );

		WP_Mock::userFunction( 'wp_mail' )->never();

		( new ResponseNotificationMailer() )->send( $response, $form );

		$this->assertTrue( true );
	}
}
