jQuery(($) => {
	const $header = $('.page-header');
	const fix = 18;

	function maybeNeedClick(id, link) {
		let $links = $(`[href="#${id}"]`);
		if (link) {
			$links = $links.not(link);
		}

		if ($links.length) {
			$links.eq(0).trigger('click', [true]);

			return true;
		}

		return false;
	}

	function scrollTo($target) {
		if ($target.length) {
			const offset = $target.offset().top - fix;
			$('html,body').animate(
				{ scrollTop: offset - $header.outerHeight() },
				{
					step(now, fx) {
						fx.end = offset - $header.outerHeight();
					},
				}
			);
		}
	}

	function maybeScrollTo(hashOrIdOrName, event, runMaybeNeedClick) {
		if (hashOrIdOrName.startsWith('#!')) {
			return;
		}

		if (hashOrIdOrName.startsWith('#')) {
			hashOrIdOrName = hashOrIdOrName.slice(1);
		}

		if (
			runMaybeNeedClick !== true &&
			maybeNeedClick(hashOrIdOrName, event && event.currentTarget)
		) {
			return;
		}

		let $target = $(`#${hashOrIdOrName}`);
		if ($target.length === 0) {
			$target = $(`[name=${hashOrIdOrName}]`);
		}

		scrollTo($target);
	}

	$.fn.wldScrollTo = function () {
		if (this && this.length) {
			scrollTo($(this).eq(0));
		}

		return this;
	};

	if (window.themeNedToHash) {
		maybeScrollTo(window.themeNedToHash);
	}

	$(document).on(
		'click',
		'a[href*="#"]:not([href="#"]):not([href*="popup"]):not(.popup-link):not(.skip-link)',
		function (e, runMaybeNeedClick) {
			if ($(this).parent().hasClass('popup-link')) {
				return;
			}

			const windowPathname = window.location.pathname.replace(/^\//, '');
			const thisPathname = this.pathname.replace(/^\//, '');

			if (
				windowPathname === thisPathname &&
				window.location.hostname === this.hostname
			) {
				if (e) {
					e.preventDefault();
				}

				maybeScrollTo(this.hash, e, runMaybeNeedClick);
			}
		}
	);
});
