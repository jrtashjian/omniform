<?php
/**
 * Tests for SelectGroup.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\SelectGroup;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for SelectGroup.
 */
class SelectGroupTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var SelectGroup
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new SelectGroup();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		$result = $this->block->render_block( array( 'fieldLabel' => 'Group' ), '<option>1</option>', $this->createBlockWithContext() );
		$this->assertEquals( '<optgroup label="Group"><option>1</option></optgroup>', $result );

		$result = $this->block->render_block( array(), '', $this->createBlockWithContext() );
		$this->assertEquals( '', $result );
	}
}
