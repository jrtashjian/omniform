/**
 * WordPress dependencies.
 */
import { registerPlugin } from '@wordpress/plugins';
import { __ } from '@wordpress/i18n';
import { Button, Popover } from '@wordpress/components';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies.
 */
import ExampleSlotFill from './components/example-slot-fill';

const TestPlugin = () => {
	const [ isVisible, setIsVisible ] = useState( false );

	return (
		<ExampleSlotFill>
			<p>
				{ __( 'This content is rendered from a registered plugin using:', 'inquirywp' ) }&nbsp;
				<a href="https://developer.wordpress.org/block-editor/reference-guides/packages/packages-plugins/#registerplugin" target={ '_blank' } rel="noreferrer">registerPlugin()</a>
			</p>
			<Button variant="primary" onClick={ () => setIsVisible( ! isVisible ) }>
				{ __( 'Toggle Popover!', 'inquirywp' ) }
				{ isVisible && <Popover>{ __( 'Popover is toggled!', 'inquirywp' ) }</Popover> }
			</Button>
		</ExampleSlotFill>
	);
};

registerPlugin( 'test-plugin', {
	render: TestPlugin,
	scope: 'inquirywp',
} );
