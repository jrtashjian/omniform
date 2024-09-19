/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	HeadingLevelDropdown,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes: { noReplyText, level },
	setAttributes,
} ) => {
	const blockProps = useBlockProps();

	return (
		<>
			<BlockControls>
				<HeadingLevelDropdown
					value={ level }
					onChange={ ( value ) => setAttributes( { level: value } ) }
				/>
			</BlockControls>
			<RichText
				identifier="noReplyText"
				tagName={ 'h' + level }
				placeholder={ __( 'Enter a title for the comment form…', 'omniform' ) }
				value={ noReplyText ?? '' }
				onChange={ ( value ) => setAttributes( { noReplyText: value } ) }
				{ ...blockProps }
			/>
		</>
	);
};

export default Edit;
