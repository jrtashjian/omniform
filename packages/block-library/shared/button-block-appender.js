/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
	Button,
	__experimentalHStack as HStack,
} from '@wordpress/components';
import { Icon, plus } from '@wordpress/icons';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { createBlock, getBlockType } from '@wordpress/blocks';

const ButtonBlockAppender = ( { rootClientId, className, insertBlockType } ) => {
	const { insertBlocks } = useDispatch( blockEditorStore );
	const blockType = getBlockType( insertBlockType );

	const { getBlockCount } = useSelect( ( select ) => {
		return {
			getBlockCount: select( blockEditorStore ).getBlockCount,
		};
	}, [ rootClientId ] );

	return (
		<Button
			className={ classnames(
				className,
				'block-editor-button-block-appender',
			) }
			onClick={ () =>
				insertBlocks(
					createBlock( insertBlockType, {} ),
					getBlockCount(),
					rootClientId
				)
			}
		>
			<HStack>
				<div>{ blockType.title }</div>
				<Icon icon={ plus } />
			</HStack>
		</Button>
	);
};

export default ButtonBlockAppender;
