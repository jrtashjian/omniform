<?php
/**
 * Tests FormSubmitter.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use Mockery;
use OmniForm\Exceptions\FormNotFoundException;
use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Form\Form;
use OmniForm\Form\FormSchema;
use OmniForm\Form\Response;
use OmniForm\Form\Submission;
use OmniForm\Form\SubmissionValidator;
use OmniForm\Plugin\BlockFormSchemaParser;
use OmniForm\Plugin\FormRepository;
use OmniForm\Plugin\FormSubmitter;
use OmniForm\Plugin\ResponseRepository;
use OmniForm\Plugin\SubmissionFactory;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests FormSubmitter.
 */
class FormSubmitterTest extends BaseTestCase {
	/**
	 * @var FormRepository&\Mockery\MockInterface
	 */
	private $forms;

	/**
	 * @var BlockFormSchemaParser&\Mockery\MockInterface
	 */
	private $parser;

	/**
	 * @var SubmissionFactory&\Mockery\MockInterface
	 */
	private $submissions;

	/**
	 * @var ResponseRepository&\Mockery\MockInterface
	 */
	private $responses;

	/**
	 * @var FormSubmitter
	 */
	private FormSubmitter $submitter;

	/**
	 * Sets up the test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->forms       = Mockery::mock( FormRepository::class );
		$this->parser      = Mockery::mock( BlockFormSchemaParser::class );
		$this->submissions = Mockery::mock( SubmissionFactory::class );
		$this->responses   = Mockery::mock( ResponseRepository::class );

		$this->submitter = new FormSubmitter(
			$this->forms,
			$this->parser,
			$this->submissions,
			new SubmissionValidator(),
			$this->responses
		);

		WP_Mock::userFunction( '__' )->andReturnUsing( static fn( $v ) => $v );
		WP_Mock::userFunction( 'do_action' )->zeroOrMoreTimes()->andReturnNull();
	}

	/**
	 * Tears down the test environment.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Happy path validates, saves, and returns success.
	 */
	public function testSubmitSuccess() {
		$form = new Form(
			content: '<!-- form -->',
			title: 'Contact',
			status: 'publish',
			id: 10
		);

		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'email' ) ),
					'Email',
					'email',
					array( 'required' )
				),
			)
		);

		$submission = new Submission( array( 'email' => 'a@b.c' ) );

		$this->forms->shouldReceive( 'get' )->once()->with( 10 )->andReturn( $form );
		$this->parser->shouldReceive( 'parse' )->once()->with( '<!-- form -->' )->andReturn( $schema );
		$this->submissions->shouldReceive( 'from_request' )
			->once()
			->with( array( 'email' => 'a@b.c' ), array() )
			->andReturn( $submission );
		$this->responses->shouldReceive( 'save' )
			->once()
			->with( Mockery::type( Response::class ), 10, array( 'user_ip' => '1.1.1.1' ) )
			->andReturn( 99 );

		$result = $this->submitter->submit(
			10,
			array( 'email' => 'a@b.c' ),
			array(),
			array( 'user_ip' => '1.1.1.1' )
		);

		$this->assertTrue( $result->is_success() );
		$this->assertSame( 99, $result->response_id() );
		$this->assertInstanceOf( Response::class, $result->response() );
	}

	/**
	 * Missing required field yields validation failure without save.
	 */
	public function testSubmitValidationFailure() {
		$form = new Form(
			content: '<!-- form -->',
			status: 'publish',
			id: 10
		);

		$schema = new FormSchema(
			array(
				new Field(
					FieldPath::from_segments( array( 'email' ) ),
					'Email',
					'email',
					array( 'required' )
				),
			)
		);

		$this->forms->shouldReceive( 'get' )->once()->andReturn( $form );
		$this->parser->shouldReceive( 'parse' )->once()->andReturn( $schema );
		$this->submissions->shouldReceive( 'from_request' )
			->once()
			->andReturn( new Submission( array() ) );
		$this->responses->shouldNotReceive( 'save' );

		$result = $this->submitter->submit( 10, array() );

		$this->assertFalse( $result->is_success() );
		$this->assertTrue( $result->is_validation_failure() );
		$this->assertNotEmpty( $result->invalid_fields() );
	}

	/**
	 * Unpublished form is rejected.
	 */
	public function testSubmitRejectsUnpublishedForm() {
		$form = new Form(
			content: '<!-- form -->',
			status: 'draft',
			id: 10
		);

		$this->forms->shouldReceive( 'get' )->once()->andReturn( $form );
		$this->parser->shouldNotReceive( 'parse' );
		$this->responses->shouldNotReceive( 'save' );

		$result = $this->submitter->submit( 10, array( 'email' => 'a@b.c' ) );

		$this->assertFalse( $result->is_success() );
		$this->assertSame( 'form_not_published', $result->error_code() );
	}

	/**
	 * Missing form is rejected.
	 */
	public function testSubmitRejectsMissingForm() {
		$this->forms->shouldReceive( 'get' )
			->once()
			->andThrow( new FormNotFoundException( 'missing' ) );

		$result = $this->submitter->submit( 999, array() );

		$this->assertFalse( $result->is_success() );
		$this->assertSame( 'form_not_found', $result->error_code() );
		$this->assertSame( 'missing', $result->error_message() );
	}
}
