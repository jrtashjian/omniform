<?php
/**
 * Tests the Functions.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin\Support;

use OmniForm\Tests\Unit\BaseTestCase;
use WP_Mock;
use Mockery;

/**
 * Tests the Functions.
 */
class FunctionsTest extends BaseTestCase {

	/**
	 * Set up the commenter mock.
	 */
	private function setUpCommenterMock() {
		WP_Mock::userFunction( 'wp_get_current_commenter' )
			->once()
			->andReturn(
				array(
					'comment_author'       => 'John Doe',
					'comment_author_email' => 'john@example.com',
					'comment_author_url'   => 'http://example.com',
				)
			);
	}

	/**
	 * Test omniform_current_commenter_author returns the comment author.
	 */
	public function testOmniformCurrentCommenterAuthor() {
		$this->setUpCommenterMock();

		$result = omniform_current_commenter_author();

		$this->assertEquals( 'John Doe', $result );
	}

	/**
	 * Test omniform_current_commenter_email returns the comment author email.
	 */
	public function testOmniformCurrentCommenterEmail() {
		$this->setUpCommenterMock();

		$result = omniform_current_commenter_email();

		$this->assertEquals( 'john@example.com', $result );
	}

	/**
	 * Test omniform_current_commenter_url returns the comment author URL.
	 */
	public function testOmniformCurrentCommenterUrl() {
		$this->setUpCommenterMock();

		$result = omniform_current_commenter_url();

		$this->assertEquals( 'http://example.com', $result );
	}

	/**
	 * Set up mocks for omniform_comment_login_required.
	 *
	 * @param bool      $comments_open Whether comments are open.
	 * @param bool|null $registration Whether registration is required (null if not checked).
	 * @param bool|null $logged_in Whether user is logged in (null if not checked).
	 */
	private function setUpCommentLoginRequiredMocks( $comments_open, $registration = null, $logged_in = null ) {
		WP_Mock::userFunction( 'comments_open' )
			->once()
			->andReturn( $comments_open );

		if ( $comments_open && null !== $registration ) {
			WP_Mock::userFunction( 'get_option' )
				->with( 'comment_registration' )
				->once()
				->andReturn( $registration );

			if ( $registration && null !== $logged_in ) {
				WP_Mock::userFunction( 'is_user_logged_in' )
					->once()
					->andReturn( $logged_in );
			}
		}
	}

	/**
	 * Test omniform_comment_login_required returns true when comments open, registration required, and user not logged in.
	 */
	public function testOmniformCommentLoginRequiredTrue() {
		$this->setUpCommentLoginRequiredMocks( true, true, false );

		$result = omniform_comment_login_required();

		$this->assertTrue( $result );
	}

	/**
	 * Test omniform_comment_login_required returns false when comments closed.
	 */
	public function testOmniformCommentLoginRequiredCommentsClosed() {
		$this->setUpCommentLoginRequiredMocks( false );

		$result = omniform_comment_login_required();

		$this->assertFalse( $result );
	}

	/**
	 * Test omniform_comment_login_required returns false when registration not required.
	 */
	public function testOmniformCommentLoginRequiredNoRegistration() {
		$this->setUpCommentLoginRequiredMocks( true, false );

		$result = omniform_comment_login_required();

		$this->assertFalse( $result );
	}

	/**
	 * Test omniform_comment_login_required returns false when user logged in.
	 */
	public function testOmniformCommentLoginRequiredUserLoggedIn() {
		$this->setUpCommentLoginRequiredMocks( true, true, true );

		$result = omniform_comment_login_required();

		$this->assertFalse( $result );
	}

	/**
	 * Set up permalink mock.
	 */
	private function setUpPermalinkMock() {
		WP_Mock::userFunction( 'get_permalink' )
			->once()
			->andReturn( 'http://example.com/post' );
	}

	/**
	 * Test omniform_comment_login_url returns the login URL.
	 */
	public function testOmniformCommentLoginUrl() {
		$this->setUpPermalinkMock();
		WP_Mock::userFunction( 'wp_login_url' )
			->with( 'http://example.com/post' )
			->once()
			->andReturn( 'http://example.com/login?redirect_to=http%3A%2F%2Fexample.com%2Fpost' );

		$result = omniform_comment_login_url();

		$this->assertEquals( 'http://example.com/login?redirect_to=http%3A%2F%2Fexample.com%2Fpost', $result );
	}

	/**
	 * Test omniform_comment_logout_url returns the logout URL.
	 */
	public function testOmniformCommentLogoutUrl() {
		$this->setUpPermalinkMock();
		WP_Mock::userFunction( 'wp_logout_url' )
			->with( 'http://example.com/post' )
			->once()
			->andReturn( 'http://example.com/logout?redirect_to=http%3A%2F%2Fexample.com%2Fpost' );

		$result = omniform_comment_logout_url();

		$this->assertEquals( 'http://example.com/logout?redirect_to=http%3A%2F%2Fexample.com%2Fpost', $result );
	}

	/**
	 * Test omniform_closed_for_comments returns true when comments closed.
	 */
	public function testOmniformClosedForCommentsTrue() {
		WP_Mock::userFunction( 'comments_open' )
			->once()
			->andReturn( false );

		$result = omniform_closed_for_comments();

		$this->assertTrue( $result );
	}

