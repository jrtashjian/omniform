<?php
/**
 * Tests the FormSchema value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\Field;
use OmniForm\Form\FieldGroup;
use OmniForm\Form\FieldPath;
use OmniForm\Form\FormSchema;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the FormSchema value object.
 */
class FormSchemaTest extends BaseTestCase {
	/**
	 * Preserves order and finds fields by path key.
	 */
	public function testPreservesOrderAndFindsByPath() {
		$fields = array(
			new Field( FieldPath::from_segments( array( 'name' ) ), 'Name', 'text' ),
			new Field( FieldPath::from_segments( array( 'email' ) ), 'Email', 'email', array( 'required' ) ),
		);
		$groups = array(
			new FieldGroup( FieldPath::from_segments( array( 'rating' ) ), 'Rating', array( 'required' ), true ),
		);

		$schema = new FormSchema( $fields, $groups );

		$this->assertSame( $fields, $schema->fields() );
		$this->assertSame( $groups, $schema->groups() );
		$this->assertSame( 'Email', $schema->field( 'email' )->label() );
		$this->assertNull( $schema->field( 'missing' ) );
		$this->assertSame( 'Rating', $schema->group( 'rating' )->label() );
		$this->assertNull( $schema->group( 'missing' ) );
	}

	/**
	 * Defaults to empty collections.
	 */
	public function testDefaultsToEmpty() {
		$schema = new FormSchema();

		$this->assertSame( array(), $schema->fields() );
		$this->assertSame( array(), $schema->groups() );
	}

	/**
	 * Rejects non-Field entries.
	 */
	public function testRejectsInvalidFields() {
		$this->expectException( \InvalidArgumentException::class );

		new FormSchema( array( 'not-a-field' ) );
	}
}
