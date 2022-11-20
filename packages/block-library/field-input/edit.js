/**
 * External dependencies
 */
import classNames from 'classnames';

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
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, `inquirywp-field-${ type }` ) }
		>
			<RichText
				className="inquirywp-field-label"
				aria-label={ __( 'Label text', 'inquirywp' ) }
				placeholder={ __( 'Enter a label to the field…', 'inquirywp' ) }
				withoutInteractiveFormatting
				multiple={ false }
				value={ label }
				onChange={ ( html ) => setAttributes( { label: html } ) }
			/>

			<input
				type={ type }
				className="inquirywp-field-control"
				id="textInput"
				aria-describedby="textInputHelp"
				aria-label={ __( 'Optional placeholder text', 'inquirywp' ) }
				placeholder={
					( placeholder || ! isSelected ) ? undefined : __( 'Enter a placeholder…', 'inquirywp' )
				}
				value={ placeholder }
				onChange={ ( event ) =>
					setAttributes( { placeholder: event.target.value } )
				}
				autoComplete="off"
			/>

			{ ( isSelected || help ) && (
				<RichText
					className="inquirywp-field-support"
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
