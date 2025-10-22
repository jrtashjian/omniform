<?php
/**
 * Tests for Form.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Form;
use OmniForm\Plugin\FormFactory;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Form.
 */
class FormTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Form
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Form();
	}

	/**
	 * Create a basic mock form with common expectations.
	 *
	 * @param array $overrides Override default expectations.
	 * @return \Mockery\MockInterface
	 */
	private function createMockForm( array $overrides = array() ) {
		$defaults = array(
			'get_content'           => '<p>Form content</p>',
			'get_id'                => 123,
			'is_password_protected' => false,
			'is_published'          => true,
		);

		$expectations = array_merge( $defaults, $overrides );

		$mock_form = \Mockery::mock( '\OmniForm\Plugin\Form' );
		foreach ( $expectations as $method => $return ) {
			$mock_form->shouldReceive( $method )->andReturn( $return );
		}

		return $mock_form;
	}

	/**
	 * Create a mock factory that returns the given form.
	 *
	 * @param \Mockery\MockInterface $mock_form The form to return.
	 * @param string                 $method The factory method to call.
	 * @param mixed                  $argument The argument for the factory method.
	 * @return \Mockery\MockInterface
	 */
	private function createMockFactory( $mock_form, $method = 'create_with_id', $argument = 123 ) {
		$mock_factory = \Mockery::mock();
		$mock_factory->shouldReceive( $method )->with( $argument )->andReturn( $mock_form );
		return $mock_factory;
	}

	/**
	 * Create a mock app with the given factory.
	 *
	 * @param \Mockery\MockInterface $mock_factory The factory to return.
	 * @return \Mockery\MockInterface
	 */
	private function createMockApp( $mock_factory ) {
		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'get' )->with( FormFactory::class )->andReturn( $mock_factory );
		return $mock_app;
	}

	/**
	 * Set up common WordPress function mocks.
	 */
	private function setupCommonWpMocks() {
		\WP_Mock::userFunction( 'do_blocks' )->andReturnUsing(
			function ( $content ) {
				return $content;
			}
		);
		\WP_Mock::userFunction( 'wp_nonce_field' )->andReturn( '<input type="hidden" name="_wpnonce" value="test">' );
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( '' );
		\WP_Mock::userFunction( 'is_preview' )->andReturn( false );
		\WP_Mock::userFunction( 'serialize_block' )->andReturnUsing(
			function () {
				return '<!-- wp:omniform/response-notification -->';
			}
		);
	}

	/**
	 * Test standalone form submission success.
	 */
	public function test_standalone_submission_success() {
		$form_content = '<p>Form content</p>';
		$form_hash    = sha1( $form_content );

		$mock_form = $this->createMockForm(
			array(
				'get_content' => $form_content,
				'get_id'      => null,
			)
		);
		$mock_form->shouldReceive( 'set_required_label' )->with( '*' );
		$mock_form->shouldReceive( 'set_request_params' )->with( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$mock_form->shouldReceive( 'validate' )->andReturn( array() );

		$mock_response = \Mockery::mock( '\OmniForm\Plugin\Response' );
		$mock_response->shouldReceive( 'email_content' )->andReturn( 'Email content' );

		$mock_response_factory = \Mockery::mock();
		$mock_response_factory->shouldReceive( 'create_with_form' )->with( $mock_form )->andReturn( $mock_response );

		$mock_factory = \Mockery::mock();
		$mock_factory->shouldReceive( 'create_with_content' )->andReturn( $mock_form );

		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'get' )->with( FormFactory::class )->andReturn( $mock_factory );
		$mock_app->shouldReceive( 'get' )->with( '\OmniForm\Plugin\ResponseFactory' )->andReturn( $mock_response_factory );

		$this->setupCommonWpMocks();
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'serialize_blocks' )->andReturn( 'serialized content' );
		\WP_Mock::userFunction( 'sanitize_text_field' )->andReturn( $form_hash );
		\WP_Mock::userFunction( 'wp_verify_nonce' )->andReturn( true );
		\WP_Mock::userFunction( 'wp_mail' )->with( 'admin@example.com', 'New Response: Test Blog - Test Form', 'Email content' );
		\WP_Mock::userFunction( 'get_option' )->with( 'admin_email' )->andReturn( 'admin@example.com' );
		\WP_Mock::userFunction( 'get_option' )->with( 'blogname' )->andReturn( 'Test Blog' );
		\WP_Mock::userFunction( 'esc_attr' )->andReturnUsing(
			function ( $text ) {
				return $text;
			}
		);
		\WP_Mock::userFunction( 'wp_kses' )->andReturnUsing(
			function ( $content ) {
				return $content;
			}
		);
		\WP_Mock::userFunction( '__' )->andReturnUsing(
			function ( $text ) {
				return $text;
			}
		);

		// Simulate POST submission.
		$_POST    = array(
			'omniform_hash' => $form_hash,
			'_wpnonce'      => 'test_nonce',
			'test'          => 'data',
		);
		$_REQUEST = array(
			'_wpnonce' => 'test_nonce',
		);

		$block_mock               = \Mockery::mock( '\WP_Block' );
		$block_mock->parsed_block = array( 'innerBlocks' => array() );

		$this->block->render_block(
			array(
				'required_label' => '*',
				'form_title'     => 'Test Form',
			),
			'inner content',
			$block_mock
		);
		$result = $this->block->render();

		$this->assertStringContainsString( '<form method="post" action="" >', $result );
	}

	/**
	 * Test render with form creation exception.
	 */
	public function test_render_form_exception() {
		$mock_factory = \Mockery::mock();
		$mock_factory->shouldReceive( 'create_with_id' )->andThrow( new \Exception( 'Form not found' ) );
		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'get' )->with( FormFactory::class )->andReturn( $mock_factory );
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'current_user_can' )->andReturn( true );

		$this->block->render_block( array( 'ref' => 1 ), '', \Mockery::mock( '\WP_Block' ) );
		$result = $this->block->render();
		$this->assertStringContainsString( 'Form not found', $result );
	}

	/**
	 * Test render with empty form content.
	 */
	public function test_render_empty_form_content() {
		$mock_form    = $this->createMockForm( array( 'get_content' => '' ) );
		$mock_factory = $this->createMockFactory( $mock_form );
		$mock_app     = $this->createMockApp( $mock_factory );

		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );

		$result = $this->block->render_block( array( 'ref' => 123 ), '', \Mockery::mock( '\WP_Block' ) );

		$this->assertEquals( '', $result );
	}

	/**
	 * Test render with entity_id (standard mode).
	 */
	public function test_render_with_entity_id() {
		$mock_form = $this->createMockForm();
		$mock_form->shouldReceive( 'get_submit_method' )->andReturn( 'POST' );
		$mock_form->shouldReceive( 'get_submit_action' )->andReturn( '' );

		$mock_factory = $this->createMockFactory( $mock_form );
		$mock_app     = $this->createMockApp( $mock_factory );

		$this->setupCommonWpMocks();
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'do_action' );

		$result = $this->block->render_block( array( 'ref' => 123 ), '', \Mockery::mock( '\WP_Block' ) );

		$this->assertStringContainsString( '<form method="post" action="" >', $result );
		$this->assertStringContainsString( '<p>Form content</p>', $result );
	}

	/**
	 * Test render standalone form without entity_id.
	 */
	public function test_render_standalone() {
		$mock_form = $this->createMockForm( array( 'get_id' => null ) );
		$mock_form->shouldReceive( 'set_required_label' )->with( '*' );

		$mock_factory = $this->createMockFactory( $mock_form, 'create_with_content', 'serialized content' );
		$mock_app     = $this->createMockApp( $mock_factory );

		$this->setupCommonWpMocks();
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'serialize_blocks' )->andReturn( 'serialized content' );
		\WP_Mock::userFunction( 'sanitize_text_field' )->andReturn( '' );

		$block_mock               = \Mockery::mock( '\WP_Block' );
		$block_mock->parsed_block = array( 'innerBlocks' => array() );

		$result = $this->block->render_block( array( 'required_label' => '*' ), 'inner content', $block_mock );

		$this->assertStringContainsString( '<form method="post" action="" >', $result );
		$this->assertStringContainsString( '<p>Form content</p>', $result );
	}

	/**
	 * Test password protected form.
	 */
	public function test_password_protected_form() {
		$mock_form    = $this->createMockForm( array( 'is_password_protected' => true ) );
		$mock_factory = $this->createMockFactory( $mock_form );
		$mock_app     = $this->createMockApp( $mock_factory );

		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'get_the_password_form' )->andReturn( '<form>Password form</form>' );

		$result = $this->block->render_block( array( 'ref' => 123 ), '', \Mockery::mock( '\WP_Block' ) );

		$this->assertEquals( '<form>Password form</form>', $result );
	}

	/**
	 * Test unpublished form.
	 */
	public function test_unpublished_form() {
		$mock_form = $this->createMockForm( array( 'is_published' => false ) );
		$mock_form->shouldReceive( 'get_title' )->andReturn( 'Test Form' );

		$mock_factory = $this->createMockFactory( $mock_form );
		$mock_app     = $this->createMockApp( $mock_factory );

		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'current_user_can' )->with( 'edit_post', 123 )->andReturn( true );
		\WP_Mock::userFunction( 'is_preview' )->andReturn( false );
		\WP_Mock::userFunction( 'admin_url' )->andReturn( 'http://example.com/wp-admin/post.php?post=123&action=edit' );
		\WP_Mock::userFunction( 'esc_url' )->andReturn( 'http://example.com/wp-admin/post.php?post=123&action=edit' );
		\WP_Mock::userFunction( 'esc_html' )->andReturnUsing(
			function ( $text ) {
				return $text;
			}
		);
		\WP_Mock::userFunction( '__' )->andReturnUsing(
			function ( $text ) {
				return $text;
			}
		);

		$result = $this->block->render_block( array( 'ref' => 123 ), '', \Mockery::mock( '\WP_Block' ) );

		$this->assertStringContainsString( 'You must publish the "Test Form" form for visitors to see it.', $result );
		$this->assertStringContainsString( 'Edit the form', $result );
	}

	/**
	 * Test form with submit action (no default notifications).
	 */
	public function test_with_submit_action() {
		$mock_form = $this->createMockForm();
		$mock_form->shouldReceive( 'get_submit_method' )->andReturn( 'POST' );
		$mock_form->shouldReceive( 'get_submit_action' )->andReturn( '/custom-action' );

		$mock_factory = $this->createMockFactory( $mock_form );
		$mock_app     = $this->createMockApp( $mock_factory );

		$this->setupCommonWpMocks();
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'do_action' );

		$result = $this->block->render_block(
			array(
				'ref'           => 123,
				'submit_action' => '/custom-action',
			),
			'',
			\Mockery::mock( '\WP_Block' )
		);

		$this->assertStringContainsString( '<form method="post" action="/custom-action" >', $result );
		$this->assertStringContainsString( '<p>Form content</p>', $result );
		// Should not contain default notifications.
		$this->assertStringNotContainsString( 'Success! Your submission has been completed.', $result );
	}

	/**
	 * Test form without submit action (adds default notifications).
	 */
	public function test_without_submit_action() {
		$mock_form = $this->createMockForm( array( 'get_id' => null ) );
		$mock_form->shouldReceive( 'set_required_label' )->with( '*' );

		$mock_factory = $this->createMockFactory( $mock_form, 'create_with_content', 'serialized content' );
		$mock_app     = $this->createMockApp( $mock_factory );

		\WP_Mock::userFunction( 'do_blocks' )->andReturnUsing(
			function ( $content ) {
				return $content;
			}
		);
		\WP_Mock::userFunction( 'wp_nonce_field' )->andReturn( '<input type="hidden" name="_wpnonce" value="test">' );
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( '' );
		\WP_Mock::userFunction( 'is_preview' )->andReturn( false );
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'serialize_blocks' )->andReturn( 'serialized content' );
		\WP_Mock::userFunction( 'serialize_block' )->andReturnUsing(
			function ( $block ) {
				$attrs = $block['attrs'] ?? array();
				if ( isset( $attrs['messageContent'] ) ) {
					return $attrs['messageContent'];
				}
				return '<!-- wp:omniform/response-notification -->';
			}
		);
		\WP_Mock::userFunction( 'sanitize_text_field' )->andReturn( '' );

		$block_mock               = \Mockery::mock( '\WP_Block' );
		$block_mock->parsed_block = array( 'innerBlocks' => array() );

		$result = $this->block->render_block( array( 'required_label' => '*' ), 'inner content', $block_mock );

		$this->assertStringContainsString( 'Success! Your submission has been completed.', $result );
		$this->assertStringContainsString( 'Unfortunately, your submission was not successful.', $result );
	}

	/**
	 * Test form wrapper for regular forms.
	 */
	public function test_form_wrapper_regular() {
		$reflection = new \ReflectionMethod( $this->block, 'get_form_wrapper' );
		$reflection->setAccessible( true );
		$result = $reflection->invoke( $this->block, 'POST', '/submit', '<p>content</p>' );
		$this->assertEquals( '<form method="post" action="/submit" ><p>content</p></form>', $result );
	}

	/**
	 * Test form wrapper for comment forms.
	 */
	public function test_form_wrapper_comment_form() {
		\WP_Mock::userFunction( 'wp_enqueue_script' )->with( 'comment-reply' );
		\WP_Mock::userFunction( 'get_comment_id_fields' )->andReturn( '<input type="hidden" name="comment_post_ID" value="1">' );
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'id="respond" class="comment-respond"' );

		// Set up block instance with context.
		$block_mock          = \Mockery::mock( '\WP_Block' );
		$block_mock->context = array( 'postId' => 1 );
		$instance_property   = new \ReflectionProperty( $this->block, 'instance' );
		$instance_property->setAccessible( true );
		$instance_property->setValue( $this->block, $block_mock );

		$reflection = new \ReflectionMethod( $this->block, 'get_form_wrapper' );
		$reflection->setAccessible( true );
		$result = $reflection->invoke( $this->block, 'POST', '/wp-comments-post.php', '<p>content</p>' );
		$this->assertStringContainsString( '<div id="respond" class="comment-respond"><form method="post" action="/wp-comments-post.php"><p>content</p><input type="hidden" name="comment_post_ID" value="1"></form></div>', $result );
	}

	/**
	 * Data provider for response notification detection tests.
	 *
	 * @return array
	 */
	public function responseNotificationDataProvider() {
		return array(
			// Success notifications.
			array( 'has_success_response_notification', '<!-- wp:omniform/response-notification {"className":"is-style-success"} -->', true ),
			array( 'has_success_response_notification', '<!-- wp:omniform/response-notification {"messageType":"success"} -->', true ),
			array( 'has_success_response_notification', '<!-- wp:omniform/response-notification {"color":"vivid-green-cyan,#00d084"} -->', true ),
			array( 'has_success_response_notification', '', false ),

			// Error notifications.
			array( 'has_error_response_notification', '<!-- wp:omniform/response-notification {"className":"is-style-error"} -->', true ),
			array( 'has_error_response_notification', '<!-- wp:omniform/response-notification {"messageType":"error"} -->', true ),
			array( 'has_error_response_notification', '<!-- wp:omniform/response-notification {"color":"vivid-red,#cf2e2e"} -->', true ),
			array( 'has_error_response_notification', '', false ),
		);
	}

	/**
	 * Test response notification detection.
	 *
	 * @param string $method The method to test.
	 * @param string $content The content to set.
	 * @param bool   $expected The expected result.
	 * @dataProvider responseNotificationDataProvider
	 */
	public function test_response_notification_detection( $method, $content, $expected ) {
		$content_property = new \ReflectionProperty( $this->block, 'content' );
		$content_property->setAccessible( true );
		$content_property->setValue( $this->block, $content );

		$reflection_method = new \ReflectionMethod( $this->block, $method );
		$reflection_method->setAccessible( true );

		$this->assertEquals( $expected, $reflection_method->invoke( $this->block ) );
	}

	/**
	 * Test render response notification.
	 */
	public function test_render_response_notification() {
		\WP_Mock::userFunction( 'serialize_block' )->andReturnUsing(
			function ( $block ) {
				return '<!-- wp:omniform/response-notification ' . json_encode( $block['attrs'] ) . ' -->'; // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
			}
		);

		$reflection = new \ReflectionMethod( $this->block, 'render_reponse_notification' );
		$reflection->setAccessible( true );
		$result = $reflection->invoke( $this->block, 'success', 'Test message' );
		$this->assertStringContainsString( '"messageContent":"Test message"', $result );
		$this->assertStringContainsString( '"className":"is-style-success"', $result );
	}
}
