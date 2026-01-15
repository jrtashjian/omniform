<?php
/**
 * Tests the Textarea block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Textarea;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;

/**
 * Tests the Textarea block.
 */
class TextareaTest extends BaseTestCase {

	/**
	 * The Textarea instance.
	 *
	 * @var Textarea
	 */
	private $block;

	/**
	 * The WP_Block mock instance.
	 *
	 * @var \WP_Block|Mockery\MockInterface
	 */
	private $wp_block_mock;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Textarea();

		$this->wp_block_mock          = $this->createMock( \stdClass::class );
		$this->wp_block_mock->context = array();

		WP_Mock::userFunction( 'esc_textarea' )->andReturnUsing(
			function ( $arg ) {

				return is_null( $arg ) ? '' : $arg;
			}
		);

		WP_Mock::userFunction( 'wp_strip_all_tags' )->andReturnUsing(
			function ( $arg ) {

				return is_null( $arg ) ? '' : $arg;
			}
		);

		WP_Mock::userFunction( 'esc_attr' )->andReturnUsing(
			function ( $arg ) {

				return is_null( $arg ) ? '' : $arg;
			}
		);

		WP_Mock::userFunction( 'sanitize_html_class' )->andReturnArg( 0 );

		// Mock WP_Block_Supports static method using Mockery.
		$block_supports_mock = Mockery::mock( 'alias:WP_Block_Supports' );
		$block_supports_mock->shouldReceive( 'get_instance' )
			->andReturnSelf();
		$block_supports_mock->shouldReceive( 'apply_block_supports' )
			->andReturn( array() );
	}

	/**
	 * Test render_block returns empty when no fieldLabel.
	 */
	public function testRenderBlockNoFieldLabel() {
		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '', $result );
	}

	/**
	 * Test render_block returns textarea with value.
	 */
	public function testRenderBlockWithValue() {
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Label' );

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'id="Test-Label" name="Test-Label" value="Textarea content" aria-label="Test Label"' );

		$result = $this->block->render_block(
			array( 'fieldValue' => 'Textarea content' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<textarea id="Test-Label" name="Test-Label" value="Textarea content" aria-label="Test Label">Textarea content</textarea>', $result );
	}

	/**
	 * Test render_block returns textarea with placeholder.
	 */
	public function testRenderBlockPlaceholder() {
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Label' );

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'id="Test-Label" name="Test-Label" placeholder="Enter text" aria-label="Test Label"' );

		$result = $this->block->render_block(
			array( 'fieldPlaceholder' => 'Enter text' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<textarea id="Test-Label" name="Test-Label" placeholder="Enter text" aria-label="Test Label"></textarea>', $result );
	}

	/**
	 * Test render_block returns textarea with aria-label.
	 */
	public function testRenderBlockAriaLabel() {
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Label' );

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'id="Test-Label" name="Test-Label" aria-label="Test Label"' );

		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<textarea id="Test-Label" name="Test-Label" aria-label="Test Label"></textarea>', $result );
	}
}
