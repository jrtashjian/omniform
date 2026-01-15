<?php
/**
 * Tests the Fieldset block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use Mockery;
use OmniForm\BlockLibrary\Blocks\Fieldset;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the Fieldset block.
 */
class FieldsetTest extends BaseTestCase {

	/**
	 * The Fieldset instance.
	 *
	 * @var Fieldset
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

		$this->block = new Fieldset();

		$this->wp_block_mock = $this->createMock( \stdClass::class );

		// Mock omniform() function and its dependencies.
		$form_mock = Mockery::mock();
		$form_mock->shouldReceive( 'get_required_label' )->andReturn( '*' );

		$omniform_mock = Mockery::mock();
		$omniform_mock->shouldReceive( 'get' )->with( \OmniForm\Plugin\Form::class )->andReturn( $form_mock );

		WP_Mock::userFunction( 'omniform' )->andReturn( $omniform_mock );

		// Mock WP_Block_Supports static method using Mockery.
		$block_supports_mock = Mockery::mock( 'alias:WP_Block_Supports' );
		$block_supports_mock->shouldReceive( 'get_instance' )
			->andReturnSelf();
		$block_supports_mock->shouldReceive( 'apply_block_supports' )
			->andReturn( array() );
	}

	/**
	 * Test render_block returns fieldset with label.
	 */
	public function testRenderBlockWithLabel() {
		$content = '<input type="text" name="field" />';

		$result = $this->block->render_block(
			array( 'fieldLabel' => 'Test Group' ),
			$content,
			$this->wp_block_mock
		);

		$expected = '<fieldset ><legend>Test Group</legend><div class="omniform-field-label" aria-hidden="true">Test Group</div>' . $content . '</fieldset>';

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Test render_block returns fieldset with required label.
	 */
	public function testRenderBlockWithLabelRequired() {
		$content = '<input type="text" name="field" />';

		$result = $this->block->render_block(
			array(
				'fieldLabel' => 'Test Group',
				'isRequired' => true,
			),
			$content,
			$this->wp_block_mock
		);

		$expected = '<fieldset ><legend>Test Group<abbr class="omniform-field-required" title="required">*</abbr></legend><div class="omniform-field-label" aria-hidden="true">Test Group<abbr class="omniform-field-required" title="required">*</abbr></div>' . $content . '</fieldset>';

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Test render_block returns empty string without label.
	 */
	public function testRenderBlockWithoutLabel() {
		$result = $this->block->render_block(
			array(),
			'<input type="text" name="field" />',
			$this->wp_block_mock
		);

		$this->assertEquals( '', $result );
	}

	/**
	 * Test get_field_group_label returns the field label.
	 */
	public function testGetFieldGroupLabel() {
		$this->block->render_block(
			array( 'fieldLabel' => 'Test Group' ),
			'',
			$this->wp_block_mock
		);

		$result = $this->block->get_field_group_label();

		$this->assertEquals( 'Test Group', $result );
	}

	/**
	 * Test get_field_group_name returns sanitized field name.
	 */
	public function testGetFieldGroupName() {
		$this->block->render_block(
			array(
				'fieldLabel' => 'Test Group',
				'fieldName'  => 'test group',
			),
			'',
			$this->wp_block_mock
		);

		$result = $this->block->get_field_group_name();

		$this->assertEquals( 'test-group', $result );
	}

	/**
	 * Test get_field_group_name returns sanitized field label when no name.
	 */
	public function testGetFieldGroupNameFromLabel() {
		$this->block->render_block(
			array( 'fieldLabel' => 'Test Group' ),
			'',
			$this->wp_block_mock
		);

		$result = $this->block->get_field_group_name();

		$this->assertEquals( 'Test-Group', $result );
	}
}
