<?php
/**
 * Tests the ValidationRules value object.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\ValidationRules;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests the ValidationRules value object.
 */
class ValidationRulesTest extends BaseTestCase {
	/**
	 * Stores rule strings and reports membership by name.
	 */
	public function testStoresRulesAndReportsMembership() {
		$rules = new ValidationRules( array( 'required', 'email', 'min:3' ) );

		$this->assertSame( array( 'required', 'email', 'min:3' ), $rules->all() );
		$this->assertTrue( $rules->has( 'required' ) );
		$this->assertTrue( $rules->has( 'email' ) );
		$this->assertTrue( $rules->has( 'min' ) );
		$this->assertFalse( $rules->has( 'url' ) );
	}

	/**
	 * Defaults to an empty list.
	 */
	public function testDefaultsToEmpty() {
		$rules = new ValidationRules();

		$this->assertSame( array(), $rules->all() );
		$this->assertFalse( $rules->has( 'required' ) );
	}

	/**
	 * Rejects empty rule strings.
	 */
	public function testRejectsEmptyRuleStrings() {
		$this->expectException( \InvalidArgumentException::class );

		new ValidationRules( array( 'required', '' ) );
	}

	/**
	 * Rejects non-string rules.
	 */
	public function testRejectsNonStringRules() {
		$this->expectException( \InvalidArgumentException::class );

		new ValidationRules( array( 'required', 1 ) );
	}
}
