/**
 * WordPress dependencies.
 */
import {
	__experimentalText as Text,
	__experimentalHeading as Heading,
	__experimentalVStack as VStack,
	Card,
	CardBody,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */

export default function MetricsCard( {
	title,
	primaryValue,
	secondaryText,
	formatType = 'number',
	trend = 'same',
} ) {
	const trendColors = {
		up: '#00a32a',
		down: '#d63638',
	};

	return (
		<Card style={ { width: '100%', textAlign: 'center' } }>
			<CardBody>
				<VStack>
					<Heading level={ 4 } variant="muted">
						{ title }
					</Heading>
					<Text style={ { fontSize: '36px', fontWeight: 'bold' } }>
						{ formatType === 'percentage'
							? `${ primaryValue.toFixed( 1 ) }%`
							: primaryValue.toLocaleString() }
					</Text>
					<Text
						variant="muted"
						style={ {
							color: trendColors[ trend ],
						} }
					>
						{ secondaryText }
					</Text>
				</VStack>
			</CardBody>
		</Card>
	);
}
