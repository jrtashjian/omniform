<?php
/**
 * Tests the Field value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\Field;
use OmniForm\Form\FieldPath;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the Field value object.
 */
class FieldTest extends BaseTestCase {
	/**
	 * Stores composed path, label, type, and rules.
	 */
	public function testStoresNameLabelTypeAndRules() {
		$field = new Field(
			name: FieldPath::from_segments( array( 'contact', 'email' ) ),
			label: 'Email',
			type: 'email',
			rules: array( 'required', 'email' ),
		);

		$this->assertSame( 'contact.email', $field->name()->key() );
		$this->assertSame( 'Email', $field->label() );
		$this->assertSame( 'email', $field->type() );
		$this->assertSame( array( 'required', 'email' ), $field->rules() );
		$this->assertTrue( $field->has_rule( 'required' ) );
		$this->assertTrue( $field->has_rule( 'email' ) );
		$this->assertFalse( $field->has_rule( 'url' ) );
	}

	/**
	 * Parameterized rules match by rule name.
	 */
	public function testHasRuleMatchesParameterizedRules() {
		$field = new Field(
			name: FieldPath::from_segments( array( 'age' ) ),
			label: 'Age',
			type: 'number',
			rules: array( 'min:18', 'max:120' ),
		);

		$this->assertTrue( $field->has_rule( 'min' ) );
		$this->assertTrue( $field->has_rule( 'max' ) );
		$this->assertFalse( $field->has_rule( 'required' ) );
	}

	/**
	 * Defaults to no rules.
	 */
	public function testDefaultsToEmptyRules() {
		$field = new Field(
			name: FieldPath::from_segments( array( 'name' ) ),
			label: 'Name',
			type: 'text',
		);

		$this->assertSame( array(), $field->rules() );
		$this->assertFalse( $field->has_rule( 'required' ) );
	}

	/**
	 * Rejects empty type.
	 */
	public function testRejectsEmptyType() {
		$this->expectException( \InvalidArgumentException::class );

		new Field( FieldPath::from_segments( array( 'x' ) ), 'X', '', array() );
	}

	/**
	 * Rejects empty label.
	 */
	public function testRejectsEmptyLabel() {
		$this->expectException( \InvalidArgumentException::class );

		new Field( FieldPath::from_segments( array( 'x' ) ), '', 'text', array() );
	}

	/**
	 * Rejects empty path.
	 */
	public function testRejectsEmptyPath() {
		$this->expectException( \InvalidArgumentException::class );

		new Field( FieldPath::empty(), 'X', 'text', array() );
	}

	/**
	 * Rejects empty rule strings.
	 */
	public function testRejectsEmptyRuleStrings() {
		$this->expectException( \InvalidArgumentException::class );

		new Field(
			FieldPath::from_segments( array( 'x' ) ),
			'X',
			'text',
			array( 'required', '' ),
		);
	}
}
