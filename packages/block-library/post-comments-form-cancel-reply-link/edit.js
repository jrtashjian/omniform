/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes: { linkText },
	setAttributes,
} ) => {
	const blockProps = useBlockProps();

	return (
		<RichText
			identifier="linkText"
			tagName={ 'a' }
			placeholder={ __( 'Enter a title for the cancel reply link…', 'omniform' ) }
			value={ linkText ?? '' }
			onChange={ ( value ) => setAttributes( { linkText: value } ) }
			{ ...blockProps }
		/>
	);
};

export default Edit;
