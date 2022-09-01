/**
 * WordPress dependencies.
 */
import '@wordpress/dom-ready';
import { render } from '@wordpress/element';

/**
 * Internal dependencies.
 */
import App from './components/app';
import './style.scss';
import './test-plugin';

/**
 * Initializes and returns and instance of PluginWP.
 *
 * @param {string}  id       Unique identifier for the editor instance.
 * @param {?Object} settings Settings object.
 */
export function initialize( id, settings ) {
	const target = document.getElementById( id );
	render( <App settings={ settings } />, target );
}
