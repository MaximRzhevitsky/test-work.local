import liveDom from './libs/live-dom';

export const selector =
	'a' +
	':not([target])' +
	':not([href^="#"])' +
	':not([href^="tel:"])' +
	':not([href^="mailto:"])' +
	':not([href^="javascript:void(0)"])' +
	':not(.target-self)';

liveDom( selector ).init( function () {
	const link = this;
	const isExternal = this.hostname !== window.location.hostname;
	const isFile = this.pathname.indexOf( '.' ) !== -1;

	if ( isExternal || isFile ) {
		link.setAttribute( 'target', '_blank' );
		if ( ! link.getAttribute( 'rel' ) ) {
			link.setAttribute( 'rel', 'noopener' );
		}
	}
} );
