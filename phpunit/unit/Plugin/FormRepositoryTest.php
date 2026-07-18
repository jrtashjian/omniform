<?php
/**
 * Tests the FormRepository class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Exceptions\FormNotFoundException;
use OmniForm\Exceptions\InvalidFormIdException;
use OmniForm\Form\Form;
use OmniForm\Plugin\FormRepository;
use OmniForm\Tests\Unit\BaseTestCase;
use Mockery;
use WP_Mock;

/**
 * Tests the FormRepository class.
 */
class FormRepositoryTest extends BaseTestCase {
	/**
	 * @var FormRepository
	 */
	private FormRepository $repository;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->repository = new FormRepository();
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Loads a form from a valid omniform post.
	 */
	public function testGetReturnsFormFromPost() {
		$form_id = 123;
		$post    = Mockery::mock( 'WP_Post' );

		$post->ID           = $form_id;
		$post->post_type    = 'omniform';
		$post->post_title   = 'Contact';
		$post->post_content = '<!-- wp:paragraph /-->';
		$post->post_status  = 'publish';

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $form_id )
			->andReturn( $post );

		WP_Mock::userFunction( 'get_post_meta' )
			->with( $form_id, 'notify_email', true )
			->andReturn( array( 'team@example.com' ) );
		WP_Mock::userFunction( 'get_post_meta' )
			->with( $form_id, 'notify_email_subject', true )
			->andReturn( 'Custom subject' );
		WP_Mock::userFunction( '__' )->andReturnUsing( static fn( $v ) => $v );

		$form = $this->repository->get( $form_id );

		$this->assertInstanceOf( Form::class, $form );
		$this->assertSame( 123, $form->id() );
		$this->assertSame( 'Contact', $form->title() );
		$this->assertSame( '<!-- wp:paragraph /-->', $form->content() );
		$this->assertSame( 'publish', $form->status() );
		$this->assertTrue( $form->is_persisted() );
		$this->assertTrue( $form->is_published() );
		$this->assertNotNull( $form->notifications() );
		$this->assertSame( array( 'team@example.com' ), $form->notifications()->recipients() );
		$this->assertSame( 'Custom subject', $form->notifications()->subject() );
	}

	/**
	 * Defaults recipients to admin_email and builds a default subject.
	 */
	public function testGetDefaultsNotificationSettings() {
		$form_id = 50;
		$post    = Mockery::mock( 'WP_Post' );

		$post->ID           = $form_id;
		$post->post_type    = 'omniform';
		$post->post_title   = 'Signup';
		$post->post_content = '<!-- form -->';
		$post->post_status  = 'publish';

		WP_Mock::userFunction( 'get_post' )->once()->with( $form_id )->andReturn( $post );
		WP_Mock::userFunction( 'get_post_meta' )
			->with( $form_id, 'notify_email', true )
			->andReturn( '' );
		WP_Mock::userFunction( 'get_post_meta' )
			->with( $form_id, 'notify_email_subject', true )
			->andReturn( '' );
		WP_Mock::userFunction( 'get_option' )
			->with( 'admin_email' )
			->andReturn( 'admin@example.com' );
		WP_Mock::userFunction( 'get_option' )
			->with( 'blogname' )
			->andReturn( 'My Site' );
		WP_Mock::userFunction( '__' )->andReturnUsing(
			static fn( $text ) => $text
		);

		$form = $this->repository->get( $form_id );

		$this->assertSame( array( 'admin@example.com' ), $form->notifications()->recipients() );
		$this->assertSame( 'New Response: My Site - Signup', $form->notifications()->subject() );
	}

	/**
	 * Rejects non-positive form IDs.
	 */
	public function testGetRejectsInvalidId() {
		$this->expectException( InvalidFormIdException::class );

		$this->repository->get( 0 );
	}

	/**
	 * Throws when the post does not exist.
	 */
	public function testGetThrowsWhenPostMissing() {
		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 123 )
			->andReturn( null );

		$this->expectException( FormNotFoundException::class );

		$this->repository->get( 123 );
	}

	/**
	 * Throws when the post is not an omniform.
	 */
	public function testGetThrowsWhenWrongPostType() {
		$post            = Mockery::mock( 'WP_Post' );
		$post->ID        = 123;
		$post->post_type = 'post';

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 123 )
			->andReturn( $post );

		$this->expectException( FormNotFoundException::class );

		$this->repository->get( 123 );
	}
}
