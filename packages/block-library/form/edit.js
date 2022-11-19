/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
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

const Edit = ( { attributes: { ref }, isSelected, clientId } ) => {
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

	const hasSelectedInnerBlock = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);

	const blockProps = useBlockProps( {
		className: 'block-library-block__reusable-block-container is-layout-flow',
	} );

	const innerBlockProps = useInnerBlocksProps( blockProps, {
		value: blocks,
		onInput,
		onChange,
		renderAppender: isSelected || hasSelectedInnerBlock
			? InnerBlocks.ButtonBlockAppender
			: undefined,
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
			<div { ...innerBlockProps } />
		</RecursionProvider>
	);
};
export default Edit;
