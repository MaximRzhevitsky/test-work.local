import liveDome from '../../defaults/scripts/libs/live-dom';
import accessibilitySetModalFocus from '../../defaults/scripts/libs/accessibility-set-modal-focus';
import defer from '../../defaults/scripts/jquery/libs/jquery-defer';

let lastFocusElement, cartPanel, closeButton;

function setModalFocus( event ) {
	accessibilitySetModalFocus( event, cartPanel, closeButton );
}

function open( event ) {
	event.preventDefault();

	cartPanel = document.querySelector( '.menu-cart' );
	closeButton = cartPanel.querySelector( '.menu-cart__close' );

	if ( ! closeButton ) {
		setTimeout( () => open( event ), 500 );
		return;
	}

	document
		.querySelectorAll( '.menu-cart-link' )
		.forEach( ( openButton ) =>
			openButton.setAttribute( 'aria-expanded', 'true' )
		);
	closeButton.setAttribute( 'aria-expanded', 'true' );
	cartPanel.setAttribute( 'aria-hidden', 'false' );

	document.body.addEventListener( 'keydown', setModalFocus );

	lastFocusElement = document.activeElement;
	setTimeout( () => {
		closeButton.focus();
	}, 400 );
}

function close() {
	cartPanel = document.querySelector( '.menu-cart' );
	closeButton = cartPanel.querySelector( '.menu-cart__close' );

	document
		.querySelectorAll( '.menu-cart-link' )
		.forEach( ( openButton ) =>
			openButton.setAttribute( 'aria-expanded', 'false' )
		);
	closeButton.setAttribute( 'aria-expanded', 'false' );
	cartPanel.setAttribute( 'aria-hidden', 'true' );

	document.body.removeEventListener( 'keydown', setModalFocus );

	if ( lastFocusElement ) {
		lastFocusElement.focus();
		lastFocusElement = null;
	}
}

liveDome( '.menu-cart-link' ).init( function() {
	this.addEventListener( 'click', open );
} );

liveDome( '.menu-cart__close' ).init( function() {
	this.addEventListener( 'click', close );
} );

defer( () => {
	jQuery( document.body ).on( 'added_to_cart', open );
} );
