if ( window.location.hash ) {
	window.themeNedToHash = window.location.hash;
	window.history.replaceState(
		{},
		document.title,
		window.location.toString().replace( window.location.hash, '' )
	);
}
