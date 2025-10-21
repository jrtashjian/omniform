<?php
/**
 * Tests for Label.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use OmniForm\BlockLibrary\Blocks\Label;
use OmniForm\Tests\Unit\BaseBlockTestCase;

/**
 * Tests for Label.
 */
class LabelTest extends BaseBlockTestCase {

	/**
	 * Test block instance.
	 *
	 * @var Label
	 */
	private $block;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();

		\WP_Mock::userFunction( 'sanitize_html_class' )->andReturnUsing(
			function ( $str ) {
				return strtolower( str_replace( ' ', '-', $str ) );
			}
		);
		\WP_Mock::userFunction( 'esc_attr' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);
		\WP_Mock::userFunction( 'wp_kses' )->andReturnUsing(
			function ( $str ) {
				return $str;
			}
		);
		\WP_Mock::userFunction( 'esc_attr__' )->andReturn( 'required' );

		$this->block = new Label();
	}

	/**
	 * Test render.
	 */
	public function test_render() {
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="label"' );

		$block = $this->createBlockWithContext(
			array(
				'omniform/fieldLabel'      => 'Field Label',
				'omniform/fieldName'       => 'field_name',
				'omniform/fieldIsRequired' => true,
			)
		);

		$mock_form = \Mockery::mock();
		$mock_form->shouldReceive( 'get_required_label' )->andReturn( '*' );
		$mock_app = \Mockery::mock();
		$mock_app->shouldReceive( 'get' )->andReturn( $mock_form );
		\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );

		$result = $this->block->render_block(
			array(),
			'',
			$block
		);
		$this->assertStringContainsString( '<label for="field_name" class="label">', $result );
		$this->assertStringContainsString( 'Field Label<abbr', $result );

		// No label.
		$block2 = $this->createBlockWithContext();
		$result = $this->block->render_block( array(), '', $block2 );
		$this->assertEquals( '', $result );
	}
}
