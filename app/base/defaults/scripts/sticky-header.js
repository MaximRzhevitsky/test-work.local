const header = document.querySelector( '.page-header' );
if ( header ) {
	new IntersectionObserver(
		( [ e ] ) => {
			const isSticky = e.intersectionRatio === 1;

			header.classList.toggle( 'page-header_sticky', isSticky );
		},
		{
			root: document.body,
			rootMargin: '-1px 0px 0px 0px',
			threshold: [ 1 ],
		}
	).observe( header );
}
