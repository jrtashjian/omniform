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
		type,
		label,
		help,
		placeholder,
	} = attributes;

	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<RichText
				className="field-label"
				aria-label={ __( 'Label text', 'inquirywp' ) }
				placeholder={ __( 'Enter a label to the field…', 'inquirywp' ) }
				withoutInteractiveFormatting
				value={ label }
				onChange={ ( html ) => setAttributes( { label: html } ) }
			/>

			<input className="field-control" type="checkbox" />

			{ ( isSelected || !! help ) && (
				<RichText
					className="field-support"
					tagName="p"
					aria-label={ __( 'Help text', 'inquirywp' ) }
					placeholder={ __( 'Write a help text…', 'inquirywp' ) }
					withoutInteractiveFormatting
					value={ help }
					onChange={ ( html ) => setAttributes( { help: html } ) }
				/>
			) }
		</div>
	);
};
export default Edit;