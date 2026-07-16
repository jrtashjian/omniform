<?php
/**
 * Tests the FieldGroup value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\FieldGroup;
use OmniForm\Form\FieldPath;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the FieldGroup value object.
 */
class FieldGroupTest extends BaseTestCase {
	/**
	 * Stores group metadata including structural choice_group flag.
	 */
	public function testStoresGroupMetadata() {
		$group = new FieldGroup(
			name: FieldPath::from_segments( array( 'rating' ) ),
			label: 'Please rate',
			rules: array( 'required' ),
			choice_group: true,
		);

		$this->assertSame( 'rating', $group->name()->key() );
		$this->assertSame( 'Please rate', $group->label() );
		$this->assertSame( array( 'required' ), $group->rules() );
		$this->assertTrue( $group->has_rule( 'required' ) );
		$this->assertTrue( $group->is_choice_group() );
	}

	/**
	 * Defaults to no rules and not a choice group.
	 */
	public function testDefaults() {
		$group = new FieldGroup(
			name: FieldPath::from_segments( array( 'contact' ) ),
			label: 'Contact',
		);

		$this->assertSame( array(), $group->rules() );
		$this->assertFalse( $group->is_choice_group() );
		$this->assertFalse( $group->has_rule( 'required' ) );
	}

	/**
	 * Rejects empty label.
	 */
	public function testRejectsEmptyLabel() {
		$this->expectException( \InvalidArgumentException::class );

		new FieldGroup( FieldPath::from_segments( array( 'x' ) ), '' );
	}

	/**
	 * Rejects empty path.
	 */
	public function testRejectsEmptyPath() {
		$this->expectException( \InvalidArgumentException::class );

		new FieldGroup( FieldPath::empty(), 'Group' );
	}
}
