import { useLocation } from 'react-router-dom';

/**
 * WordPress dependencies.
 */
import { useEffect } from '@wordpress/element';

export function useSyncCurrentMenu() {
	const { pathname } = useLocation();

	useEffect( () => {
		document.querySelectorAll( '.toplevel_page_omniform li.current' ).forEach( ( element ) => {
			element.classList.remove( 'current' );
			element.removeAttribute( 'aria-current' );
		} );

		const currentPath = pathname === '/' ? '' : `#${ pathname }`;
		const currentLink = document.querySelector( `.toplevel_page_omniform .wp-submenu a[href="admin.php?page=omniform${ currentPath }"]` )?.parentElement;

		if ( currentLink ) {
			currentLink.classList.add( 'current' );
			currentLink.setAttribute( 'aria-current', 'page' );
		}
	}, [ pathname ] );
}
