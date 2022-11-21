/**
 * WordPress dependencies
 */
import { useEntityBlockEditor } from '@wordpress/core-data';
import {
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { FORM_POST_TYPE } from '../../shared/constants';

export default function FormInnerBlocks( {
	blockProps,
	formId: id,
	hasInnerBlocks,
} ) {
	const [ blocks, onInput, onChange ] = useEntityBlockEditor(
		'postType',
		FORM_POST_TYPE,
		{ id }
	);

	const innerBlockProps = useInnerBlocksProps( blockProps, {
		value: blocks,
		onInput,
		onChange,
		renderAppender: hasInnerBlocks
			? undefined
			: InnerBlocks.ButtonBlockAppender,
	} );

	return <div { ...innerBlockProps } />;
}
