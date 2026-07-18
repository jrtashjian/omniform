<?php
/**
 * Tests FormRenderSettings.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin;

use OmniForm\Plugin\FormRenderSettings;
use OmniForm\Tests\Unit\BaseTestCase;
use Mockery;
use WP_Mock;

/**
 * Tests FormRenderSettings.
 */
class FormRenderSettingsTest extends BaseTestCase {
	/**
	 * @var FormRenderSettings
	 */
	private FormRenderSettings $settings;

	/**
	 * Sets up the test environment before each test method is executed.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->settings = new FormRenderSettings();
	}

	/**
	 * Tears down the test environment after each test method is executed.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Password protection delegates to post_password_required.
	 */
	public function testIsPasswordProtected() {
		WP_Mock::userFunction( 'post_password_required' )
			->once()
			->with( 42 )
			->andReturn( true );

		$this->assertTrue( $this->settings->is_password_protected( 42 ) );
	}

	/**
	 * Standard form type always uses POST regardless of meta.
	 */
	public function testSubmitMethodStandardTypeForcesPost() {
		$this->stub_form_type( 10, 'standard' );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 10, 'submit_method', true )
			->andReturn( 'GET' );

		$this->assertSame( 'POST', $this->settings->submit_method( 10 ) );
	}

	/**
	 * Custom form type uses meta when set.
	 */
	public function testSubmitMethodCustomTypeUsesMeta() {
		$this->stub_form_type( 11, 'custom' );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 11, 'submit_method', true )
			->andReturn( 'GET' );

		$this->assertSame( 'GET', $this->settings->submit_method( 11 ) );
	}

	/**
	 * Empty submit method defaults to POST without consulting form type.
	 */
	public function testSubmitMethodEmptyDefaultsToPost() {
		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 12, 'submit_method', true )
			->andReturn( '' );

		$this->assertSame( 'POST', $this->settings->submit_method( 12 ) );
	}

	/**
	 * Standard form action is the REST responses endpoint.
	 */
	public function testSubmitActionStandardTypeUsesRestUrl() {
		$this->stub_form_type( 20, 'standard' );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 20, 'submit_action', true )
			->andReturn( 'https://example.com/elsewhere' );

		WP_Mock::userFunction( 'rest_url' )
			->once()
			->with( 'omniform/v1/forms/20/responses' )
			->andReturn( 'https://example.com/wp-json/omniform/v1/forms/20/responses' );

		$this->assertSame(
			'https://example.com/wp-json/omniform/v1/forms/20/responses',
			$this->settings->submit_action( 20 )
		);
	}

	/**
	 * Custom form type uses meta when set.
	 */
	public function testSubmitActionCustomTypeUsesMeta() {
		$this->stub_form_type( 21, 'custom' );

		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 21, 'submit_action', true )
			->andReturn( 'https://example.com/submit' );

		$this->assertSame( 'https://example.com/submit', $this->settings->submit_action( 21 ) );
	}

	/**
	 * Empty action falls back to REST URL without consulting form type.
	 */
	public function testSubmitActionEmptyUsesRestUrl() {
		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 22, 'submit_action', true )
			->andReturn( '' );

		WP_Mock::userFunction( 'rest_url' )
			->once()
			->with( 'omniform/v1/forms/22/responses' )
			->andReturn( 'https://example.com/wp-json/omniform/v1/forms/22/responses' );

		$this->assertSame(
			'https://example.com/wp-json/omniform/v1/forms/22/responses',
			$this->settings->submit_action( 22 )
		);
	}

	/**
	 * Override wins when non-empty.
	 */
	public function testRequiredLabelOverride() {
		$this->assertSame( '(required)', $this->settings->required_label( 1, '(required)' ) );
	}

	/**
	 * Meta is used when no override.
	 */
	public function testRequiredLabelFromMeta() {
		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 5, 'required_label', true )
			->andReturn( 'req' );

		$this->assertSame( 'req', $this->settings->required_label( 5 ) );
	}

	/**
	 * Empty meta defaults to asterisk.
	 */
	public function testRequiredLabelDefaultsToAsterisk() {
		WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 6, 'required_label', true )
			->andReturn( '' );

		$this->assertSame( '*', $this->settings->required_label( 6 ) );
	}

	/**
	 * Content-only forms with no override default to asterisk.
	 */
	public function testRequiredLabelWithoutFormId() {
		$this->assertSame( '*', $this->settings->required_label() );
	}

	/**
	 * @param int    $form_id Form ID.
	 * @param string $slug    Type slug.
	 */
	private function stub_form_type( int $form_id, string $slug ): void {
		$term       = new \stdClass();
		$term->slug = $slug;

		WP_Mock::userFunction( 'get_the_terms' )
			->once()
			->with( $form_id, 'omniform_type' )
			->andReturn( array( $term ) );

		WP_Mock::userFunction( 'is_wp_error' )
			->once()
			->andReturn( false );
	}
}
