/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import {
	__experimentalVStack as VStack,
	__experimentalHStack as HStack,
	SelectControl,
} from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies.
 */
import MetricsCard from '../metrics-card';

export default function MetricsPanel() {
	const [ period, setPeriod ] = useState( '7d' );
	const [ metrics, setMetrics ] = useState( null );
	const [ error, setError ] = useState( null );

	useEffect( () => {
		const fetchMetrics = async () => {
			setError( null );

			try {
				const path = addQueryArgs( '/omniform/v1/analytics/overview', { period } );
				const data = await apiFetch( { path } );
				setMetrics( data );
			} catch ( err ) {
				setError( err );
			}
		};

		fetchMetrics();
	}, [ period ] );

	const formatChange = ( changeValue ) => {
		const sign = changeValue > 0 ? '+' : '';
		return `${ sign }${ ( changeValue * 100 ).toFixed( 0 ) }%`;
	};

	const { metrics: metricData, comparison } = metrics || {};

	return (
		<VStack spacing="4">
			<HStack alignment="right">
				<SelectControl
					label={ __( 'Time Period', 'omniform' ) }
					value={ period }
					options={ [
						{ label: __( 'Last 24 Hours', 'omniform' ), value: '1d' },
						{ label: __( 'Last 7 Days', 'omniform' ), value: '7d' },
						{ label: __( 'Last 30 Days', 'omniform' ), value: '30d' },
					] }
					onChange={ setPeriod }
					labelPosition="side"
					__nextHasNoMarginBottom
				/>
			</HStack>

			<HStack spacing="4" alignment="stretch">
				<MetricsCard
					title={ __( 'Total Responses', 'omniform' ) }
					primaryValue={ error ? 0 : ( metricData?.submissions?.total || 0 ) }
					secondaryText={ error ? '-' : `${ metricData?.submissions?.unique || 0 } unique` }
				/>

				<MetricsCard
					title={ __( 'Total Impressions', 'omniform' ) }
					primaryValue={ error ? 0 : ( metricData?.impressions?.total || 0 ) }
					secondaryText={ error ? '-' : `${ metricData?.impressions?.unique || 0 } unique` }
				/>

				<MetricsCard
					title={ __( 'Avg Conversion Rate', 'omniform' ) }
					primaryValue={ error ? 0 : ( ( metricData?.conversion_rate || 0 ) * 100 ) }
					secondaryText={ error ? '-' : formatChange( comparison?.conversion_rate ) }
					formatType="percentage"
				/>
			</HStack>
		</VStack>
	);
}
