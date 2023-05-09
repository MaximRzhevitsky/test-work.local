import domReady from './libs/dom-ready';
import liveDom from './libs/live-dom';
import { maybeScrollTo } from './libs/smooth-scroll';

export const selector =
	'a[href*="#"]:not([href="#"]):not([href*="popup"]):not(.popup-link):not([href^="#tab-"])';

domReady( () => {
	liveDom( selector ).init( function () {
		const link = this;
		const windowPathname = window.location.pathname.replace( /^\//, '' );
		const linkPathname = link.pathname.replace( /^\//, '' );

		if (
			link.parentNode.classList.contains( 'popup-link' ) ||
			windowPathname !== linkPathname ||
			window.location.hostname !== link.hostname
		) {
			return;
		}

		link.addEventListener( 'click', function ( event ) {
			event.preventDefault();

			maybeScrollTo( link.hash, event );
		} );
	} );
} );
