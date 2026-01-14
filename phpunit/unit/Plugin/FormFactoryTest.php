<?php
/**
 * Tests the FormFactory class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\FormFactory;
use OmniForm\Plugin\Form;
use OmniForm\Dependencies\League\Container\Container;
use OmniForm\Exceptions\FormNotFoundException;
use OmniForm\Exceptions\InvalidFormIdException;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;

/**
 * Tests the FormFactory class.
 */
class FormFactoryTest extends BaseTestCase {
	/**
	 * The Container mock.
	 *
	 * @var \Mockery\MockInterface|Container
	 */
	private $container;

	/**
	 * The FormFactory instance.
	 *
	 * @var FormFactory
	 */
	private $form_factory;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->container = Mockery::mock( Container::class );

		$this->form_factory = new FormFactory( $this->container );
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Test create_with_content creates form with content.
	 */
	public function testCreateWithContent() {
		$form_content = 'test content';
		$form_mock    = Mockery::mock( Form::class );

		$this->container->shouldReceive( 'get' )
			->once()
			->with( Form::class )
			->andReturn( $form_mock );

		$form_mock->shouldReceive( 'set_content' )
			->once()
			->with( $form_content );

		$result = $this->form_factory->create_with_content( $form_content );

		$this->assertSame( $form_mock, $result );
	}

	/**
	 * Test create_with_id creates form with valid ID.
	 */
	public function testCreateWithIdValid() {
		$form_id          = 123;
		$form_mock        = Mockery::mock( Form::class );
		$_form            = Mockery::mock( 'WP_Post' );
		$_form->ID        = $form_id;
		$_form->post_type = 'omniform';

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $form_id )
			->andReturn( $_form );

		$this->container->shouldReceive( 'get' )
			->once()
			->with( Form::class )
			->andReturn( $form_mock );

		$form_mock->shouldReceive( 'set_post_data' )
			->once()
			->with( $_form );

		$result = $this->form_factory->create_with_id( $form_id );

		$this->assertSame( $form_mock, $result );
	}

	/**
	 * Test create_with_id throws InvalidFormIdException for invalid ID.
	 */
	public function testCreateWithIdInvalid() {
		$form_id = 'abc';

		$this->expectException( InvalidFormIdException::class );

		$this->form_factory->create_with_id( $form_id );
	}

	/**
	 * Test create_with_id throws FormNotFoundException for non-existent form.
	 */
	public function testCreateWithIdNotFound() {
		$form_id = 123;

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $form_id )
			->andReturn( null );

		$this->expectException( FormNotFoundException::class );

		$this->form_factory->create_with_id( $form_id );
	}

	/**
	 * Test create_with_id throws FormNotFoundException for wrong post type.
	 */
	public function testCreateWithIdWrongPostType() {
		$form_id          = 123;
		$_form            = Mockery::mock( 'WP_Post' );
		$_form->ID        = $form_id;
		$_form->post_type = 'post';

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $form_id )
			->andReturn( $_form );

		$this->expectException( FormNotFoundException::class );

		$this->form_factory->create_with_id( $form_id );
	}
}
