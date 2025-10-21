<?php
/**
 * Tests for Fieldset.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Fieldset;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Fieldset.
 */
class FieldsetTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Fieldset
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Fieldset();
	}



	/**
	 * Test get_field_group_label.
	 */
	public function test_get_field_group_label() {
		$this->block->render_block( array( 'fieldLabel' => 'Label' ), '', $this->createBlockWithContext() );
		$this->assertEquals( 'Label', $this->block->get_field_group_label() );
	}

	/**
	 * Test get_field_group_name.
	 */
	public function test_get_field_group_name() {
		$this->block->render_block( array( 'fieldName' => 'group name' ), '', $this->createBlockWithContext() );
		$this->assertEquals( 'group-name', $this->block->get_field_group_name() );
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="fieldset"' );
		\WP_Mock::userFunction( 'esc_attr__' )->andReturn( 'required' );

		$result = $this->block->render_block(
			array(
				'fieldLabel' => 'Group Label',
				'isRequired' => false,
			),
			'inner content',
			$this->createBlockWithContext()
		);
		$this->assertStringContainsString( '<fieldset class="fieldset"><legend>Group Label</legend>', $result );
		$this->assertStringContainsString( 'inner content</fieldset>', $result );

		// No label.
		$result = $this->block->render_block( array(), '', $this->createBlockWithContext() );
		$this->assertEquals( '', $result );
	}

	/**
	 * Test render with required field using asterisk.
	 */
	public function test_render_required_with_asterisk() {
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="fieldset"' );
		\WP_Mock::userFunction( 'esc_attr__' )->andReturn( 'required' );

		$mock_form = \Mockery::mock();
		$mock_form->shouldReceive( 'get_required_label' )->andReturn( '*' );
		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'get' )->andReturn( $mock_form );
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );

		$result = $this->block->render_block(
			array(
				'fieldLabel' => 'Group Label',
				'isRequired' => true,
			),
			'inner content',
			$this->createBlockWithContext()
		);
		$this->assertStringContainsString( '<fieldset class="fieldset"><legend>Group Label<abbr class="omniform-field-required" title="required">*</abbr></legend>', $result );
		$this->assertStringContainsString( '<div class="omniform-field-label" aria-hidden="true">Group Label<abbr class="omniform-field-required" title="required">*</abbr></div>', $result );
		$this->assertStringContainsString( 'inner content</fieldset>', $result );
	}

	/**
	 * Test render with required field using custom label.
	 */
	public function test_render_required_with_custom_label() {
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="fieldset"' );

		$mock_form = \Mockery::mock();
		$mock_form->shouldReceive( 'get_required_label' )->andReturn( '(required)' );
		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'get' )->andReturn( $mock_form );
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );

		$result = $this->block->render_block(
			array(
				'fieldLabel' => 'Group Label',
				'isRequired' => true,
			),
			'inner content',
			$this->createBlockWithContext()
		);
		$this->assertStringContainsString( '<fieldset class="fieldset"><legend>Group Label<span class="omniform-field-required">(required)</span></legend>', $result );
		$this->assertStringContainsString( '<div class="omniform-field-label" aria-hidden="true">Group Label<span class="omniform-field-required">(required)</span></div>', $result );
		$this->assertStringContainsString( 'inner content</fieldset>', $result );
	}
}
