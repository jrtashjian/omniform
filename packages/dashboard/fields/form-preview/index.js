/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { parse } from '@wordpress/blocks';
import { BlockPreview } from '@wordpress/block-editor';

// TODO: BlockPreview isn't working right now because although blocks are
// registered on the backend, they are not getting registered on the frontend.
// This is because we're not actually initializing an editor instance here.
// Once there is a way to register blocks on the frontend without an editor,
// we can use BlockPreview here.
function PreviewField( { item } ) {
	const blocks = useMemo( () => {
		return parse( item.content.raw );
	}, [ item.content.raw ] );
	const isEmpty = ! blocks?.length;

	return (
		<div style={ { width: '90px' } }>
			{ isEmpty && __( 'Empty Form', 'omniform' ) }
			{ ! isEmpty && (
				<BlockPreview.Async>
					<BlockPreview blocks={ blocks } />
				</BlockPreview.Async>
			) }
		</div>
	);
}

const formPreview = {
	id: 'preview',
	label: __( 'Preview', 'omniform' ),
	render: PreviewField,
	enableSorting: false,
};

export default formPreview;
