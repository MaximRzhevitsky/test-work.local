import liveDom from '../libs/live-dom.js';
import { scrollToElement } from '../libs/smooth-scroll.js';

liveDom( '.faq' ).init( function () {
	const section = this;
	const form = section.querySelector( '.faq__search-form' );

	if ( form ) {
		const tablist = this.querySelector( '.tabs__tablist' );
		const accordions = section.querySelectorAll( '.accordion' );
		const input = section.querySelector( '.form__input' );
		form.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			if ( input.value ) {
				const pattern = new RegExp( input.value, 'i' );
				accordions.forEach( ( accordion ) => {
					if ( pattern.test( accordion.innerText ) ) {
						accordion.style.removeProperty( 'display' );
					} else {
						accordion.style.display = 'none';
					}
				} );
			} else {
				accordions.forEach( ( accordion ) => {
					accordion.style.removeProperty( 'display' );
				} );
			}

			scrollToElement( tablist, 30 ).then();
		} );
	}
} );
