<?php
/**
 * Tests for Button.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Button;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Button.
 */
class ButtonTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Button
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Button();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturnUsing(
			function ( $attrs ) {
				return 'class="' . $attrs['class'] . '" type="' . $attrs['type'] . '"';
			}
		);
		\WP_Mock::userFunction( 'wp_theme_get_element_class_name' )->andReturn( 'wp-element-button' );

		$result = $this->block->render_block(
			array(
				'buttonLabel' => 'Click Me',
				'buttonType'  => 'submit',
			),
			'',
			$this->createBlockWithContext()
		);
		$this->assertEquals( '<button class="wp-element-button" type="submit">Click Me</button>', $result );

		// Test without label.
		$result = $this->block->render_block( array(), '', $this->createBlockWithContext() );
		$this->assertEquals( '', $result );
	}
}
