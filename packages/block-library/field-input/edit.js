/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';
import { Required } from '../shared/icons';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
		context,
	} = props;
	const {
		fieldPlaceholder,
		fieldType,
		fieldValue,
		isRequired,
	} = attributes;

	const isTextInput = [ 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' ].includes( fieldType );
	const isOptionInput = [ 'checkbox', 'radio' ].includes( fieldType );
	const isHiddenInput = fieldType === 'hidden';
	const isGrouped = !! context[ 'omniform/fieldGroupName' ];

	const blockProps = useBlockProps();

	const richTextPlaceholder = isHiddenInput
		? __( 'Enter a value…', 'omniform' )
		: __( 'Enter a placeholder…', 'omniform' );
	const richTextOnChange = ( html ) => isHiddenInput
		? setAttributes( { fieldValue: html } )
		: setAttributes( { fieldPlaceholder: html } );

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				`omniform-field-${ fieldType }`,
				{ [ `field-required` ]: isRequired }
			) }
		>
			<FormLabel
				originBlockProps={ props }
				isRadioInput={ 'radio' === fieldType }
				isGrouped={ isGrouped }
			/>

			{ isTextInput && (
				<RichText
					identifier="fieldControl"
					className="omniform-field-control"
					aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
					placeholder={
						( fieldPlaceholder || fieldValue || ! isSelected )
							? undefined
							: richTextPlaceholder
					}
					allowedFormats={ [] }
					withoutInteractiveFormatting
					value={ isHiddenInput ? fieldValue : fieldPlaceholder }
					onChange={ richTextOnChange }
					// When hitting enter, place a new insertion point. This makes adding field a lot easier.
					onSplit={ ( _value, isOriginal ) => {
						const block = isOriginal
							? createBlock( props.name, props.attributes )
							: createBlock( getDefaultBlockName() );

						if ( isOriginal ) {
							block.clientId = props.clientId;
						}

						return block;
					} }
					onReplace={ props.onReplace }
				/>
			) }

			{ isOptionInput && (
				<div className="omniform-field-control" />
			) }

			{ ! isTextInput && ! isOptionInput && (
				<input
					className="omniform-field-control"
					type={ fieldType }
					disabled
				/>
			) }

			<BlockControls>
				<ToolbarGroup>
					{ ! isHiddenInput && (
						<ToolbarButton
							icon={ Required }
							isActive={ isRequired }
							label={ __( 'Required field', 'omniform' ) }
							onClick={ () => setAttributes( { isRequired: ! isRequired } ) }
						/>
					) }
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
			</InspectorControls>
		</div>
	);
};
export default Edit;
