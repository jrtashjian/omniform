/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	Warning,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import FormInspectorControls from './inspector-controls';
import { ALLOWED_BLOCKS } from '../../shared/constants';
import QuickStartPlaceholder from './quick-start';

export default function StandaloneForm( blockObject ) {
	const {
		name,
		clientId,
	} = blockObject;

	const { isNested, hasInnerBlocks } = useSelect(
		( select ) => {
			const {
				getBlockParents,
				getBlock,
				getBlockCount,
			} = select( blockEditorStore );

			const parentBlocks = getBlockParents( clientId );
			const rootBlock = getBlock( parentBlocks[ 0 ] );

			return {
				isNested: rootBlock?.name === name,
				hasInnerBlocks: getBlockCount( clientId ) > 0,
			};
		},
		[ clientId, name ]
	);

	const blockProps = useBlockProps( {
		className: 'block-library-block__reusable-block-container',
	} );

	const innerBlockProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		renderAppender: hasInnerBlocks
			? undefined
			: InnerBlocks.ButtonBlockAppender,
	} );

	if ( isNested ) {
		return (
			<div { ...blockProps }>
				<Warning>
					{ __( 'A form cannot be nested within another form.', 'omniform' ) }
				</Warning>
			</div>
		);
	}

	return hasInnerBlocks ? (
		<>
			<FormInspectorControls blockObject={ blockObject } />
			<div { ...innerBlockProps } />
		</>
	) : (
		<div { ...blockProps }>
			<QuickStartPlaceholder clientId={ clientId } />
		</div>
	);
}
