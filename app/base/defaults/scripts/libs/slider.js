import liveDom from './live-dom';

export function sliderDependencyCallback(done, error) {
	if (typeof $.slick === 'function') {
		done();
	} else {
		import(/* webpackChunkName: 'slick' */ './slick')
			.then(done)
			.catch(error);
	}
}

export function slider(className, optionsOrCallback = {}) {
	let callback;
	let autoplay = false;
	if (typeof optionsOrCallback === 'function') {
		callback = optionsOrCallback;
	} else {
		/**
		 * @this HTMLElement
		 */
		function defaultCallback() {
			$(this).slick(Object.assign(optionsOrCallback));
		}

		callback = defaultCallback;
		if (optionsOrCallback.autoplay) {
			autoplay = true;
		}
	}

	const sliderLiveDom = liveDom(className);

	sliderLiveDom
		.dependency(sliderDependencyCallback)
		.firstShow(callback, false);

	if (autoplay) {
		sliderLiveDom
			.show(function () {
				$(this).slick('slickPlay');
			})
			.hide(function () {
				$(this).slick('slickPause');
			});
	}

	sliderLiveDom.start();
}
