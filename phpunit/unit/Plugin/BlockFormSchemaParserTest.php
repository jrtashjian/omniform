<?php
/**
 * Tests the BlockFormSchemaParser.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\BlockFormSchemaParser;
use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;

/**
 * Tests the BlockFormSchemaParser.
 */
class BlockFormSchemaParserTest extends BaseTestCase {
	/**
	 * @var BlockFormSchemaParser
	 */
	private BlockFormSchemaParser $parser;

	/**
	 * Sets up the test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->parser = new BlockFormSchemaParser();
	}

	/**
	 * Parses simple fields and textareas.
	 */
	public function testParsesSimpleFieldAndTextarea() {
		$content = 'simple-fields';

		WP_Mock::userFunction( 'parse_blocks' )
			->once()
			->with( $content )
			->andReturn(
				array(
					array(
						'blockName'   => 'omniform/field',
						'attrs'       => array(
							'fieldLabel' => 'Email',
							'fieldName'  => 'email',
							'isRequired' => true,
						),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/input',
								'attrs'       => array( 'fieldType' => 'email' ),
								'innerBlocks' => array(),
							),
						),
					),
					array(
						'blockName'   => 'omniform/field',
						'attrs'       => array( 'fieldLabel' => 'Message' ),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/textarea',
								'attrs'       => array(),
								'innerBlocks' => array(),
							),
						),
					),
				)
			);

		$schema = $this->parser->parse( $content );
		$fields = $schema->fields();

		$this->assertCount( 2, $fields );
		$this->assertSame( 'email', $fields[0]->name()->key() );
		$this->assertSame( 'Email', $fields[0]->label() );
		$this->assertSame( 'email', $fields[0]->type() );
		$this->assertTrue( $fields[0]->has_rule( 'required' ) );
		$this->assertSame( 'Message', $fields[1]->name()->key() );
		$this->assertSame( 'textarea', $fields[1]->type() );
		$this->assertFalse( $fields[1]->has_rule( 'required' ) );
	}

	/**
	 * Nested fieldset fields get composed paths.
	 */
	public function testParsesNestedFieldsetFields() {
		$content = 'fieldset-nested';

		WP_Mock::userFunction( 'parse_blocks' )
			->once()
			->with( $content )
			->andReturn(
				array(
					array(
						'blockName'   => 'omniform/fieldset',
						'attrs'       => array(
							'fieldLabel' => 'Your Name',
							'fieldName'  => 'name',
						),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/field',
								'attrs'       => array(
									'fieldLabel' => 'First',
									'fieldName'  => 'first',
								),
								'innerBlocks' => array(
									array(
										'blockName'   => 'omniform/input',
										'attrs'       => array( 'fieldType' => 'text' ),
										'innerBlocks' => array(),
									),
								),
							),
						),
					),
				)
			);

		$schema = $this->parser->parse( $content );

		$this->assertCount( 1, $schema->groups() );
		$this->assertSame( 'name', $schema->groups()[0]->name()->key() );
		$this->assertFalse( $schema->groups()[0]->is_choice_group() );
		$this->assertSame( 'name.first', $schema->fields()[0]->name()->key() );
		$this->assertSame( 'First', $schema->fields()[0]->label() );
	}

	/**
	 * Radio choice groups emit one field at the group path.
	 */
	public function testParsesRadioChoiceGroupAsSingleField() {
		$content = 'choice-group';

		WP_Mock::userFunction( 'parse_blocks' )
			->once()
			->with( $content )
			->andReturn(
				array(
					array(
						'blockName'   => 'omniform/fieldset',
						'attrs'       => array(
							'fieldLabel' => 'Attending?',
							'isRequired' => true,
						),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/field',
								'attrs'       => array( 'fieldLabel' => 'Yes' ),
								'innerBlocks' => array(
									array(
										'blockName'   => 'omniform/input',
										'attrs'       => array( 'fieldType' => 'radio' ),
										'innerBlocks' => array(),
									),
								),
							),
							array(
								'blockName'   => 'omniform/field',
								'attrs'       => array( 'fieldLabel' => 'No' ),
								'innerBlocks' => array(
									array(
										'blockName'   => 'omniform/input',
										'attrs'       => array( 'fieldType' => 'radio' ),
										'innerBlocks' => array(),
									),
								),
							),
						),
					),
				)
			);

		$schema = $this->parser->parse( $content );

		$this->assertCount( 1, $schema->fields() );
		$this->assertSame( 'Attending', $schema->fields()[0]->name()->key() );
		$this->assertSame( 'radio', $schema->fields()[0]->type() );
		$this->assertTrue( $schema->fields()[0]->has_rule( 'required' ) );
		$this->assertTrue( $schema->groups()[0]->is_choice_group() );
		$this->assertTrue( $schema->groups()[0]->has_rule( 'required' ) );
	}

	/**
	 * Mixed field types under a fieldset are not a choice group.
	 */
	public function testMixedFieldsetIsNotChoiceGroup() {
		$content = 'mixed-fieldset';

		WP_Mock::userFunction( 'parse_blocks' )
			->once()
			->with( $content )
			->andReturn(
				array(
					array(
						'blockName'   => 'omniform/fieldset',
						'attrs'       => array(
							'fieldLabel' => 'Details',
							'fieldName'  => 'details',
						),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/field',
								'attrs'       => array( 'fieldLabel' => 'Name', 'fieldName' => 'name' ),
								'innerBlocks' => array(
									array(
										'blockName'   => 'omniform/input',
										'attrs'       => array( 'fieldType' => 'text' ),
										'innerBlocks' => array(),
									),
								),
							),
							array(
								'blockName'   => 'omniform/field',
								'attrs'       => array( 'fieldLabel' => 'Agree', 'fieldName' => 'agree' ),
								'innerBlocks' => array(
									array(
										'blockName'   => 'omniform/input',
										'attrs'       => array( 'fieldType' => 'checkbox' ),
										'innerBlocks' => array(),
									),
								),
							),
						),
					),
				)
			);

		$schema = $this->parser->parse( $content );

		$this->assertFalse( $schema->groups()[0]->is_choice_group() );
		$this->assertCount( 2, $schema->fields() );
		$this->assertSame( 'details.name', $schema->fields()[0]->name()->key() );
		$this->assertSame( 'details.agree', $schema->fields()[1]->name()->key() );
	}

	/**
	 * Lone checkboxes outside choice groups can be required.
	 */
	public function testParsesRequiredLoneCheckbox() {
		$content = 'lone-checkbox';

		WP_Mock::userFunction( 'parse_blocks' )
			->once()
			->with( $content )
			->andReturn(
				array(
					array(
						'blockName'   => 'omniform/field',
						'attrs'       => array(
							'fieldLabel' => 'I agree',
							'fieldName'  => 'agree',
							'isRequired' => true,
						),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/input',
								'attrs'       => array( 'fieldType' => 'checkbox' ),
								'innerBlocks' => array(),
							),
						),
					),
				)
			);

		$field = $this->parser->parse( $content )->fields()[0];

		$this->assertSame( 'agree', $field->name()->key() );
		$this->assertSame( 'checkbox', $field->type() );
		$this->assertTrue( $field->has_rule( 'required' ) );
	}

	/**
	 * Hidden, select, file type, and wrapper blocks.
	 */
	public function testParsesHiddenSelectFileAndWrappers() {
		$content = 'mixed';

		WP_Mock::userFunction( 'parse_blocks' )
			->once()
			->with( $content )
			->andReturn(
				array(
					array(
						'blockName'   => 'omniform/hidden',
						'attrs'       => array( 'fieldName' => 'source' ),
						'innerBlocks' => array(),
					),
					array(
						'blockName'   => 'omniform/field',
						'attrs'       => array(
							'fieldLabel' => 'Topic',
							'fieldName'  => 'topic',
						),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/select',
								'attrs'       => array(),
								'innerBlocks' => array(),
							),
						),
					),
					array(
						'blockName'   => 'omniform/field',
						'attrs'       => array(
							'fieldLabel' => 'Resume',
							'fieldName'  => 'resume',
						),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/input',
								'attrs'       => array( 'fieldType' => 'file' ),
								'innerBlocks' => array(),
							),
						),
					),
					array(
						'blockName'   => 'core/group',
						'attrs'       => array(),
						'innerBlocks' => array(
							array(
								'blockName'   => 'omniform/field',
								'attrs'       => array(
									'fieldLabel' => 'Nested',
									'fieldName'  => 'nested',
								),
								'innerBlocks' => array(
									array(
										'blockName'   => 'omniform/input',
										'attrs'       => array(),
										'innerBlocks' => array(),
									),
								),
							),
						),
					),
				)
			);

		$schema  = $this->parser->parse( $content );
		$by_path = array();

		foreach ( $schema->fields() as $field ) {
			$by_path[ $field->name()->key() ] = $field;
		}

		$this->assertSame( 'hidden', $by_path['source']->type() );
		$this->assertSame( 'select', $by_path['topic']->type() );
		$this->assertSame( 'file', $by_path['resume']->type() );
		$this->assertSame( 'text', $by_path['nested']->type() );
	}
}
