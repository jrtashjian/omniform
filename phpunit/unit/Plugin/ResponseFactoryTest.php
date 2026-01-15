<?php
/**
 * Tests the ResponseFactory class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\ResponseFactory;
use OmniForm\Plugin\Form;
use OmniForm\Plugin\Response;
use OmniForm\Dependencies\League\Container\Container;
use OmniForm\Exceptions\ResponseNotFoundException;
use OmniForm\Exceptions\InvalidResponseIdException;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;

/**
 * Tests the ResponseFactory class.
 */
class ResponseFactoryTest extends BaseTestCase {
	/**
	 * The Container mock.
	 *
	 * @var \Mockery\MockInterface|Container
	 */
	private $container;

	/**
	 * The ResponseFactory instance.
	 *
	 * @var ResponseFactory
	 */
	private $response_factory;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->container = Mockery::mock( Container::class );

		$this->response_factory = new ResponseFactory( $this->container );
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Test create_with_form creates response with form data.
	 */
	public function testCreateWithForm() {
		$form_mock      = Mockery::mock( Form::class );
		$response_mock  = Mockery::mock( Response::class );
		$request_params = array( 'field1' => 'value1' );
		$fields         = array( 'field1' => 'Field 1' );
		$groups         = array( 'group1' => 'Group 1' );
		$current_time   = '2023-01-01 12:00:00';

		$_SERVER['REMOTE_ADDR'] = '192.168.1.1';

		WP_Mock::userFunction( 'current_time' )
			->with( 'mysql' )
			->andReturn( $current_time );

		$this->container->shouldReceive( 'get' )
			->once()
			->with( Response::class )
			->andReturn( $response_mock );

		$form_mock->shouldReceive( 'get_request_params' )
			->once()
			->andReturn( $request_params );
		$form_mock->shouldReceive( 'get_fields' )
			->once()
			->andReturn( $fields );
		$form_mock->shouldReceive( 'get_groups' )
			->once()
			->andReturn( $groups );

		$response_mock->shouldReceive( 'set_request_params' )
			->once()
			->with(
				array_merge(
					$request_params,
					array( '_omniform_user_ip' => '192.168.1.1' )
				)
			);
		$response_mock->shouldReceive( 'set_fields' )
			->once()
			->with( $fields );
		$response_mock->shouldReceive( 'set_groups' )
			->once()
			->with( $groups );
		$response_mock->shouldReceive( 'set_date' )
			->once()
			->with( $current_time );

		$result = $this->response_factory->create_with_form( $form_mock );

		$this->assertSame( $response_mock, $result );
	}

	/**
	 * Test create_with_form handles invalid IP.
	 */
	public function testCreateWithFormInvalidIp() {
		$form_mock      = Mockery::mock( Form::class );
		$response_mock  = Mockery::mock( Response::class );
		$request_params = array( 'field1' => 'value1' );
		$fields         = array( 'field1' => 'Field 1' );
		$groups         = array( 'group1' => 'Group 1' );
		$current_time   = '2023-01-01 12:00:00';

		unset( $_SERVER['REMOTE_ADDR'] );

		WP_Mock::userFunction( 'current_time' )
			->with( 'mysql' )
			->andReturn( $current_time );

		$this->container->shouldReceive( 'get' )
			->once()
			->with( Response::class )
			->andReturn( $response_mock );

		$form_mock->shouldReceive( 'get_request_params' )
			->once()
			->andReturn( $request_params );
		$form_mock->shouldReceive( 'get_fields' )
			->once()
			->andReturn( $fields );
		$form_mock->shouldReceive( 'get_groups' )
			->once()
			->andReturn( $groups );

		$response_mock->shouldReceive( 'set_request_params' )
			->once()
			->with(
				array_merge(
					$request_params,
					array( '_omniform_user_ip' => '' )
				)
			);
		$response_mock->shouldReceive( 'set_fields' )
			->once()
			->with( $fields );
		$response_mock->shouldReceive( 'set_groups' )
			->once()
			->with( $groups );
		$response_mock->shouldReceive( 'set_date' )
			->once()
			->with( $current_time );

		$result = $this->response_factory->create_with_form( $form_mock );

		$this->assertSame( $response_mock, $result );
	}

