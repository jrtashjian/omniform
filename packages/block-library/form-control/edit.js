/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes,
	setAttributes,
	isSelected,
} ) => {
	const {
		label,
		helpText,
	} = attributes;

	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<RichText
				className="form-label"
				aria-label={ __( 'Label text', 'inquirywp' ) }
				placeholder={ __( 'Add label…', 'inquirywp' ) }
				withoutInteractiveFormatting
				value={ label }
				onChange={ ( html ) => setAttributes( { label: html } ) }
			/>

			<input type="text" className="form-control" id="textInput" aria-describedby="textInputHelp" />

			{ isSelected && ! helpText && (
				<RichText
					className="form-text"
					aria-label={ __( 'Help text', 'inquirywp' ) }
					placeholder={ __( 'Add help text…', 'inquirywp' ) }
					withoutInteractiveFormatting
					value={ helpText }
					onChange={ ( html ) => setAttributes( { helpText: html } ) }
				/>
			) }
		</div>
	);
};
export default Edit;
