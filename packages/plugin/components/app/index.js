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
import { useEntityRecords } from '@wordpress/core-data';

export default function App( { settings } ) {
	const { records: formRecords, hasResolved: hasFormRecordsResolved } = useEntityRecords( 'postType', 'omniform' );
	const { records: responseRecords, hasResolved: hasResponseRecordsResolved } = useEntityRecords( 'postType', 'omniform_response' );

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

						<ul>
							{ hasFormRecordsResolved && formRecords.map( ( form ) => (
								<li key={ form.id }><a href={ `/wp-admin/post.php?post=${ form.id }&action=edit` }>{ form.title.raw }</a></li>
							) ) }
						</ul>

						<ul>
							{ hasResponseRecordsResolved && responseRecords.map( ( form ) => (
								<li key={ form.id }><a href={ `/wp-admin/post.php?post=${ form.id }&action=edit` }>{ form.title.raw }</a></li>
							) ) }
						</ul>
					</div>
				) }
			/>
			<PluginArea scope="omniform" />
			<Popover.Slot />
		</SlotFillProvider>
	);
}
