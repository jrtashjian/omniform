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

/**
 * Internal dependencies
 */
import ExampleSlotFill from '../example-slot-fill';

export default function App( { settings } ) {
	return (
		<SlotFillProvider>
			<FullscreenMode isActive={ false } />
			<InterfaceSkeleton
				content={ (
					<div style={ { padding: '1rem' } }>
						<h1>{ __( 'InquiryWP', 'inquirywp' ) }</h1>

						<pre style={ { margin: '0' } }>
							{ __( 'Initial Settings', 'inquirywp' ) }:<br />
							{ JSON.stringify( settings, null, 2 ) }
						</pre>

						<pre><strong>&lt;ExampleSlotFill.Slot&gt;</strong> --------------------</pre>
						<ExampleSlotFill.Slot />
						<pre><strong>&lt;/ExampleSlotFill.Slot&gt;</strong> --------------------</pre>
					</div>
				) }
			/>
			<PluginArea scope="inquirywp" />
			<Popover.Slot />
		</SlotFillProvider>
	);
}
