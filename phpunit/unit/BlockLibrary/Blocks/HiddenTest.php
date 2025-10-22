<?php
/**
 * Tests for Hidden.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Hidden;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Hidden.
 */
class HiddenTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Hidden
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Hidden();
	}

	/**
	 * Test get_field_label.
	 */
	public function test_get_field_label() {
		$this->block->render_block( array( 'fieldName' => 'hidden_field' ), '', $this->createBlockWithContext() );
		$this->assertEquals( 'hidden_field', $this->block->get_field_label() );
	}

	/**
	 * Test get_extra_wrapper_attributes.
	 */
	public function test_get_extra_wrapper_attributes() {
		$this->block->render_block(
			array(
				'fieldName'  => 'hidden_field',
				'fieldValue' => 'value',
			),
			'',
			$this->createBlockWithContext()
		);
		$result = $this->block->get_extra_wrapper_attributes();
		$this->assertEquals(
			array(
				'type'  => 'hidden',
				'id'    => 'hidden_field',
				'name'  => 'hidden_field',
				'value' => 'value',
			),
			$result
		);
	}
}
