<?php
/**
 * Tests the Field block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Field;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the Field block.
 */
class FieldTest extends BaseTestCase {

	/**
	 * The Field instance.
	 *
	 * @var Field
	 */
	private $block;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Field();
	}

	/**
	 * Test render returns empty string when no fieldLabel attribute.
	 */
	public function testRenderEmptyWhenNoFieldLabel() {
		$result = $this->block->render_block(
			array(),
			'<p>Field content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '', $result );
	}

	/**
	 * Test render returns div with content when fieldLabel attribute present.
	 */
	public function testRenderWithFieldLabel() {
		$result = $this->block->render_block(
			array( 'fieldLabel' => 'Test Field' ),
			'<p>Field content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '<div class="wp-block-omniform-field-is-layout-flex"><p>Field content</p></div>', $result );
	}
}
