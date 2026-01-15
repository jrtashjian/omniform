<?php
/**
 * Tests the Label block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Label;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;

/**
 * Tests the Label block.
 */
class LabelTest extends BaseTestCase {

	/**
	 * The Label instance.
	 *
	 * @var Label
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

		$this->block = new Label();

		$this->wp_block_mock          = $this->createMock( \stdClass::class );
		$this->wp_block_mock->context = array();
	}

	/**
	 * Test render returns empty string when no fieldLabel context.
	 */
	public function testRenderEmptyWhenNoFieldLabel() {
		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '', $result );
	}

	/**
	 * Test render returns label with fieldLabel content.
	 */
	public function testRenderWithFieldLabel() {
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Label' );

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( '' );

		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<label for="Test-Label" >Test Label</label>', $result );
	}

	/**
	 * Test render returns label with fieldName for attribute.
	 */
	public function testRenderWithFieldName() {
		$this->wp_block_mock->context = array(
			'omniform/fieldLabel' => 'Test Label',
			'omniform/fieldName'  => 'test-field',
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( '' );

		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<label for="test-field" >Test Label</label>', $result );
	}

	/**
	 * Test render includes screen-reader-text class when isHidden.
	 */
	public function testRenderHidden() {
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Label' );

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="screen-reader-text"' );

		$result = $this->block->render_block(
			array( 'isHidden' => true ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<label for="Test-Label" class="screen-reader-text">Test Label</label>', $result );
	}

	/**
	 * Test render includes required abbr when required and required_label is *.
	 */
	public function testRenderRequiredAsterisk() {
		$this->wp_block_mock->context = array(
			'omniform/fieldLabel'      => 'Test Label',
			'omniform/fieldIsRequired' => true,
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( '' );

		// Mock the form get_required_label.
		$form_mock = Mockery::mock( \OmniForm\Plugin\Form::class );
		$form_mock->shouldReceive( 'get_required_label' )->andReturn( '*' );

		WP_Mock::userFunction( 'omniform' )->andReturn(
			Mockery::mock()->shouldReceive( 'get' )->with( \OmniForm\Plugin\Form::class )->andReturn( $form_mock )->getMock()
		);

		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<label for="Test-Label" >Test Label<abbr class="omniform-field-required" title="required">*</abbr></label>', $result );
	}

	/**
	 * Test render includes required span when required and required_label is text.
	 */
	public function testRenderRequiredText() {
		$this->wp_block_mock->context = array(
			'omniform/fieldLabel'      => 'Test Label',
			'omniform/fieldIsRequired' => true,
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( '' );

		// Mock the form get_required_label.
		$form_mock = Mockery::mock( \OmniForm\Plugin\Form::class );
		$form_mock->shouldReceive( 'get_required_label' )->andReturn( '(required)' );

		WP_Mock::userFunction( 'omniform' )->andReturn(
			Mockery::mock()->shouldReceive( 'get' )->with( \OmniForm\Plugin\Form::class )->andReturn( $form_mock )->getMock()
		);

		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<label for="Test-Label" >Test Label<span class="omniform-field-required">(required)</span></label>', $result );
	}
}
