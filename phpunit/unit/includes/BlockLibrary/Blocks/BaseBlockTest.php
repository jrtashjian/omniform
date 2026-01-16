<?php
/**
 * Tests the BaseBlock class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseBlock;

/**
 * Tests the BaseBlock class.
 */
class BaseBlockTest extends \OmniForm\Tests\Unit\BaseTestCase {
	/**
	 * Test instance.
	 *
	 * @var TestableBaseBlock
	 */
	private $block;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new TestableBaseBlock();
	}

	/**
	 * Test block_type_name method.
	 */
	public function testBlockTypeName() {
		$result = $this->block->block_type_name();

		$this->assertEquals( 'testable-base-block', $result );
	}

	/**
	 * Test render_block method.
	 */
	public function testRenderBlock() {
		$result = $this->block->render_block(
			array( 'test_attr' => 'test_value' ),
			'',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '<div>Test Block Content</div>', $result );
	}

	/**
	 * Test get_block_attribute method with existing attribute.
	 */
	public function testGetBlockAttributeExisting() {
		$this->block->render_block(
			array( 'test_key' => 'test_value' ),
			'',
			$this->createMock( \stdClass::class )
		);

		$result = $this->block->get_block_attribute( 'test_key' );

		$this->assertEquals( 'test_value', $result );
	}

	/**
	 * Test get_block_attribute method with non-existing attribute.
	 */
	public function testGetBlockAttributeNonExisting() {
		$this->block->render_block(
			array(),
			'',
			$this->createMock( \stdClass::class )
		);

		$result = $this->block->get_block_attribute( 'non_existing_key' );

		$this->assertNull( $result );
	}

	/**
	 * Test get_block_context method with existing context.
	 */
	public function testGetBlockContextExisting() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array( 'test_key' => 'test_value' );

		$this->block->render_block( array(), '', $block );

		$result = $this->block->get_block_context( 'test_key' );

		$this->assertEquals( 'test_value', $result );
	}

	/**
	 * Test get_block_context method with non-existing context.
	 */
	public function testGetBlockContextNonExisting() {
		$block          = $this->createMock( \stdClass::class );
		$block->context = array();

		$this->block->render_block( array(), '', $block );

		$result = $this->block->get_block_context( 'non_existing_key' );

		$this->assertNull( $result );
	}

	/**
	 * Test get_block_context method when instance has no context property.
	 */
	public function testGetBlockContextNoContextProperty() {
		$this->block->render_block(
			array(),
			'',
			$this->createMock( \stdClass::class )
		);

		$result = $this->block->get_block_context( 'test_key' );

		$this->assertNull( $result );
	}
}

// phpcs:disable
class TestableBaseBlock extends BaseBlock {
	protected function render() {
		return '<div>Test Block Content</div>';
	}
}
