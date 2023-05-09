const iframe = document.createElement( 'iframe' );
const srcPage = 'home.html';

let iframeWidth = 320;
iframe.src = srcPage;
iframe.width = '320px';
iframe.height = '0';
iframe.style.maxWidth = 'unset !important';
iframe.style.height = '0';
iframe.style.position = 'absolute';

document.body.appendChild( iframe );

function changeIframeWidth( sizes ) {
	const intervalId = setInterval( () => {
		if ( iframe.getBoundingClientRect().width <= 1920 ) {
			iframeWidth += 5;
			iframe.width = `${ iframeWidth }px`;
		} else {
			wldGetStyle( sizes );
			clearInterval( intervalId );
		}
	}, 1 );
}

iframe.addEventListener( 'load', function () {
	const iframeDoc = iframe.contentWindow.document;
	const blocks = iframeDoc.querySelectorAll(
		'body > main > section, body > main > div, body > footer'
	);
	const sizes = {};

	blocks.forEach( ( block ) => {
		const resizeObserver = new ResizeObserver( ( entries ) => {
			const targetId = entries[ 0 ].target.id;
			if ( targetId ) {
				if ( ! sizes[ targetId ] ) {
					sizes[ targetId ] = {
						media: {},
						lastHeight: 0,
					};
				}

				const blockHeight = entries[ 0 ].target
					.getBoundingClientRect()
					.height.toFixed( 0 );
				if ( sizes[ targetId ].lastHeight === blockHeight ) {
					return;
				}

				sizes[ targetId ].lastHeight = sizes[ targetId ].media[
					iframeWidth
				] = blockHeight;
			}
		} );

		resizeObserver.observe( block );
	} );

	changeIframeWidth( sizes );
} );

function wldGetStyle( sizes ) {
	let css = '';

	for ( const id in sizes ) {
		const tmp = [];
		Object.keys( sizes[ id ].media )
			.sort( ( a, b ) => a - b )
			.forEach( ( endpoint, index, endpoints ) => {
				const currentHeight = sizes[ id ].media[ endpoint ];
				if ( ! index ) {
					tmp.push( {
						endpoint,
						jump: 10000,
						height: currentHeight,
					} );
				}

				const nextEndpoint = endpoints[ index + 1 ];
				if ( nextEndpoint ) {
					const nextHeight = sizes[ id ].media[ nextEndpoint ];
					const jump = Math.abs( nextHeight - currentHeight );

					tmp.push( { endpoint, jump, height: currentHeight } );
				} else if ( index ) {
					tmp.push( {
						endpoint,
						jump: 10000,
						height: currentHeight,
					} );
				}
			} );

		const endpoints = tmp
			.sort( ( a, b ) => b.jump - a.jump )
			.slice( 0, 10 )
			.sort( ( a, b ) => parseInt( a.endpoint ) - parseInt( b.endpoint ) )
			.filter( ( current, index, array ) => {
				const next = array[ index + 1 ];

				return ! next || Math.abs( next.height - current.height ) > 10;
			} )
			.map( ( a ) => a.endpoint );

		const firstEndpoint = endpoints.shift();
		if ( firstEndpoint ) {
			const height = sizes[ id ].media[ firstEndpoint ];
			css += `#${ id }{content-visibility:auto;contain-intrinsic-height:auto ${ height }px}`;
		}

		endpoints.forEach( ( endpoint ) => {
			const height = sizes[ id ].media[ endpoint ];
			css += `@media all and (min-width: ${ endpoint }px){#${ id }{contain-intrinsic-height:auto ${ height }px}}`;
		} );
	}

	console.log( css );
}
