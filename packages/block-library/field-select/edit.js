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
	InnerBlocks,
	useInnerBlocksProps,
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
		multiple,
		help,
	} = attributes;

	const blockProps = useBlockProps();

	const innerBlockProps = useInnerBlocksProps( {
		ref: blockProps.ref,
		className: 'omniform-select-options-container',
	}, {
		allowedBlocks: [ 'omniform/select-option', 'omniform/select-group' ],
		template: [
			[ 'omniform/select-option', { label: 'Option One' } ],
			[ 'omniform/select-option', { label: 'Option Two' } ],
			[ 'omniform/select-option', { label: 'Option Three' } ],
			[
				'omniform/select-group',
				{ label: 'Option Group One' },
				[
					[ 'omniform/select-option', { label: 'Option One' } ],
					[ 'omniform/select-option', { label: 'Option Two' } ],
					[ 'omniform/select-option', { label: 'Option Three' } ],
				],
			],
			[
				'omniform/select-group',
				{ label: 'Option Group Two' },
				[
					[ 'omniform/select-option', { label: 'Option One' } ],
					[ 'omniform/select-option', { label: 'Option Two' } ],
					[ 'omniform/select-option', { label: 'Option Three' } ],
					[
						'omniform/select-group',
						{ label: 'Option Group Two' },
						[
							[ 'omniform/select-option', { label: 'Option One' } ],
							[ 'omniform/select-option', { label: 'Option Two' } ],
							[ 'omniform/select-option', { label: 'Option Three' } ],
						],
					],
				],
			],
			[
				'omniform/select-group',
				{ label: 'Option Group' },
				[
					[ 'omniform/select-option', { label: 'Option One' } ],
					[ 'omniform/select-option', { label: 'Option Two' } ],
					[ 'omniform/select-option', { label: 'Option Three' } ],
				],
			],
		],
		renderAppender: InnerBlocks.ButtonBlockAppender,
	} );

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-field-select' ) }
		>
			<FormLabel originBlockProps={ props } />

			<select className="omniform-field-control" multiple={ multiple }></select>

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

			<ul { ...innerBlockProps } />
		</div>
	);
};
export default Edit;
