<?php
/**
 * Tests the Form definition object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\Form;
use OmniForm\Form\FormNotificationSettings;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the Form definition object.
 */
class FormTest extends BaseTestCase {
	/**
	 * Content-only forms have no identity.
	 */
	public function testFromContentCreatesUnpersistedForm() {
		$form = Form::from_content( '<!-- wp:paragraph /-->' );

		$this->assertSame( '<!-- wp:paragraph /-->', $form->content() );
		$this->assertSame( '', $form->title() );
		$this->assertNull( $form->id() );
		$this->assertFalse( $form->is_persisted() );
		$this->assertTrue( $form->is_published() );
		$this->assertSame( 'publish', $form->status() );
		$this->assertNull( $form->notifications() );
	}

	/**
	 * Full construction captures identity and status.
	 */
	public function testFullConstruction() {
		$notifications = new FormNotificationSettings(
			array( 'a@example.com' ),
			'Subject'
		);

		$form = new Form(
			content: '<!-- form -->',
			title: 'Contact',
			status: 'draft',
			id: 42,
			notifications: $notifications,
		);

		$this->assertSame( 42, $form->id() );
		$this->assertTrue( $form->is_persisted() );
		$this->assertSame( 'Contact', $form->title() );
		$this->assertSame( '<!-- form -->', $form->content() );
		$this->assertSame( 'draft', $form->status() );
		$this->assertFalse( $form->is_published() );
		$this->assertSame( $notifications, $form->notifications() );
	}

	/**
	 * Non-positive ids are rejected.
	 */
	public function testRejectsNonPositiveId() {
		$this->expectException( \InvalidArgumentException::class );

		new Form( content: 'x', id: 0 );
	}
}
