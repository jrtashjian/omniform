<?php
/**
 * Tests the Hidden class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Hidden;

require dirname( dirname( __DIR__ ) ) . '/callback-functions.php';

/**
 * Tests the Hidden class.
 */
class HiddenTest extends FormBlockTestCase {
	/**
	 * The block instance to test against.
	 *
	 * @var \OmniForm\BlockLibrary\Blocks\Hidden
	 */
	protected $block_instance;

	/**
	 * Register the block to test against.
	 */
	public function set_up() {
		$this->register_block_type( new HiddenBlock() );
	}

	/**
	 * Make sure the block does not render markup if the fieldName attribute is empty.
	 */
	public function test_does_not_render_without_field_name() {
		$this->assertEmpty( $this->render_block_with_attributes() );

		$this->assertNotEmpty(
			$this->render_block_with_attributes(
				array(
					'fieldName' => 'field-name',
				)
			)
		);
	}

	/**
	 * Static values should be returned as the input value.
	 */
	public function test_renders_with_static_field_value() {
		$field_name  = 'field-name';
		$field_value = 'field-value';

		$block_rendered = $this->render_block_with_attributes(
			array(
				'fieldName'  => $field_name,
				'fieldValue' => '',
			)
		);

		$this->assertStringContainsString( "name=\"$field_name\"", $block_rendered );
		$this->assertStringNotContainsString( 'value=', $block_rendered );

		$block_rendered = $this->render_block_with_attributes(
			array(
				'fieldName'  => $field_name,
				'fieldValue' => $field_value,
			)
		);

		$this->assertStringContainsString( "name=\"$field_name\"", $block_rendered );
		$this->assertStringContainsString( "value=\"$field_value\"", $block_rendered );
	}

	/**
	 * Ensure the block does not render a value if the callback does not exist.
	 */
	public function test_render_with_nonexistent_callback() {
		$field_name = 'field-name';

		$block_rendered = $this->render_block_with_attributes(
			array(
				'fieldName'  => $field_name,
				'fieldValue' => '{{ nonexistent_callback }}',
			)
		);

		$this->assertStringContainsString( "name=\"$field_name\"", $block_rendered );
		$this->assertStringNotContainsString( 'value=', $block_rendered );
	}

	/**
	 * Ensure the block renders the value returned by the callback.
	 */
	public function test_render_with_callback() {
		$field_name = 'field-name';

		$block_rendered = $this->render_block_with_attributes(
			array(
				'fieldName'  => $field_name,
				'fieldValue' => '{{ omniform_existent_callback_return_string }}',
			)
		);

		$this->assertStringContainsString( "name=\"$field_name\"", $block_rendered );
		$this->assertStringContainsString( 'value="callback string"', $block_rendered );

		// Ensure arrays and objects are not rendered.
		$block_rendered = $this->render_block_with_attributes(
			array(
				'fieldName'  => $field_name,
				'fieldValue' => '{{ omniform_existent_callback_return_array }}',
			)
		);

		$this->assertStringContainsString( "name=\"$field_name\"", $block_rendered );
		$this->assertStringNotContainsString( 'value=', $block_rendered );

		$block_rendered = $this->render_block_with_attributes(
			array(
				'fieldName'  => $field_name,
				'fieldValue' => '{{ omniform_existent_callback_return_object }}',
			)
		);

		$this->assertStringContainsString( "name=\"$field_name\"", $block_rendered );
		$this->assertStringNotContainsString( 'value=', $block_rendered );
	}
}

// phpcs:disable
class HiddenBlock extends Hidden {
	public function block_type_metadata() {
		return 'omniform/' . $this->block_type_name();
	}
}
