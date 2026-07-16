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

		$form = $this->repository->get( $form_id );

		$this->assertInstanceOf( Form::class, $form );
		$this->assertSame( 123, $form->id() );
		$this->assertSame( 'Contact', $form->title() );
		$this->assertSame( '<!-- wp:paragraph /-->', $form->content() );
		$this->assertSame( 'publish', $form->status() );
		$this->assertTrue( $form->is_persisted() );
		$this->assertTrue( $form->is_published() );
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
