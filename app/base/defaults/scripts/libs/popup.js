import liveDom from './live-dom';
import domReady from './dom-ready';
import { getVimeoVideoIdFromUrl } from './get-vimeo-video-id-from-url';
import { getYouTubeVideoIdFromUrl } from './get-you-tube-video-id-from-url';

const isBrowserNotSupportDialog = window.HTMLDialogElement === undefined;

function openPopup( dialog, modal ) {
	if ( true === modal ) {
		dialog.showModal();
	} else {
		dialog.show();
	}
}

function createDialog( btn, isModal ) {
	let src = btn.getAttribute( 'href' );

	/*
	todo: 1) It is not very optimal to check each link twice using regular expressions.
	todo: 2) Links can have GET parameters, such as playback time, they need to somehow add a final SRC.
	 */
	const youTubeId = getYouTubeVideoIdFromUrl( src );
	if ( youTubeId ) {
		src = `https://www.youtube.com/embed/${ youTubeId }/?autoplay=1&rel=0`;
	} else {
		const vimeoId = getVimeoVideoIdFromUrl( src );
		if ( vimeoId ) {
			src = `https://player.vimeo.com/video/${ vimeoId }/?title=0&byline=0&portrait=0`;
		}
	}

	const dialog = document.createElement( 'dialog' );
	dialog.setAttribute( 'id', 'popup-iframe' );
	dialog.classList.add( 'popup' );
	dialog.innerHTML = `
		<div class="popup__content">
		<iframe src="${ src }"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
				allowfullscreen></iframe>
		</div>
		<button class="popup__close-btn" type="button" data-close-popup-id="popup-iframe"><span>Close</span></button>
	`;
	document.body.appendChild( dialog );
	if ( isBrowserNotSupportDialog ) {
		setTimeout( () => openPopup( dialog, isModal ), 500 );
	} else {
		openPopup( dialog, isModal );
	}
}

if ( isBrowserNotSupportDialog ) {
	( async () => {
		import( '../../styles/parts/dialog-polyfill.css' );
		const { default: polyfill } = await import( 'dialog-polyfill' );
		liveDom( 'dialog' ).init( function () {
			polyfill.registerDialog( this );
		} );
	} )();
}

domReady( () => {
	liveDom( '.popup__close-btn' ).init( function () {
		this.addEventListener( 'click', function () {
			const popupId = this.getAttribute( 'data-close-popup-id' );
			const dialog = document.getElementById( popupId );

			dialog.close();

			if ( popupId.includes( 'iframe' ) ) {
				dialog.remove();
			}
		} );
	} );
} );

export const selector =
	'[href^="https://www.youtube.com/watch?v="],' +
	':not([href^="#"])' +
	'[href^="https://vimeo.com/video/"]' +
	'[href^="https://vimeo.com/"]' +
	':not([href^="javascript:void(0)"])' +
	':not(.target-self)';

export function popup( className, options = { type: 'inline', modal: true } ) {
	domReady( () => {
		liveDom( className ).init( function () {
			this.addEventListener( 'click', function ( e ) {
				if ( 'iframe' === options.type ) {
					e.preventDefault();
					createDialog( this, options.modal );
				} else {
					const id = this.getAttribute( 'href' )
						? this.getAttribute( 'href' ).slice( 1 )
						: this.getAttribute( 'data-popup-id' );

					openPopup( document.getElementById( id ), options.modal );
				}
			} );
		} );
	} );
}
