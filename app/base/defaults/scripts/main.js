import domReady from './libs/dom-ready';
import liveDom from './libs/live-dom';

import { selector as targetBlankSelector } from './target-blank';
import { selector as smoothScrollLinksSelector } from './smooth-scroll-links';

import './hash-reset';
import './sticky-header';
import './video-popup';

import '../../../src/scripts/popups';
import '../../../src/scripts/sliders';
import '../../../src/scripts/maps';

domReady( () => {
	if ( window.themeNedToHash ) {
		import( './smooth-scroll-hash' );
	}
	liveDom( '[data-accessibility-menu]' ).onceInit( () => import( './menu' ) );
	liveDom( '.menu-header-main-mobile' ).onceInit( () =>
		import( './menu-header-main-mobile' )
	);
	liveDom( '.open-mobile-menu-button' ).onceInit( () =>
		import( './menu-mobile' )
	);
	liveDom( '[data-menu-navigation]' ).onceInit( () =>
		import( './footer-menu' )
	);
	liveDom( '[data-optimisation-gf-form-id], .gfield' ).onceInit( () =>
		import( './gravity-forms' )
	);
	liveDom( '.accessibility-card' ).onceInit( () => import( './cards' ) );
	liveDom( '.accordion' ).onceInit( () => import( './accordions' ) );
	liveDom( '.tabs:not(.wc-tabs)' ).onceInit( () => import( './tabs' ) );
	liveDom( 'textarea,input' ).onceInit( () =>
		import( './disabled-visual-focus' )
	);
	liveDom( '.form__item' ).onceInit( () => import( './forms' ) );
	liveDom( targetBlankSelector ).onceInit( () => import( './target-blank' ) );
	liveDom( smoothScrollLinksSelector ).onceInit( () =>
		import( './smooth-scroll-links' )
	);

	// eslint-disable-next-line import/no-extraneous-dependencies
	import( 'instant.page' );
} );
