import { useNavigate } from 'react-router-dom';

/**
 * WordPress dependencies.
 */
import { useEffect } from '@wordpress/element';

export function useInterceptMenuLink() {
	const navigate = useNavigate();

	useEffect( () => {
		const handleClick = ( event ) => {
			const link = event.target.closest( 'a[href="admin.php?page=omniform"]' );

			if ( link ) {
				event.preventDefault();
				event.stopPropagation();

				// Remove the hash from the URL to prevent the page from jumping to the top.
				navigate( '/', { replace: true } );
				window.location.hash = '';
			}
		};

		document.addEventListener( 'click', handleClick, true );
		return () => document.removeEventListener( 'click', handleClick, true );
	}, [ navigate ] );
}
