import liveDom from './libs/live-dom';

liveDom(
	'[href^="https://www.youtube.com/watch?v="], [href^="https://vimeo.com/video/"]'
).firstShow( function () {
	if ( this.getElementsByTagName( 'img' ).length > 0 ) {
		this.classList.add( 'with-btn-play' );
	}
} );
