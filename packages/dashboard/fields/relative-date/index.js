/**
 * WordPress dependencies.
 */
import { __experimentalText as Text } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { getDate } from '@wordpress/date';

function getRelativeTime( dateString ) {
	const now = Date.now();
	const date = getDate( dateString ).getTime();
	const diff = now - date;

	const minute = 60 * 1000;
	const hour = 60 * minute;
	const day = 24 * hour;

	if ( diff < minute ) {
		return __( 'just now', 'omniform' );
	}

	if ( diff < hour ) {
		const minutes = Math.floor( diff / minute );
		return sprintf(
			/* translators: %d: number of minutes */
			__( '%dm ago', 'omniform' ),
			minutes,
		);
	}

	if ( diff < day ) {
		const hours = Math.floor( diff / hour );
		return sprintf(
			/* translators: %d: number of hours */
			__( '%dh ago', 'omniform' ),
			hours,
		);
	}

	const days = Math.floor( diff / day );
	return sprintf(
		/* translators: %d: number of days */
		__( '%dd ago', 'omniform' ),
		days,
	);
}

function RelativeDateField( { item } ) {
	return <Text>{ getRelativeTime( item.date ) }</Text>;
}

const relativeDateField = {
	id: 'date',
	type: 'datetime',
	label: __( 'Date' ),
	render: RelativeDateField,
	enableHiding: false,
	enableSorting: false,
	filterBy: false,
};

export default relativeDateField;
