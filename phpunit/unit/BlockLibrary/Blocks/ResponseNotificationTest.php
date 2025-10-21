<?php
/**
 * Tests for ResponseNotification.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\ResponseNotification;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for ResponseNotification.
 */
class ResponseNotificationTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var ResponseNotification
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new ResponseNotification();
	}

	/**
	 * Data provider for success render tests.
	 */
	public function success_render_data_provider() {
		return array(
			'success hidden'               => array(
				array(
					'message_type'         => 'success',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => 'display:none;',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'className' => 'is-style-success' ),
					'expected_class'       => 'success-response-notification is-style-success',
				),
			),
			'success shown'                => array(
				array(
					'message_type'         => 'success',
					'validation_succeeded' => true,
					'validation_failed'    => false,
					'expected_display'     => 'display:block;',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'className' => 'is-style-success' ),
					'expected_class'       => 'success-response-notification is-style-success',
				),
			),
			'validation messages'          => array(
				array(
					'message_type'         => 'success',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => 'display:none;',
					'validation_messages'  => array( 'Error 1', 'Error 2' ),
					'block_attributes'     => array( 'className' => 'is-style-success' ),
					'expected_class'       => 'success-response-notification is-style-success',
				),
			),
			'fallback messageType success' => array(
				array(
					'message_type'         => 'success',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => 'display:none;',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'messageType' => 'success' ),
					'expected_class'       => 'success-response-notification is-style-success',
				),
			),
			'fallback border success'      => array(
				array(
					'message_type'         => 'success',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => 'display:none;',
					'validation_messages'  => array(),
					'block_attributes'     => array(
						'style' => array(
							'border' => array(
								'left' => array(
									'color' => 'var(--wp--preset--color--vivid-green-cyan,#00d084)',
								),
							),
						),
					),
					'expected_class'       => 'success-response-notification is-style-success',
				),
			),
		);
	}

	/**
	 * Data provider for error render tests.
	 */
	public function error_render_data_provider() {
		return array(
			'error hidden'               => array(
				array(
					'message_type'         => 'error',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => 'display:none;',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'className' => 'is-style-error' ),
					'expected_class'       => 'error-response-notification is-style-error',
				),
			),
			'error shown'                => array(
				array(
					'message_type'         => 'error',
					'validation_succeeded' => false,
					'validation_failed'    => true,
					'expected_display'     => 'display:block;',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'className' => 'is-style-error' ),
					'expected_class'       => 'error-response-notification is-style-error',
				),
			),
			'fallback messageType error' => array(
				array(
					'message_type'         => 'error',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => 'display:none;',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'messageType' => 'error' ),
					'expected_class'       => 'error-response-notification is-style-error',
				),
			),
			'fallback border error'      => array(
				array(
					'message_type'         => 'error',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => 'display:none;',
					'validation_messages'  => array(),
					'block_attributes'     => array(
						'style' => array(
							'border' => array(
								'left' => array(
									'color' => 'var(--wp--preset--color--vivid-red,#cf2e2e)',
								),
							),
						),
					),
					'expected_class'       => 'error-response-notification is-style-error',
				),
			),
		);
	}

	/**
	 * Data provider for info render tests.
	 */
	public function info_render_data_provider() {
		return array(
			'info'                         => array(
				array(
					'message_type'         => 'info',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => '',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'className' => 'is-style-info' ),
					'expected_class'       => 'info-response-notification is-style-info',
				),
			),
			'fallback messageType unknown' => array(
				array(
					'message_type'         => 'info',
					'validation_succeeded' => false,
					'validation_failed'    => false,
					'expected_display'     => '',
					'validation_messages'  => array(),
					'block_attributes'     => array( 'messageType' => 'unknown' ),
					'expected_class'       => 'info-response-notification is-style-info',
				),
			),
		);
	}

	/**
	 * Test render success.
	 *
	 * @param array $data Test data.
	 * @dataProvider success_render_data_provider
	 */
	public function test_render_success( $data ) {
		$this->run_render_test( $data );
	}

	/**
	 * Test render error.
	 *
	 * @param array $data Test data.
	 * @dataProvider error_render_data_provider
	 */
	public function test_render_error( $data ) {
		$this->run_render_test( $data );
	}

	/**
	 * Test render info.
	 *
	 * @param array $data Test data.
	 * @dataProvider info_render_data_provider
	 */
	public function test_render_info( $data ) {
		$this->run_render_test( $data );
	}

	/**
	 * Helper method to run render test.
	 *
	 * @param array $data Test data.
	 */
	private function run_render_test( $data ) {
		$mock_form = \Mockery::mock( '\OmniForm\Plugin\Form' );
		$mock_form->shouldReceive( 'get_validation_messages' )->andReturn( $data['validation_messages'] );
		$mock_form->shouldReceive( 'validation_failed' )->andReturn( $data['validation_failed'] );
		$mock_form->shouldReceive( 'validation_succeeded' )->andReturn( $data['validation_succeeded'] );
		$mock_app = \Mockery::mock( '\OmniForm\Application' );
		$mock_app->shouldReceive( 'get' )->with( \OmniForm\Plugin\Form::class )->andReturn( $mock_form );
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturnUsing(
			function ( $args ) {
				$class = isset( $args['class'] ) ? $args['class'] : '';
				$style = isset( $args['style'] ) ? $args['style'] : '';
				return sprintf( 'class="%s" style="%s"', $class, $style );
			}
		);
		\WP_Mock::userFunction( 'wp_kses' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);
		\WP_Mock::userFunction( 'do_blocks' )->andReturnUsing(
			function ( $content ) {
				return $content;
			}
		);
		\WP_Mock::userFunction( 'esc_attr' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);
		\WP_Mock::userFunction( 'esc_html' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);

		$this->block->render_block(
			array_merge(
				array( 'messageContent' => 'Test message' ),
				$data['block_attributes']
			),
			'',
			\Mockery::mock( '\WP_Block' )
		);
		$result = $this->block->render();

		$this->assertStringContainsString( sprintf( 'class="%s"', $data['expected_class'] ), $result );
		$this->assertStringContainsString( sprintf( 'style="%s"', $data['expected_display'] ), $result );
		$this->assertStringContainsString( '<p>Test message</p>', $result );

		if ( ! empty( $data['validation_messages'] ) ) {
			$this->assertStringContainsString( '<ul class="wp-block-list">', $result );
			foreach ( $data['validation_messages'] as $message ) {
				$this->assertStringContainsString( sprintf( '<li>%s</li>', $message ), $result );
			}
		}
	}
}
