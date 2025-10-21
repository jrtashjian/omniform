<?php
/**
 * Tests for SelectOption.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\SelectOption;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for SelectOption.
 */
class SelectOptionTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var SelectOption
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new SelectOption();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		$result = $this->block->render_block( array( 'fieldLabel' => 'Option 1' ), '', $this->createBlockWithContext() );
		$this->assertEquals( '<option value="Option 1">Option 1</option>', $result );

		$result = $this->block->render_block( array(), '', $this->createBlockWithContext() );
		$this->assertEquals( '', $result );
	}
}
