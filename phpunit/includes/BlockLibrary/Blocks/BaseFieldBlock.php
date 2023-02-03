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
	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$block_object = new BaseFieldBlockStub();

		register_block_type(
			$block_object->blockTypeMetadata(),
			array(
				'render_callback' => array( $block_object, 'renderBlock' ),
				'uses_context'    => array( 'omniform/fieldGroupName' ),
			)
		);

		omniform()->addServiceProvider( new \OmniForm\Plugin\PluginServiceProvider() );
	}

	/**
	 * Helper method to render a block comment delimeter.
	 *
	 * @param array $attributes The block attributes.
	 */
	private function render_block_with_attributes( $attributes ) {
		return do_blocks(
			sprintf(
				'<!-- wp:omniform/base-field-block-stub %s -->',
				wp_json_encode( $attributes )
			)
		);
	}

	/**
	 * Make sure the block does not render markup if the fieldLabel attribute is empty.
	 */
	public function test_does_not_render_without_field_label() {
		$this->assertEmpty(
			$this->render_block_with_attributes( array( 'fieldLabel' => '' ) )
		);

		$block = $this->render_block_with_attributes( array( 'fieldLabel' => 'test label' ) );

		$this->assertNotEmpty( $block );
		$this->assertEquals( '<div class="omniform-base-field-block-stub wp-block-omniform-base-field-block-stub"><label class="omniform-field-label " for="test-label">test label</label><div id="test-label" name="test-label" /></div>', $block );
	}

	/**
	 * Make sure the "field-required" class is included in the markup when isRequired is true.
	 */
	public function test_field_is_required() {
		$block = $this->render_block_with_attributes(
			array(
				'fieldLabel' => 'test label',
				'isRequired' => true,
			)
		);

		$this->assertStringContainsString( 'field-required', $block );
	}

	/**
	 * Ensure the field is submitted as a child of the fieldset group.
	 */
	public function test_field_is_group() {
		add_filter(
			'render_block_context',
			function( $context ) {
				$context['omniform/fieldGroupName'] = 'test-group';
				return $context;
			}
		);

		$block = $this->render_block_with_attributes( array( 'fieldLabel' => 'test label' ) );

		$this->assertStringContainsString( 'name="test-group[test-label]"', $block );
	}

	/**
	 * Make sure the custom value is included in the markup when fieldValue is set.
	 */
	public function test_control_value() {
		$block = $this->render_block_with_attributes(
			array(
				'fieldLabel' => 'test label',
				'fieldValue' => 'test value',
			)
		);

		$this->assertStringContainsString( 'value="test value"', $block );
	}
}

// phpcs:disable
class BaseFieldBlockStub extends BaseFieldBlock {
	public function blockTypeMetadata() {
		return 'omniform/' . $this->blockTypeName();
	}

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
