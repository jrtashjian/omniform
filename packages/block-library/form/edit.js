/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useEntityBlockEditor,
	useEntityProp,
	useEntityRecord,
} from '@wordpress/core-data';
import {
	__experimentalRecursionProvider as RecursionProvider,
	__experimentalUseHasRecursion as useHasRecursion,
	InnerBlocks,
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
	Warning,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Placeholder,
	Spinner,
	TextControl,
} from '@wordpress/components';

const Edit = ( { attributes: { ref } } ) => {
	const hasAlreadyRendered = useHasRecursion( ref );
	const { record, hasResolved } = useEntityRecord(
		'postType',
		'inquirywp_form',
		ref
	);
	const isMissing = hasResolved && ! record;

	const [ blocks, onInput, onChange ] = useEntityBlockEditor(
		'postType',
		'inquirywp_form',
		{ id: ref }
	);
	const [ title, setTitle ] = useEntityProp(
		'postType',
		'inquirywp_form',
		'title',
		ref
	);

	const blockProps = useBlockProps();

	const innerBlockProps = useInnerBlocksProps( {
		ref: blockProps.ref,
		className: 'block-library-block__reusable-block-container',
	}, {
		value: blocks,
		onInput,
		onChange,
		renderAppender: blocks?.length
			? undefined
			: InnerBlocks.ButtonBlockAppender,
	} );

	if ( hasAlreadyRendered ) {
		return (
			<div { ...blockProps }>
				<Warning>
					{ __( 'Form cannot be rendered inside itself.', 'inquirywp' ) }
				</Warning>
			</div>
		);
	}

	if ( isMissing ) {
		return (
			<div { ...blockProps }>
				<Warning>
					{ __( 'Form has been deleted or is unavailable.', 'inquirywp' ) }
				</Warning>
			</div>
		);
	}

	if ( ! hasResolved ) {
		return (
			<div { ...blockProps }>
				<Placeholder>
					<Spinner />
				</Placeholder>
			</div>
		);
	}

	return (
		<RecursionProvider uniqueId={ ref }>
			<InspectorControls>
				<PanelBody>
					<TextControl
						label={ __( 'Name', 'inquirywp' ) }
						value={ title }
						onChange={ setTitle }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div { ...innerBlockProps } />
				<button type="submit" className="wp-block-button wp-element-button">Submit</button>
			</div>
		</RecursionProvider>
	);
};
export default Edit;
