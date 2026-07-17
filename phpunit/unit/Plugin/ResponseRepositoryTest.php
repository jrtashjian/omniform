<?php
/**
 * Tests the ResponseRepository.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Exceptions\InvalidResponseIdException;
use OmniForm\Exceptions\ResponseNotFoundException;
use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;
use OmniForm\Plugin\ResponseRepository;
use OmniForm\Tests\Unit\BaseTestCase;
use Mockery;
use WP_Mock;

/**
 * Tests the ResponseRepository.
 */
class ResponseRepositoryTest extends BaseTestCase {
	/**
	 * @var ResponseRepository
	 */
	private ResponseRepository $repository;

	/**
	 * Sets up the test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->repository = new ResponseRepository();

		WP_Mock::userFunction( 'esc_attr' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( '__' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'sanitize_text_field' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'esc_url_raw' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'wp_generate_uuid4' )->andReturn( 'uuid-test' );
		WP_Mock::userFunction( 'wp_json_encode' )->andReturnUsing(
			static fn( $data ) => json_encode( $data ) // phpcs:ignore WordPress.WP.AlternativeFunctions
		);
		WP_Mock::userFunction( 'is_wp_error' )->andReturnUsing(
			static fn( $thing ) => $thing instanceof \WP_Error
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
	 * Loads a domain response from post content.
	 */
	public function testGetReturnsDomainResponse() {
		$payload = array(
			'version'    => 1,
			'schema'     => array(
				'fields' => array(
					array(
						'name'  => 'email',
						'label' => 'Email',
						'type'  => 'email',
						'rules' => array( 'required' ),
					),
				),
				'groups' => array(),
			),
			'submission' => array(
				'values' => array( 'email' => 'a@b.c' ),
			),
		);

		$post              = Mockery::mock( 'WP_Post' );
		$post->ID          = 55;
		$post->post_type   = 'omniform_response';
		$post->post_content = wp_json_encode( $payload );

		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 55 )
			->andReturn( $post );

		$response = $this->repository->get( 55 );

		$this->assertInstanceOf( Response::class, $response );
		$this->assertSame( 'Email', $response->schema()->field( 'email' )->label() );
		$this->assertSame(
			'a@b.c',
			$response->submission()->value( FieldPath::from_segments( array( 'email' ) ) )
		);
	}

	/**
	 * Legacy snapshots render from stored fields without the parent form.
	 */
	public function testFromPostHydratesLegacySnapshotWithoutForm() {
		$payload = array(
			'response' => array(
				'name'  => 'Jane',
				'email' => 'jane@example.com',
			),
			'fields'   => array(
				'name'  => 'Name',
				'email' => 'Email',
			),
			'groups'   => array(
				'contact' => 'Contact',
			),
		);

		$post               = Mockery::mock( 'WP_Post' );
		$post->ID           = 115;
		$post->post_type    = 'omniform_response';
		$post->post_content = wp_json_encode( $payload );
		$post->post_parent  = 999;

		$response = $this->repository->from_post( $post );

		$this->assertSame( 'Name', $response->schema()->field( 'name' )->label() );
		$this->assertSame( 'Email', $response->schema()->field( 'email' )->label() );
		$this->assertSame( 'Contact', $response->schema()->group( 'contact' )->label() );
		$this->assertSame(
			'Jane',
			$response->submission()->value( FieldPath::from_segments( array( 'name' ) ) )
		);
		$this->assertSame(
			'jane@example.com',
			$response->submission()->value( FieldPath::from_segments( array( 'email' ) ) )
		);
	}

	/**
	 * Nested legacy field paths stay addressable in the domain snapshot.
	 */
	public function testFromPostHydratesNestedLegacyFields() {
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
		$post->ID           = 12;
		$post->post_type    = 'omniform_response';
		$post->post_content = wp_json_encode( $payload );

		$response = $this->repository->from_post( $post );

		$this->assertSame( 'Email', $response->schema()->field( 'contact.email' )->label() );
		$this->assertSame(
			'nested@example.com',
			$response->submission()->value( FieldPath::from_segments( array( 'contact', 'email' ) ) )
		);
	}

	/**
	 * Very old flat payloads still hydrate without a form.
	 */
	public function testFromPostHydratesVeryOldFlatPayload() {
		$payload = array(
			'name'  => 'Legacy',
			'email' => 'legacy@example.com',
		);

		$post               = Mockery::mock( 'WP_Post' );
		$post->ID           = 13;
		$post->post_type    = 'omniform_response';
		$post->post_content = wp_json_encode( $payload );

		$response = $this->repository->from_post( $post );

		$this->assertSame( 'name', $response->schema()->field( 'name' )->label() );
		$this->assertSame(
			'Legacy',
			$response->submission()->value( FieldPath::from_segments( array( 'name' ) ) )
		);
	}

	/**
	 * Rejects non-positive IDs.
	 */
	public function testGetRejectsInvalidId() {
		$this->expectException( InvalidResponseIdException::class );

		$this->repository->get( 0 );
	}

	/**
	 * Throws when the post is missing.
	 */
	public function testGetThrowsWhenMissing() {
		WP_Mock::userFunction( 'get_post' )
			->once()
			->with( 99 )
			->andReturn( null );

		$this->expectException( ResponseNotFoundException::class );

		$this->repository->get( 99 );
	}

	/**
	 * Saves a response under a form.
	 */
	public function testSaveInsertsPost() {
		$response = new Response(
			new FormSchema(
				array(
					new Field( FieldPath::from_segments( array( 'email' ) ), 'Email', 'email' ),
				)
			),
			new Submission( array( 'email' => 'a@b.c' ) )
		);

		WP_Mock::userFunction( 'wp_insert_post' )
			->once()
			->with(
				\Mockery::on(
					static function ( array $args ): bool {
						$data = json_decode( $args['post_content'], true );

						return 'omniform_response' === $args['post_type']
							&& 'omniform_unread' === $args['post_status']
							&& 10 === $args['post_parent']
							&& 10 === $args['meta_input']['_omniform_id']
							&& '1.2.3.4' === $args['meta_input']['_omniform_user_ip']
							&& 'https://example.com' === $args['meta_input']['_wp_http_referer']
							&& 1 === $data['version']
							&& 'a@b.c' === $data['submission']['values']['email'];
					}
				),
				true
			)
			->andReturn( 77 );

		$id = $this->repository->save(
			$response,
			10,
			array(
				'user_ip' => '1.2.3.4',
				'referer' => 'https://example.com',
			)
		);

		$this->assertSame( 77, $id );
	}

	/**
	 * Rejects invalid form IDs on save.
	 */
	public function testSaveRejectsInvalidFormId() {
		$this->expectException( \InvalidArgumentException::class );

		$this->repository->save(
			new Response( new FormSchema(), new Submission() ),
			0
		);
	}
}
