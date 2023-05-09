import liveDom from '../../libs/live-dom';

export function popupDependencyCallback( done, error ) {
	if ( typeof $.magnificPopup === 'function' ) {
		done();
	} else {
		import( /* webpackChunkName: 'magnific-popup' */ './magnific-popup' )
			.then( () => {
				setTimeout( done, 1000 );
			} )
			.catch( error );
	}
}

export function popup( className, optionsOrCallback = {} ) {
	let callback;
	if ( typeof optionsOrCallback === 'function' ) {
		callback = optionsOrCallback;
	} else {
		/**
		 * @this HTMLElement
		 */
		function defaultCallback() {
			$( this ).magnificPopup( optionsOrCallback );
		}

		callback = defaultCallback;
	}

	liveDom( className )
		.dependency( popupDependencyCallback )
		.firstShow( callback );
}
