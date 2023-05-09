jQuery(($) => {
	const selector =
		'a' +
		':not([href^="#"])' +
		':not([href^="tel:"])' +
		':not([href^="mailto:"])' +
		':not([href^="javascript:void(0)"])' +
		':not(.target-self)';

	$(selector)
		.filter(function () {
			const isExternal = this.hostname !== window.location.hostname;
			const isFile =
				this.pathname.indexOf('.') !== -1 &&
				this.pathname.indexOf('.php') === -1;
			return isExternal || isFile;
		})
		.attr({
			target: '_blank',
			rel: 'noopener',
		});
});
