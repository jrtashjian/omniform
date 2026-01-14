<?php
/**
 * Tests the ConditionalGroup block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\ConditionalGroup;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

// Include callback functions for testing.
require_once __DIR__ . '/../../../../unit/Traits/callback-functions.php';

/**
 * Tests the ConditionalGroup block.
 */
class ConditionalGroupTest extends BaseTestCase {

	/**
	 * The ConditionalGroup instance.
	 *
	 * @var ConditionalGroup
	 */
	private $block;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new ConditionalGroup();
	}

	/**
	 * Test render returns content when no callback attribute.
	 */
	public function testRenderReturnsContentWhenNoCallback() {
		$result = $this->block->render_block(
			array(),
			'<p>Test content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '<p>Test content</p>', $result );
	}

	/**
	 * Test render returns content when callback attribute is empty.
	 */
	public function testRenderReturnsContentWhenCallbackEmpty() {
		$result = $this->block->render_block(
			array( 'callback' => '' ),
			'<p>Test content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '<p>Test content</p>', $result );
	}

	/**
	 * Test render returns content when has callback and condition met (non-empty result, reverse false).
	 */
	public function testRenderReturnsContentWhenConditionMetNonEmptyReverseFalse() {
		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( 'callback string' ),
				'return' => 'callback string',
			)
		);

		$result = $this->block->render_block(
			array(
				'callback'         => '{{ omniform_existent_callback_return_string }}',
				'reverseCondition' => false,
			),
			'<p>Test content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '<p>Test content</p>', $result );
	}

	/**
	 * Test render returns empty when has callback and condition not met (non-empty result, reverse true).
	 */
	public function testRenderReturnsEmptyWhenConditionNotMetNonEmptyReverseTrue() {
		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( 'callback string' ),
				'return' => 'callback string',
			)
		);

		$result = $this->block->render_block(
			array(
				'callback'         => '{{ omniform_existent_callback_return_string }}',
				'reverseCondition' => true,
			),
			'<p>Test content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '', $result );
	}

	/**
	 * Test render returns empty when has callback and condition not met (empty result, reverse false).
	 */
	public function testRenderReturnsEmptyWhenConditionNotMetEmptyReverseFalse() {
		$result = $this->block->render_block(
			array(
				'callback'         => '{{ omniform_existent_callback_return_array }}',
				'reverseCondition' => false,
			),
			'<p>Test content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '', $result );
	}

	/**
	 * Test render returns content when has callback and condition met (empty result, reverse true).
	 */
	public function testRenderReturnsContentWhenConditionMetEmptyReverseTrue() {
		$result = $this->block->render_block(
			array(
				'callback'         => '{{ omniform_existent_callback_return_array }}',
				'reverseCondition' => true,
			),
			'<p>Test content</p>',
			$this->createMock( \stdClass::class )
		);

		$this->assertEquals( '<p>Test content</p>', $result );
	}
}
