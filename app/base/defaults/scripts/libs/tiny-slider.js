import liveDom from './live-dom';
import domReady from './dom-ready';

let tns;

export function sliderDependencyCallback( done, error ) {
	if ( typeof tns === 'function' ) {
		done();
	} else {
		import( /* webpackChunkName: 'tiny-slider' */ './tiny' )
			.then( ( tns1 ) => {
				tns = tns1.default;
				done();
			} )
			.catch( error );
	}
}

export function tinySlider( className, optionsOrCallback = {} ) {
	let callback;
	if ( typeof optionsOrCallback === 'function' ) {
		callback = optionsOrCallback;
	} else {
		function defaultCallback() {
			const defaultAutoplay = optionsOrCallback.autoplay;
			const mediaQuery = window.matchMedia(
				'(prefers-reduced-motion: no-preference)'
			);

			if ( ! optionsOrCallback.container ) {
				optionsOrCallback.container = className;
			}

			if ( ! mediaQuery.matches ) {
				optionsOrCallback.autoplay = false;
			}

			mediaQuery.addEventListener( 'change', () => {
				if ( ! mediaQuery.matches ) {
					optionsOrCallback.autoplay = false;
				} else {
					optionsOrCallback.autoplay = defaultAutoplay;
				}
			} );

			tns( optionsOrCallback );
		}

		callback = defaultCallback;
	}

	domReady( () => {
		liveDom( className, {
			dependency: sliderDependencyCallback,
			firstShow: callback,
		} );
	} );
}
