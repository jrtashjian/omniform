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

			<input
				type={ type }
				className="field-control"
				id="textInput"
				aria-describedby="textInputHelp"
				aria-label={ __( 'Optional placeholder text', 'inquirywp' ) }
				placeholder={
					placeholder ? undefined : __( 'Enter a label to the field…', 'inquirywp' )
				}
				value={ placeholder }
				onChange={ ( event ) =>
					setAttributes( { placeholder: event.target.value } )
				}
				autoComplete="off"
			/>

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
