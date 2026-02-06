import { HashRouter } from 'react-router-dom';

/**
 * WordPress dependencies.
 */
import '@wordpress/dom-ready';
import { createRoot, StrictMode } from '@wordpress/element';

/**
 * Internal dependencies.
 */
import App from './components/app';
import './style.scss';

/**
 * Initializes the dashboard app and returns the React root instance.
 *
 * @param {string}  id       Unique identifier for the DOM element.
 * @param {?Object} settings Settings object.
 */
export function initialize( id, settings ) {
	const target = document.getElementById( id );
	const root = createRoot( target );

	root.render(
		<StrictMode>
			<HashRouter>
				<App settings={ settings } />
			</HashRouter>
		</StrictMode>
	);
	return root;
}
