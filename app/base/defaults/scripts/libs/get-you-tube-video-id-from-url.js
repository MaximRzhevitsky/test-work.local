const pattern =
	/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?vi?=|&vi?=))([^#&?]*).*/;

export function getYouTubeVideoIdFromUrl( url ) {
	const found = url.match( pattern );

	if ( found ) {
		return found[ 1 ];
	}

	return 0;
}
