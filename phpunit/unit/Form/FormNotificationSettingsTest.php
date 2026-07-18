<?php
/**
 * Tests FormNotificationSettings.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\FormNotificationSettings;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests FormNotificationSettings.
 */
class FormNotificationSettingsTest extends BaseTestCase {
	/**
	 * Stores recipients and subject.
	 */
	public function testAccessors() {
		$settings = new FormNotificationSettings(
			array( 'a@example.com', 'b@example.com' ),
			'New response'
		);

		$this->assertSame( array( 'a@example.com', 'b@example.com' ), $settings->recipients() );
		$this->assertSame( 'New response', $settings->subject() );
		$this->assertTrue( $settings->has_recipients() );
	}

	/**
	 * Empty recipient list is allowed (caller skips send).
	 */
	public function testEmptyRecipients() {
		$settings = new FormNotificationSettings( array(), 'Subject' );

		$this->assertFalse( $settings->has_recipients() );
	}

	/**
	 * Empty subject is rejected.
	 */
	public function testRejectsEmptySubject() {
		$this->expectException( \InvalidArgumentException::class );

		new FormNotificationSettings( array( 'a@example.com' ), '' );
	}
}
