<?php
/**
 * Helper Functions.
 *
 * @package OmniForm
 */

/**
 * Get the current commenter's name.
 *
 * @return string The commenter's name.
 */
function omniform_current_commenter_author() {
	$commenter = wp_get_current_commenter();
	return $commenter['comment_author'];
}

/**
 * Get the current commenter's email.
 *
 * @return string The commenter's email.
 */
function omniform_current_commenter_email() {
	$commenter = wp_get_current_commenter();
	return $commenter['comment_author_email'];
}

/**
 * Get the current commenter's URL.
 *
 * @return string The commenter's URL.
 */
function omniform_current_commenter_url() {
	$commenter = wp_get_current_commenter();
	return $commenter['comment_author_url'];
}

/**
 * Check if the current user is required to login to comment.
 *
 * @return bool True if the user is required to login to comment, false otherwise.
 */
function omniform_comment_login_required() {
	return comments_open() && get_option( 'comment_registration' ) && ! is_user_logged_in();
}

/**
 * Check if comments are closed for the current post.
 *
 * @return bool True if comments are closed for the current post, false otherwise.
 */
function omniform_closed_for_comments() {
	return ! comments_open();
}

/**
 * Check if comments are open for the current post.
 *
 * @return bool True if comments are open for the current post, false otherwise.
 */
function omniform_open_for_comments() {
	// Check if comments are open.
	if ( ! comments_open() ) {
		return false;
	}

	// If comment_registration is required, check if the user is logged in.
	if ( get_option( 'comment_registration' ) ) {
		return is_user_logged_in();
	}

	// If comment_registration is not required, allow comments.
	return true;
}

/**
 * Check if the comment cookies opt-in is enabled.
 *
 * @return bool True if the comment cookies opt-in is enabled, false otherwise.
 */
function omniform_comment_cookies_opt_in() {
	return has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' );
}
