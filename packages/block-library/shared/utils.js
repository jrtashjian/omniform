/**
 * External dependencies
 */
import removeAccents from 'remove-accents';

/**
 * Generates a short unique ID.
 *
 * @return {string} The short ID.
 */
export function generateShortId() {
	return window.crypto
		.getRandomValues( new Uint8Array( 6 ) )
		.reduce( ( a, b ) => a + b.toString( 36 ), '' )
		.substring( 0, 8 );
}

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

/**
 * Collects every omniform/input fieldType nested under the given blocks.
 *
 * Fields can nest inside core/columns, so the walk recurses into inner blocks.
 *
 * @param {Array} blocks Block list to inspect.
 * @return {Array} fieldType strings; '' for fields without an input control.
 */
function collectFieldTypes( blocks ) {
	const types = [];

	if ( ! Array.isArray( blocks ) ) {
		return types;
	}

	for ( const block of blocks ) {
		if ( 'omniform/field' === block.name ) {
			const input = ( block.innerBlocks || [] ).find(
				( inner ) => 'omniform/input' === inner.name,
			);
			types.push( input ? input.attributes?.fieldType : '' );
			continue;
		}

		if ( Array.isArray( block.innerBlocks ) && block.innerBlocks.length ) {
			types.push( ...collectFieldTypes( block.innerBlocks ) );
		}
	}

	return types;
}

/**
 * Whether the blocks form a choice-only fieldset, and which kind.
 *
 * Returns 'radio' or 'checkbox' when every field is the same choice type;
 * otherwise null (mixed, non-choice, or no fields).
 *
 * @param {Array} blocks Fieldset inner blocks.
 * @return {string|null} The shared choice type, or null.
 */
export function choiceGroupType( blocks ) {
	const types = collectFieldTypes( blocks );

	if ( ! types.length ) {
		return null;
	}

	const first = types[ 0 ];

	if ( ! [ 'radio', 'checkbox' ].includes( first ) ) {
		return null;
	}

	return types.every( ( type ) => type === first ) ? first : null;
}
