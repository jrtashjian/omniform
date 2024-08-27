/**
 * WordPress dependencies
 */
import { store as blockEditorStore } from '@wordpress/block-editor';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import { useRefEffect } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { ENTER } from '@wordpress/keycodes';

/**
 * Handles the Enter key press event.
 *
 * @param {string} clientId The client ID of the block.
 */
export default function useEnter( clientId ) {
	const { insertBlock } = useDispatch( blockEditorStore );
	const { getBlockName, getBlockRootClientId, getBlockIndex } = useSelect( blockEditorStore );

	return useRefEffect(
		( element ) => {
			function onKeyDown( event ) {
				if ( event.defaultPrevented || event.keyCode !== ENTER ) {
					return;
				}

				event.preventDefault();

				const wrappedElements = [ 'omniform/input', 'omniform/label', 'omniform/select' ];

				const targetClientId = wrappedElements.includes( getBlockName( clientId ) )
					? getBlockRootClientId( clientId )
					: clientId;

				insertBlock(
					createBlock( getDefaultBlockName() ),
					getBlockIndex( targetClientId ) + 1,
					getBlockRootClientId( targetClientId )
				);
			}

			element.addEventListener( 'keydown', onKeyDown );
			return () => {
				element.removeEventListener( 'keydown', onKeyDown );
			};
		},
		[ clientId ]
	);
}
