/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	Popover,
	SlotFillProvider,
	__experimentalHeading as Heading,
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	Button,
} from '@wordpress/components';
import {
	FullscreenMode,
	InterfaceSkeleton,
} from '@wordpress/interface';

export default function App( { settings } ) {
	return (
		<SlotFillProvider>
			<FullscreenMode isActive={ false } />
			<InterfaceSkeleton
				header={ (
					<VStack className="omniform-page__header" as="header">
						<HStack justify="space-between" spacing={ 2 }>
							<HStack spacing={ 2 } justify="left">
								<Heading as="h2" level={ 3 } weight={ 500 } truncate>
									{ __( 'OmniForm', 'omniform' ) }
								</Heading>
							</HStack>
							<HStack
								style={ { width: 'auto', flexShrink: 0 } }
								spacing={ 2 }
								className="omniform-page__header-actions"
							>
								<Button variant="primary">
									{ __( 'Primary Action', 'omniform' ) }
								</Button>
							</HStack>
						</HStack>
						<p className="omniform-page__header-subtitle">
							{ __( 'Subtitle', 'omniform' ) }
						</p>
					</VStack>
				) }
				content={ (
					<div style={ { padding: '1rem' } }>
						<pre style={ { margin: '0' } }>
							{ __( 'Initial Settings', 'omniform' ) }:<br />
							{ JSON.stringify( settings, null, 2 ) }
						</pre>
					</div>
				) }
			/>
			<Popover.Slot />
		</SlotFillProvider>
	);
}
