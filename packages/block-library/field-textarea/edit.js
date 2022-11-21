/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
	} = props;
	const {
		type,
		help,
		placeholder,
	} = attributes;

	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<FormLabel originBlockProps={ props } />

			<textarea
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
				rows="10"
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