	/**
	 * Test omniform_closed_for_comments returns false when comments open.
	 */
	public function testOmniformClosedForCommentsFalse() {
		WP_Mock::userFunction( 'comments_open' )
			->once()
			->andReturn( true );

		$result = omniform_closed_for_comments();

		$this->assertFalse( $result );
	}

	/**
	 * Set up mocks for omniform_open_for_comments.
	 *
	 * @param bool      $comments_open Whether comments are open.
	 * @param bool|null $registration Whether registration is required (null if not checked).
	 * @param bool|null $logged_in Whether user is logged in (null if not checked).
	 */
	private function setUpOpenForCommentsMocks( $comments_open, $registration = null, $logged_in = null ) {
		WP_Mock::userFunction( 'comments_open' )
			->once()
			->andReturn( $comments_open );

		if ( $comments_open && null !== $registration ) {
			WP_Mock::userFunction( 'get_option' )
				->with( 'comment_registration' )
				->once()
				->andReturn( $registration );

			if ( $registration && null !== $logged_in ) {
				WP_Mock::userFunction( 'is_user_logged_in' )
					->once()
					->andReturn( $logged_in );
			}
		}
	}

	/**
	 * Test omniform_open_for_comments returns false when comments closed.
	 */
	public function testOmniformOpenForCommentsClosed() {
		$this->setUpOpenForCommentsMocks( false );

		$result = omniform_open_for_comments();

		$this->assertFalse( $result );
	}

	/**
	 * Test omniform_open_for_comments returns true when comments open and no registration required.
	 */
	public function testOmniformOpenForCommentsOpenNoRegistration() {
		$this->setUpOpenForCommentsMocks( true, false );

		$result = omniform_open_for_comments();

		$this->assertTrue( $result );
	}

	/**
	 * Test omniform_open_for_comments returns true when comments open, registration required, and user logged in.
	 */
	public function testOmniformOpenForCommentsOpenRegistrationLoggedIn() {
		$this->setUpOpenForCommentsMocks( true, true, true );

		$result = omniform_open_for_comments();

		$this->assertTrue( $result );
	}

	/**
	 * Test omniform_open_for_comments returns false when comments open, registration required, and user not logged in.
	 */
	public function testOmniformOpenForCommentsOpenRegistrationNotLoggedIn() {
		$this->setUpOpenForCommentsMocks( true, true, false );

		$result = omniform_open_for_comments();

		$this->assertFalse( $result );
	}

	/**
	 * Set up mocks for omniform_comment_cookies_opt_in.
	 *
	 * @param bool      $has_action Whether the action is hooked.
	 * @param bool|null $option The option value (null if not checked).
	 */
	private function setUpCookiesOptInMocks( $has_action, $option = null ) {
		WP_Mock::userFunction( 'has_action' )
			->with( 'set_comment_cookies', 'wp_set_comment_cookies' )
			->once()
			->andReturn( $has_action );

		if ( $has_action && null !== $option ) {
			WP_Mock::userFunction( 'get_option' )
				->with( 'show_comments_cookies_opt_in' )
				->once()
				->andReturn( $option );
		}
	}

	/**
	 * Test omniform_comment_cookies_opt_in returns true when both conditions met.
	 */
	public function testOmniformCommentCookiesOptInTrue() {
		$this->setUpCookiesOptInMocks( true, true );

		$result = omniform_comment_cookies_opt_in();

		$this->assertTrue( $result );
	}

	/**
	 * Test omniform_comment_cookies_opt_in returns false when has_action false.
	 */
	public function testOmniformCommentCookiesOptInNoAction() {
		$this->setUpCookiesOptInMocks( false );

		$result = omniform_comment_cookies_opt_in();

		$this->assertFalse( $result );
	}

	/**
	 * Test omniform_comment_cookies_opt_in returns false when option false.
	 */
	public function testOmniformCommentCookiesOptInOptionFalse() {
		$this->setUpCookiesOptInMocks( true, false );

		$result = omniform_comment_cookies_opt_in();

		$this->assertFalse( $result );
	}

	/**
	 * Set up mocks for omniform_current_user_display_name.
	 *
	 * @param bool   $exists Whether the user exists.
	 * @param string $display_name The display name if exists.
	 */
	private function setUpUserMock( $exists, $display_name = '' ) {
		$user = Mockery::mock( 'WP_User' );
		$user->shouldReceive( 'exists' )->andReturn( $exists );

		if ( $exists ) {
			$user->display_name = $display_name;
		}

		WP_Mock::userFunction( 'wp_get_current_user' )
			->once()
			->andReturn( $user );
	}

	/**
	 * Test omniform_current_user_display_name returns display name when user exists.
	 */
	public function testOmniformCurrentUserDisplayNameExists() {
		$this->setUpUserMock( true, 'John Doe' );

		$result = omniform_current_user_display_name();

		$this->assertEquals( 'John Doe', $result );
	}

	/**
	 * Test omniform_current_user_display_name returns empty string when user does not exist.
	 */
	public function testOmniformCurrentUserDisplayNameNotExists() {
		$this->setUpUserMock( false );

		$result = omniform_current_user_display_name();

		$this->assertEquals( '', $result );
	}

	/**
	 * Tear down the test environment.
	 */
	public function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}
}
