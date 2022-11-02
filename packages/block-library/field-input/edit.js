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
				className="field-label"
				aria-label={ __( 'Label text', 'inquirywp' ) }
				placeholder={ __( 'Add label…', 'inquirywp' ) }
				withoutInteractiveFormatting
				value={ label }
				onChange={ ( html ) => setAttributes( { label: html } ) }
			/>

			<pre>{ JSON.stringify( attributes, '', 2 ) }</pre>

			<input type="text" className="field-control" id="textInput" aria-describedby="textInputHelp" />

			{ isSelected && ! helpText && (
				<RichText
					className="field-text"
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
