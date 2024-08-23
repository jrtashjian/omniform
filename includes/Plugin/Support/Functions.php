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
