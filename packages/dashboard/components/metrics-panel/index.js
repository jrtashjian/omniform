/**
 * WordPress dependencies.
 */
import { useState, useEffect } from '@wordpress/element';
import { __experimentalHStack as HStack } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies.
 */
import MetricsCard from '../metrics-card';

export default function MetricsPanel() {
	const [ metrics, setMetrics ] = useState( null );
	const [ error, setError ] = useState( null );

	useEffect( () => {
		const fetchMetrics = async () => {
			setError( null );

			try {
				const data = await apiFetch( {
					path: '/omniform/v1/analytics/overview',
				} );
				setMetrics( data.metrics );
			} catch ( err ) {
				setError( err );
			}
		};

		fetchMetrics();
	}, [] );

	const items = metrics ?? [];

	return (
		<HStack spacing="4" alignment="stretch">
			{ items.map( ( metric, index ) => (
				<MetricsCard
					key={ index }
					title={ metric.title }
					primaryValue={ error ? 0 : metric.value }
					secondaryText={ error ? '-' : metric.sub_text }
					formatType={ metric.format }
					trend={ error ? 'same' : metric.trend }
				/>
			) ) }
		</HStack>
	);
}
