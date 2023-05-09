const callbacks = [];

/**
 * @callback callbackFunction
 * @param {jQuery} $ - JQuery object
 */

/**
 * @param {callbackFunction} cb       - The function that will be called when JQuery is ready or when the DOM is ready
 * @param {boolean}          DOMReady - Do we need to wait for DOM readiness
 */
function run( cb, DOMReady ) {
	if ( DOMReady ) {
		jQuery( cb );
	} else {
		cb( jQuery );
	}
}

export function start() {
	callbacks.forEach( ( item ) => item() );
}

/**
 * @param {callbackFunction} cb       - The function that will be called when JQuery is ready or when the DOM is ready
 * @param {boolean=}         DOMReady - Do we need to wait for DOM readiness
 */
export default function defer( cb, DOMReady = false ) {
	if ( typeof jQuery !== 'undefined' ) {
		run( cb, DOMReady );
	} else {
		callbacks.push( () => run( cb, DOMReady ) );
	}
}
