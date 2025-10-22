<?php
/**
 * Tests for ConditionalGroup.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\ConditionalGroup;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for ConditionalGroup.
 */
class ConditionalGroupTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var ConditionalGroup
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new ConditionalGroup();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		$content = 'inner content';

		// No callback.
		$result = $this->block->render_block( array(), $content, $this->createBlockWithContext() );
		$this->assertEquals( $content, $result );

		// Callback that returns truthy.
		\WP_Mock::userFunction( 'truthy_callback' )->andReturn( 'yes' );
		$result = $this->block->render_block(
			array(
				'callback'         => '{{ truthy_callback }}',
				'reverseCondition' => false,
			),
			$content,
			$this->createBlockWithContext()
		);
		$this->assertEquals( $content, $result );

		$result = $this->block->render_block(
			array(
				'callback'         => '{{ truthy_callback }}',
				'reverseCondition' => true,
			),
			$content,
			$this->createBlockWithContext()
		);
		$this->assertEquals( '', $result );

		// Callback that returns falsy.
		\WP_Mock::userFunction( 'falsy_callback' )->andReturn( '' );
		$result = $this->block->render_block(
			array(
				'callback'         => '{{ falsy_callback }}',
				'reverseCondition' => false,
			),
			$content,
			$this->createBlockWithContext()
		);
		$this->assertEquals( '', $result );

		$result = $this->block->render_block(
			array(
				'callback'         => '{{ falsy_callback }}',
				'reverseCondition' => true,
			),
			$content,
			$this->createBlockWithContext()
		);
		$this->assertEquals( $content, $result );
	}
}
