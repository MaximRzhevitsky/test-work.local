const pattern =
	/(?:http|https)?:?\/?\/?(?:www\.)?(?:player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|video\/|)([a-z-0-9]+)(?:|\/\?)/;

export function getVimeoVideoIdFromUrl( url ) {
	const found = url.match( pattern );

	if ( found ) {
		return found[ 1 ];
	}

	return 0;
}
