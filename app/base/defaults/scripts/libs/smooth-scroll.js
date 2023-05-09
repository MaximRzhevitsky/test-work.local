export function maybeNeedClick( id, link ) {
	let links = Array.from( document.querySelectorAll( `[href="#${ id }"]` ) );
	if ( link ) {
		links = links.filter( ( node ) => ! node.isEqualNode( link ) );
	}

	if ( links.length ) {
		const event = new Event( 'click', {
			detail: { runMaybeNeedClick: true },
		} );

		links[ 0 ].dispatchEvent( event );

		return true;
	}

	return false;
}

export function maybeScrollTo( hashOrIdOrName, event ) {
	if ( hashOrIdOrName.startsWith( '#!' ) ) {
		return;
	}

	if ( hashOrIdOrName.startsWith( '#' ) ) {
		hashOrIdOrName = hashOrIdOrName.slice( 1 );
	}

	if (
		event &&
		event?.detail?.runMaybeNeedClick !== true &&
		maybeNeedClick( hashOrIdOrName, event.currentTarget )
	) {
		return;
	}

	let target = document.getElementById( hashOrIdOrName );
	if ( ! target ) {
		target =
			document.getElementsByName( `[name=${ hashOrIdOrName }]` )[ 0 ] ||
			null;
	}

	if ( target ) {
		scrollToElement( target ).then();
	}
}

export function smoothScroll( target, fixY ) {
	let start;
	let previousTimeStamp;
	let done = false;

	const header = document.querySelector( '.page-header' );
	const headerInitHeight = header ? header.getBoundingClientRect().height : 0;
	const targetInitY =
		target.getBoundingClientRect().top - headerInitHeight - fixY;
	const duration = targetInitY > 1000 ? 1000 : 500;
	const stepInitY = targetInitY / ( duration / 60 );

	return new Promise( ( resolve ) => {
		const step = ( timestamp ) => {
			if ( start === undefined ) {
				start = timestamp;
			}

			const elapsed = timestamp - start;
			const headerHeight = header
				? header.getBoundingClientRect().height
				: 0;
			const targetY =
				target.getBoundingClientRect().top - headerHeight - fixY;

			if ( previousTimeStamp !== timestamp ) {
				const stepY =
					targetY < 0
						? Math.max( stepInitY, targetY )
						: Math.min( stepInitY, targetY );
				if ( stepY === targetY ) {
					done = true;
				}

				window.scrollBy( 0, stepY );
			}

			if ( elapsed < duration || done ) {
				previousTimeStamp = timestamp;
				if ( ! done ) {
					requestAnimationFrame( step );
				} else {
					resolve( target );
				}
			} else {
				window.scrollBy( 0, targetY );
				resolve( target );
			}
		};

		requestAnimationFrame( step );
	} );
}

export function restoreFocus( target ) {
	const tabindex = target.getAttribute( 'tabindex' );
	target.setAttribute( 'tabindex', '0' );
	target.focus();

	if ( tabindex ) {
		target.setAttribute( 'tabindex', tabindex );
	} else {
		target.removeAttribute( 'tabindex' );
	}
}

export function scrollToElement( target, fixY = 18 ) {
	return smoothScroll( target, fixY ).then( restoreFocus );
}
