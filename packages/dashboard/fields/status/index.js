/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';

const STATUSES = [
	{ value: 'trash', label: __( 'Trash', 'omniform' ) },
	{ value: 'omniform_read', label: __( 'Read', 'omniform' ) },
	{ value: 'omniform_unread', label: __( 'Unread', 'omniform' ) },
];

const statusField = {
	id: 'status',
	label: __( 'Status', 'omniform' ),
	elements: STATUSES,
	filterBy: {
		operators: [ 'isAny' ],
	},
	enableSorting: false,
};

export default statusField;