	/**
	 * Test create_with_id creates response with valid ID.
	 */
	public function testCreateWithIdValid() {
		$response_id             = 123;
		$response_mock           = Mockery::mock( Response::class );
		$_response               = Mockery::mock( 'WP_Post' );
		$_response->ID           = $response_id;
		$_response->post_type    = 'omniform_response';
		$_response->post_content = json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
			array(
				'response' => array( 'field1' => 'value1' ),
				'fields'   => array( 'field1' => 'Field 1' ),
				'groups'   => array( 'group1' => 'Group 1' ),
			)
		);
		$_response->post_date    = '2023-01-01 12:00:00';
		$post_meta               = array(
			'_wp_http_referer'  => array( 'http://example.com' ),
			'_omniform_user_ip' => array( '192.168.1.1' ),
		);

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $response_id )
			->andReturn( $_response );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( $response_id )
			->andReturn( $post_meta );

		$this->container->shouldReceive( 'get' )
			->once()
			->with( Response::class )
			->andReturn( $response_mock );

		$response_mock->shouldReceive( 'set_request_params' )
			->once()
			->with(
				array_merge(
					array( 'field1' => 'value1' ),
					array(
						'_wp_http_referer'  => 'http://example.com',
						'_omniform_user_ip' => '192.168.1.1',
					)
				)
			);
		$response_mock->shouldReceive( 'set_fields' )
			->once()
			->with( array( 'field1' => 'Field 1' ) );
		$response_mock->shouldReceive( 'set_groups' )
			->once()
			->with( array( 'group1' => 'Group 1' ) );
		$response_mock->shouldReceive( 'set_date' )
			->once()
			->with( '2023-01-01 12:00:00' );

		$result = $this->response_factory->create_with_id( $response_id );

		$this->assertSame( $response_mock, $result );
	}

	/**
	 * Test create_with_id handles old response format.
	 */
	public function testCreateWithIdOldFormat() {
		$response_id             = 123;
		$response_mock           = Mockery::mock( Response::class );
		$_response               = Mockery::mock( 'WP_Post' );
		$_response->ID           = $response_id;
		$_response->post_type    = 'omniform_response';
		$_response->post_content = json_encode( array( 'field1' => 'value1' ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
		$_response->post_date    = '2023-01-01 12:00:00';
		$post_meta               = array(
			'_wp_http_referer'  => array( 'http://example.com' ),
			'_omniform_user_ip' => array( '192.168.1.1' ),
		);

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $response_id )
			->andReturn( $_response );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( $response_id )
			->andReturn( $post_meta );

		$this->container->shouldReceive( 'get' )
			->once()
			->with( Response::class )
			->andReturn( $response_mock );

		$response_mock->shouldReceive( 'set_request_params' )
			->once()
			->with(
				array_merge(
					array( 'field1' => 'value1' ),
					array(
						'_wp_http_referer'  => 'http://example.com',
						'_omniform_user_ip' => '192.168.1.1',
					)
				)
			);
		$response_mock->shouldReceive( 'set_fields' )
			->once()
			->with( array( 'field1' => 'field1' ) );
		$response_mock->shouldReceive( 'set_groups' )
			->once()
			->with( array( 'field1' => 'field1' ) );
		$response_mock->shouldReceive( 'set_date' )
			->once()
			->with( '2023-01-01 12:00:00' );

		$result = $this->response_factory->create_with_id( $response_id );

		$this->assertSame( $response_mock, $result );
	}

	/**
	 * Test create_with_id throws InvalidResponseIdException for invalid ID.
	 */
	public function testCreateWithIdInvalid() {
		$response_id = 'abc';

		$this->expectException( InvalidResponseIdException::class );

		$this->response_factory->create_with_id( $response_id );
	}

	/**
	 * Test create_with_id throws ResponseNotFoundException for non-existent response.
	 */
	public function testCreateWithIdNotFound() {
		$response_id = 123;

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $response_id )
			->andReturn( null );

		$this->expectException( ResponseNotFoundException::class );

		$this->response_factory->create_with_id( $response_id );
	}

	/**
	 * Test create_with_id throws ResponseNotFoundException for wrong post type.
	 */
	public function testCreateWithIdWrongPostType() {
		$response_id          = 123;
		$_response            = Mockery::mock( 'WP_Post' );
		$_response->ID        = $response_id;
		$_response->post_type = 'post';

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( $response_id )
			->andReturn( $_response );

		$this->expectException( ResponseNotFoundException::class );

		$this->response_factory->create_with_id( $response_id );
	}
}
