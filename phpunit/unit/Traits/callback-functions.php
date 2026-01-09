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

/**
 * Callback function that returns true.
 *
 * @return bool
 */
function omniform_existent_callback_return_true() {
	return true;
}

/**
 * Callback function that returns false.
 *
 * @return bool
 */
function omniform_existent_callback_return_false() {
	return false;
}

/**
 * Callback function that returns a number.
 *
 * @return int
 */
function omniform_existent_callback_return_number() {
	return 42;
}
