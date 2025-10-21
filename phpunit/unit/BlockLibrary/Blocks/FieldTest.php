<?php
/**
 * Tests for Field.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Field;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Field.
 */
class FieldTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Field
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Field();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturnUsing(
			function ( $attrs ) {
				return 'class="' . ( $attrs['class'] ?? '' ) . '"';
			}
		);

		$result = $this->block->render_block( array( 'fieldLabel' => 'Field Label' ), 'inner content', $this->createBlockWithContext() );
		$this->assertStringContainsString( '<div class="wp-block-omniform-field-is-layout-flex">inner content</div>', $result );

		// No label.
		$result = $this->block->render_block( array(), '', $this->createBlockWithContext() );
		$this->assertEquals( '', $result );
	}
}
