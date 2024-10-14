/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	Warning,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

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

	const [ isQuickStartFinished, setIsQuickStartFinished ] = useState( false );

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

	useEffect( () => {
		if ( hasInnerBlocks ) {
			setIsQuickStartFinished( true );
		}
	}, [ hasInnerBlocks ] );

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

	if ( ! isQuickStartFinished ) {
		return (
			<div { ...blockProps }>
				<QuickStartPlaceholder clientId={ clientId } />
			</div>
		);
	}

	return (
		<>
			<FormInspectorControls blockObject={ blockObject } />
			<div { ...innerBlockProps } />
		</>
	);
}
