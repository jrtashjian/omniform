/**
 * External dependencies
 */
import removeAccents from 'remove-accents';

/**
 * Performs some basic cleanup of a string for use as a fieldName.
 *
 * @see https://github.com/WordPress/gutenberg/blob/wp/6.5/packages/url/src/clean-for-slug.js
 *
 * @param {string} string The string to be processed.
 *
 * @return {string} The processed string.
 */
export function cleanFieldName( string ) {
	if ( ! string ) {
		return '';
	}
	return (
		removeAccents( string.replace( /(<([^>]+)>)/gi, '' ) )
			// Convert each group of whitespace, periods, and forward slashes to a hyphen.
			.replace( /[\s\./]+/g, '-' )
			// Remove anything that's not a letter, number, underscore or hyphen.
			.replace( /[^\p{L}\p{N}_-]+/gu, '' )
			// Replace multiple hyphens with a single one.
			.replace( /-+/g, '-' )
			// Remove any remaining leading or trailing hyphens.
			.replace( /(^-+)|(-+$)/g, '' )
	);
}
