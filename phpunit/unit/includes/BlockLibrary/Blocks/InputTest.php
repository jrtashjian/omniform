<?php
/**
 * Tests the Input block.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\BlockLibrary\Blocks;

use Mockery;
use OmniForm\BlockLibrary\Blocks\Input;
use OmniForm\Dependencies\Respect\Validation\Rules\Optional as ValidationOptional;
use OmniForm\Dependencies\Respect\Validation\Rules\Email as ValidationEmail;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the Input block.
 */
class InputTest extends BaseTestCase {

	/**
	 * The Input instance.
	 *
	 * @var Input
	 */
	private $block;

	/**
	 * The WP_Block mock instance.
	 *
	 * @var \WP_Block|Mockery\MockInterface
	 */
	private $wp_block_mock;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->block = new Input();

		$this->wp_block_mock          = $this->createMock( \stdClass::class );
		$this->wp_block_mock->context = array( 'omniform/fieldLabel' => 'Test Label' );

		// Mock WP_Block_Supports static method using Mockery.
		$block_supports_mock = Mockery::mock( 'alias:WP_Block_Supports' );
		$block_supports_mock->shouldReceive( 'get_instance' )
			->andReturnSelf();
		$block_supports_mock->shouldReceive( 'apply_block_supports' )
			->andReturn( array() );
	}

	/**
	 * Test render_block returns input with default type.
	 */
	public function testRenderBlockDefault() {
		$result = $this->block->render_block(
			array(),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<input id="Test-Label" name="Test-Label" type="text" aria-label="Test Label" />', $result );
	}

	/**
	 * Test render_block returns input with email type.
	 */
	public function testRenderBlockEmail() {
		$result = $this->block->render_block(
			array( 'fieldType' => 'email' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<input id="Test-Label" name="Test-Label" type="email" aria-label="Test Label" />', $result );
	}

	/**
	 * Test render_block returns input with text type for username-email.
	 */
	public function testRenderBlockUsernameEmail() {
		$result = $this->block->render_block(
			array( 'fieldType' => 'username-email' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<input id="Test-Label" name="Test-Label" type="text" aria-label="Test Label" />', $result );
	}

	/**
	 * Test render_block returns input with value for checkbox.
	 */
	public function testRenderBlockCheckbox() {
		$result = $this->block->render_block(
			array( 'fieldType' => 'checkbox' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<input id="Test-Label" name="Test-Label" value="Test Label" type="checkbox" aria-label="Test Label" />', $result );
	}

	/**
	 * Test render_block returns input with value for date.
	 */
	public function testRenderBlockDate() {
		$value = gmdate( Input::FORMAT_DATE );

		$result = $this->block->render_block(
			array( 'fieldType' => 'date' ),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<input id="Test-Label" name="Test-Label" value="' . $value . '" type="date" aria-label="Test Label" />', $result );
	}

	/**
	 * Test render_block returns input with placeholder.
	 */
	public function testRenderBlockPlaceholder() {
		$result = $this->block->render_block(
			array(
				'fieldType'        => 'text',
				'fieldPlaceholder' => 'Enter text',
			),
			'',
			$this->wp_block_mock
		);

		$this->assertEquals( '<input id="Test-Label" name="Test-Label" type="text" aria-label="Test Label" placeholder="Enter text" />', $result );
	}

	/**
	 * Test get_validation_rules includes Email rule when not required.
	 */
	public function testGetValidationRulesEmail() {
		$this->block->render_block(
			array( 'fieldType' => 'email' ),
			'',
			$this->wp_block_mock
		);

		$rules = $this->block->get_validation_rules();

		$this->assertCount( 1, $rules );
		$this->assertInstanceOf( ValidationOptional::class, $rules[0] );
	}



	/**
	 * Test get_validation_rules includes UsernameOrEmailRule for username-email type.
	 */
	public function testGetValidationRulesUsernameEmail() {
		$this->block->render_block(
			array( 'fieldType' => 'username-email' ),
			'',
			$this->wp_block_mock
		);

		$rules = $this->block->get_validation_rules();

		$this->assertCount( 1, $rules );
		$this->assertInstanceOf( ValidationOptional::class, $rules[0] );
	}

	/**
	 * Test get_validation_rules includes Email rule directly when required.
	 */
	public function testGetValidationRulesEmailRequired() {
		$this->wp_block_mock->context['omniform/fieldIsRequired'] = true;

		$this->block->render_block(
			array( 'fieldType' => 'email' ),
			'',
			$this->wp_block_mock
		);

		$rules = $this->block->get_validation_rules();

		$rule_classes = array_map( 'get_class', $rules );
		$this->assertContains( ValidationEmail::class, $rule_classes );
		$this->assertNotContains( ValidationOptional::class, $rule_classes );
	}

	/**
	 * Test get_control_name returns name with [] for grouped checkbox.
	 */
	public function testGetControlNameCheckboxGrouped() {
		$this->wp_block_mock->context = array(
			'omniform/fieldLabel'     => 'Test Label',
			'omniform/fieldGroupName' => 'Test Group',
		);

		$this->block->render_block(
			array( 'fieldType' => 'checkbox' ),
			'',
			$this->wp_block_mock
		);

		$result = $this->block->get_control_name();

		$this->assertEquals( 'Test-Group[]', $result );
	}
}
