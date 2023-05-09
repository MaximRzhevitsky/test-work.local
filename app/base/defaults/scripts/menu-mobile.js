import liveDom from './libs/live-dom';
import accessibilitySetModalFocus from './libs/accessibility-set-modal-focus';

liveDom( '.open-mobile-menu-button' ).firstShow( function () {
	let isOpen = false;

	const openButton = this;
	const mobileMenu = document.querySelector( '.mobile-menu' );
	const animationDuration = 400;

	function setModalFocus( e ) {
		accessibilitySetModalFocus( e, mobileMenu, openButton );
	}

	function toggleMenu() {
		if ( isOpen ) {
			openButton.setAttribute( 'aria-expanded', 'false' );
			mobileMenu.setAttribute( 'aria-hidden', 'true' );
			document.body.removeEventListener( 'keydown', setModalFocus );
			openButton.focus();

			setTimeout( () => {
				document.body.style.paddingRight = '';
				document.body.style.overflow = '';
			}, animationDuration );
		} else {
			openButton.setAttribute( 'aria-expanded', 'true' );
			mobileMenu.setAttribute( 'aria-hidden', 'false' );
			document.body.addEventListener( 'keydown', setModalFocus );
		}
		isOpen = ! isOpen;
	}

	openButton.addEventListener( 'click', toggleMenu );
} );
