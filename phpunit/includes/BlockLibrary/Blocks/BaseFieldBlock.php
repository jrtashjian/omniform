<?php
/**
 * Tests the BaseFieldBlock class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\BaseFieldBlock;

/**
 * Tests the BaseFieldBlock class.
 */
class BaseFieldBlockTest extends \WP_UnitTestCase {
	public function test_does_not_render_without_field_label() {
		$this->assertEmpty(
			( new BaseFieldBlockStub() )->renderBlock( array(), '', (object) array() )
		);

		$block  = new BaseFieldBlockStub();
		$render = $block->renderBlock( array( 'fieldLabel' => 'test label' ), '', (object) array() );

		$this->assertNotEmpty( $render );
		$this->assertEquals( '<div class="wp-block-omniform-base-field-block-stub omniform-base-field-block-stub" style=""><label class="omniform-field-label" for="test-label">test label</label><div id="test-label" name="test-label" /></div>', $render );
		$this->assertStringContainsString( 'id="test-label"', $block->renderControl() );
		$this->assertStringContainsString( 'name="test-label"', $block->renderControl() );
	}

	public function test_field_is_required() {
		$block = ( new BaseFieldBlockStub() )->renderBlock(
			array(
				'fieldLabel' => 'test label',
				'isRequired' => true,
			),
			'',
			(object) array()
		);

		$this->assertStringContainsString( 'field-required', $block );
	}

	public function test_field_is_group() {
		$block = ( new BaseFieldBlockStub() )->renderBlock(
			array( 'fieldLabel' => 'test label' ),
			'',
			(object) array( 'context' => array( 'omniform/fieldGroupName' => 'test-group' ) )
		);

		$this->assertStringContainsString( 'name="test-group[test-label]"', $block );
	}

	public function test_control_value() {
		$block = ( new BaseFieldBlockStub() )->renderBlock(
			array(
				'fieldLabel' => 'test label',
				'fieldValue' => 'test value',
			),
			'',
			(object) array()
		);

		$this->assertStringContainsString( 'value="test value"', $block );
	}
}

// phpcs:disable
class BaseFieldBlockStub extends BaseFieldBlock {
	public function renderControl() {
		$attributes = array_merge(
			$this->getControlAttributes(),
			array(
				$this->getElementAttribute( 'value', $this->getControlValue() ),
			),
		);

		return sprintf(
			'<div %s />',
			trim( implode( ' ', $attributes ) )
		);
	}
}
