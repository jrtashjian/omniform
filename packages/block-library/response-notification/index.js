/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import variations from './variations';
import { iconSuccess } from '../shared/icons';

import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: iconSuccess,
	example: {
		attributes: {
			messageType: 'success',
			messageContent: __( 'Success! Your submission has been completed.', 'omniform' ),
			style: { border: { left: { color: 'var(--wp--preset--color--vivid-green-cyan,#00d084)', width: '6px' } }, spacing: { padding: { top: '0.5em', bottom: '0.5em', left: '1.5em', right: '1.5em' } } },
		},
	},
	variations,
} );
