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
	 * Data provider for render tests.
	 */
	public function provide_render_scenarios() {
		return array(
			'required_field'     => array(
				array(
					'context'               => array(
						'omniform/fieldLabel'      => 'Field Label',
						'omniform/fieldName'       => 'field_name',
						'omniform/fieldIsRequired' => true,
					),
					'expected_contains'     => array( '<label for="field_name" class="label">', 'Field Label<abbr' ),
					'expected_not_contains' => array(),
					'is_required'           => true,
				),
			),
			'not_required_field' => array(
				array(
					'context'               => array(
						'omniform/fieldLabel'      => 'Field Label',
						'omniform/fieldName'       => 'field_name',
						'omniform/fieldIsRequired' => false,
					),
					'expected_contains'     => array( '<label for="field_name" class="label">', 'Field Label</label>' ),
					'expected_not_contains' => array( '<abbr' ),
					'is_required'           => false,
				),
			),
			'no_label'           => array(
				array(
					'context'         => array(),
					'expected_result' => '',
					'is_required'     => false,
				),
			),
		);
	}

	/**
	 * Test render.
	 *
	 * @dataProvider provide_render_scenarios
	 * @param array $scenario The test scenario data.
	 */
	public function test_render( $scenario ) {
		\WP_Mock::userFunction( 'get_block_wrapper_attributes' )->andReturn( 'class="label"' );

		if ( $scenario['is_required'] ) {
			$mock_form = \Mockery::mock();
			$mock_form->shouldReceive( 'get_required_label' )->andReturn( '*' );
			$mock_app = \Mockery::mock();
			$mock_app->shouldReceive( 'get' )->andReturn( $mock_form );
			\WP_Mock::userFunction( 'omniform' )->andReturn( $mock_app );
		}

		$block  = $this->createBlockWithContext( $scenario['context'] );
		$result = $this->block->render_block( array(), '', $block );

		if ( isset( $scenario['expected_result'] ) ) {
			$this->assertEquals( $scenario['expected_result'], $result );
		} else {
			foreach ( $scenario['expected_contains'] as $str ) {
				$this->assertStringContainsString( $str, $result );
			}
			foreach ( $scenario['expected_not_contains'] as $str ) {
				$this->assertStringNotContainsString( $str, $result );
			}
		}
	}
}
