/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useMergeRefs } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import {
	Fill,
	PanelBody,
	TextControl,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import useEnter from '../shared/hooks';

const Edit = ( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) => {
	const {
		fieldPlaceholder,
		fieldType,
	} = attributes;

	const isTextInput = [ 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden', 'username-email' ].includes( fieldType );
	const isOptionInput = [ 'checkbox', 'radio' ].includes( fieldType );

	const {
		selectBlock,
	} = useDispatch( blockEditorStore );

	const { selectedBlockClientId, parentClientId } = useSelect( ( select ) => {
		const { getSelectedBlockClientId, getBlockRootClientId } = select( blockEditorStore );
		return {
			selectedBlockClientId: getSelectedBlockClientId(),
			parentClientId: getBlockRootClientId( clientId ),
		};
	}, [ clientId ] );

	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
	} );

	let blockElement;

	if ( isTextInput ) {
		blockElement = (
			<RichText
				{ ...blockProps }
				identifier="fieldControl"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( isSelected || fieldPlaceholder ) ? __( 'Enter a placeholder…', 'omniform' ) : undefined
				}
				value={ fieldPlaceholder }
				onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
				withoutInteractiveFormatting
				allowedFormats={ [] }
				disableLineBreaks
			/>
		);
	} else if ( isOptionInput ) {
		blockElement = (
			<div type={ fieldType } { ...blockProps } />
		);
	} else {
		blockElement = (
			<input type={ fieldType } { ...blockProps } readOnly
				// Prevent editing of the input field in the editor.
				onClick={ ( event ) => event.preventDefault() }
				onKeyDown={ ( event ) => {
					event.preventDefault();
					if ( event.key === 'ArrowLeft' || event.key === 'ArrowRight' ) {
						if ( parentClientId ) {
							selectBlock( parentClientId );
						}
					}
				} }
				onMouseDown={ ( event ) => {
					event.preventDefault();
					event.target.focus();
				} }
			/>
		);
	}

	return (
		<>
			{ blockElement }
			{ selectedBlockClientId === parentClientId && (
				<Fill name="OmniformFieldInnerSettings">
					<InputSettingsPanel { ...{ attributes, setAttributes } } />
				</Fill>
			) }
			{ selectedBlockClientId === clientId && (
				<InspectorControls>
					<PanelBody title={ __( 'Input Settings', 'omniform' ) }>
						<InputSettingsPanel { ...{ attributes, setAttributes } } />
					</PanelBody>
				</InspectorControls>
			) }
		</>
	);
};

function InputSettingsPanel( {
	attributes,
	setAttributes,
} ) {
	const attributeInputTypes = {
		placeholder: [ 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden', 'username-email' ],
		min: [ 'range' ],
		max: [ 'range' ],
		step: [ 'range' ],
	};

	return (
		<>
			{ attributeInputTypes.placeholder.includes( attributes.fieldType ) && (
				<TextControl
					label={ __( 'Placeholder', 'omniform' ) }
					value={ attributes.fieldPlaceholder || '' }
					onChange={ ( value ) => setAttributes( { fieldPlaceholder: value } ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			) }
			{ attributeInputTypes.min.includes( attributes.fieldType ) && (
				<NumberControl
					label={ __( 'Min', 'omniform' ) }
					value={ attributes.fieldMin || '' }
					onChange={ ( value ) => setAttributes( { fieldMin: value } ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			) }
			{ attributeInputTypes.max.includes( attributes.fieldType ) && (
				<NumberControl
					label={ __( 'Max', 'omniform' ) }
					value={ attributes.fieldMax || '' }
					onChange={ ( value ) => setAttributes( { fieldMax: value } ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			) }
			{ attributeInputTypes.step.includes( attributes.fieldType ) && (
				<NumberControl
					label={ __( 'Step', 'omniform' ) }
					value={ attributes.fieldStep || '' }
					onChange={ ( value ) => setAttributes( { fieldStep: value } ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			) }
		</>
	);
}

export default Edit;
