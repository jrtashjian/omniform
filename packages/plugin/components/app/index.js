/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	Popover,
	SlotFillProvider,
} from '@wordpress/components';
import { PluginArea } from '@wordpress/plugins';
import {
	FullscreenMode,
	InterfaceSkeleton,
} from '@wordpress/interface';

export default function App( { settings } ) {
	return (
		<SlotFillProvider>
			<FullscreenMode isActive={ false } />
			<InterfaceSkeleton
				content={ (
					<div style={ { padding: '1rem' } }>
						<h1>{ __( 'OmniForm', 'omniform' ) }</h1>

						<pre style={ { margin: '0' } }>
							{ __( 'Initial Settings', 'omniform' ) }:<br />
							{ JSON.stringify( settings, null, 2 ) }
						</pre>
					</div>
				) }
			/>
			<PluginArea scope="omniform" />
			<Popover.Slot />
		</SlotFillProvider>
	);
}
