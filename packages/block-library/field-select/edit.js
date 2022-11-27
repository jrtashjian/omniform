/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { __experimentalHStack as HStack } from '@wordpress/components';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';
import ButtonBlockAppender from '../shared/button-block-appender';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
		clientId,
	} = props;
	const {
		placeholder,
		multiple,
		help,
	} = attributes;

	const {
		hasSelectedInnerBlock,
		parentClientId,
	} = useSelect(
		( select ) => {
			const blockSelected = select( blockEditorStore ).getSelectedBlock();
			return {
				hasSelectedInnerBlock: select( blockEditorStore ).hasSelectedInnerBlock( clientId, true ),
				parentClientId: select( blockEditorStore ).getBlockRootClientId( blockSelected?.clientId ),
				selectedBlock: blockSelected,
			};
		},
		[ clientId ]
	);

	const blockProps = useBlockProps();

	const innerBlockProps = useInnerBlocksProps( {
		className: 'omniform-select-options-container',
	}, {
		allowedBlocks: [ 'omniform/select-option', 'omniform/select-group' ],
		template: [
			[ 'omniform/select-option', { label: 'Option One' } ],
			[ 'omniform/select-option', { label: 'Option Two' } ],
			[ 'omniform/select-option', { label: 'Option Three' } ],
		],
		__experimentalCaptureToolbars: true,
		renderAppender: false,
	} );

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-field-select', { [ `type-multiple` ]: multiple } ) }
		>
			<FormLabel originBlockProps={ props } />

			<div className="omniform-field-control">
				{ ! multiple && (
					<RichText
						className="placeholder-text"
						aria-label={ __( 'Help text', 'omniform' ) }
						placeholder={
							( placeholder || ( ! isSelected && ! hasSelectedInnerBlock ) ) ? undefined : __( 'Enter a placeholder…', 'omniform' )
						}
						allowedFormats={ [] }
						withoutInteractiveFormatting
						value={ placeholder }
						onChange={ ( html ) => setAttributes( { placeholder: html } ) }
					/>
				) }
				{ ( isSelected || hasSelectedInnerBlock || multiple ) && (
					<ul { ...innerBlockProps } />
				) }

				{ ( isSelected || hasSelectedInnerBlock ) && (
					<HStack>
						{ ( ! parentClientId || parentClientId === clientId ) ? (
							<>
								<ButtonBlockAppender
									rootClientId={ clientId }
									insertBlockType="omniform/select-option"
								/>
								<ButtonBlockAppender
									rootClientId={ clientId }
									insertBlockType="omniform/select-group"
								/>
							</>
						) : (
							<ButtonBlockAppender
								rootClientId={ parentClientId }
								insertBlockType="omniform/select-option"
							/>
						) }
					</HStack>
				) }
			</div>

			{ ( isSelected || help ) && (
				<RichText
					className="omniform-field-support"
					tagName="p"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write a help text…', 'omniform' ) }
					withoutInteractiveFormatting
					value={ help }
					onChange={ ( html ) => setAttributes( { help: html } ) }
				/>
			) }
		</div>
	);
};
export default Edit;
