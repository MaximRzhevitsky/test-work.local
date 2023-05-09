import liveDom from './live-dom';
import domReady from './dom-ready';

let googleMapsLoaded = 'not-init';

export const libraries = [];

function dependency( done, error ) {
	if ( theme.googleMapsApiKey ) {
		if ( 'not-init' === googleMapsLoaded ) {
			googleMapsLoaded = 'progress';

			const params = [ `key=${ theme.googleMapsApiKey }` ];
			if ( libraries.length ) {
				params.push( `libraries=${ libraries.join( ',' ) }` );
			}

			const script = document.createElement( 'script' );
			const src =
				'https://maps.googleapis.com/maps/api/js?' + params.join( '&' );

			script.setAttribute( 'src', src );
			script.async = true;
			script.onload = function () {
				googleMapsLoaded = 'done';
				done();
			};
			script.onerror = function () {
				googleMapsLoaded = 'error';
				error();
			};

			document.body.appendChild( script );
		} else if ( 'progress' === googleMapsLoaded ) {
			setTimeout( () => dependency( done, error ), 500 );
		} else if ( 'done' === googleMapsLoaded ) {
			done();
		} else {
			error();
		}
	} else {
		// eslint-disable-next-line no-console
		console.error(
			'There is a map on the page with no API key configured.'
		);
		error();
	}
}

export function map( selector, callback ) {
	domReady( () => {
		liveDom( selector, {
			dependency,
			firstShow: callback,
		} );
	} );
}
