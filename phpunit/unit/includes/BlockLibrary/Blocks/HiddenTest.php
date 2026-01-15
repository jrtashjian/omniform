<?php
/**
 * Tests the Hidden block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Hidden;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the Hidden block.
 */
class HiddenTest extends BaseTestCase {

	/**
	 * The Hidden instance.
	 *
	 * @var Hidden
	 */
	private $block;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Hidden();
	}

	/**
	 * Test get_field_label returns fieldName.
	 */
	public function testGetFieldLabel() {
		$this->block->render_block(
			array( 'fieldName' => 'test-hidden' ),
			'',
			$this->createMock( \stdClass::class )
		);

		$result = $this->block->get_field_label();

		$this->assertEquals( 'test-hidden', $result );
	}

	/**
	 * Test get_extra_wrapper_attributes returns hidden input attributes.
	 */
	public function testGetExtraWrapperAttributes() {
		$this->block->render_block(
			array(
				'fieldName'  => 'test-hidden',
				'fieldValue' => 'hidden-value',
			),
			'',
			$this->createMock( \stdClass::class )
		);

		$result = $this->block->get_extra_wrapper_attributes();

		$this->assertArrayHasKey( 'type', $result );
		$this->assertEquals( 'hidden', $result['type'] );
		$this->assertArrayHasKey( 'id', $result );
		$this->assertArrayHasKey( 'name', $result );
		$this->assertArrayHasKey( 'value', $result );
		$this->assertEquals( 'hidden-value', $result['value'] );
	}

	/**
	 * Test render_block returns hidden input.
	 */
	public function testRenderBlock() {
		$result = $this->block->render_block(
			array(
				'fieldName'  => 'test-hidden',
				'fieldValue' => 'hidden-value',
			),
			'',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '<input type="hidden" id="test-hidden" name="test-hidden" value="hidden-value" />', $result );
	}
}
