<?php
/**
 * PHPUnit helper functions.
 *
 * @package OmniForm
 */

/**
 * Callback function that returns a string.
 *
 * @return string
 */
function omniform_existent_callback_return_string() {
	return 'callback string';
}

/**
 * Callback function that returns an array.
 *
 * @return array
 */
function omniform_existent_callback_return_array() {
	return array();
}

/**
 * Callback function that returns an object.
 *
 * @return \stdClass
 */
function omniform_existent_callback_return_object() {
	return new \stdClass();
}
