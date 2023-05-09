export default function domReady( callback ) {
	if ( /complete|interactive|loaded/.test( document.readyState ) ) {
		callback();
	} else {
		document.addEventListener( 'DOMContentLoaded', callback, false );
	}
}
