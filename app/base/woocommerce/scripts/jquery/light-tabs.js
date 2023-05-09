(function ($) {
	$.fn.lightTabs = function () {
		const createTabs = function () {
			const tabs = this;
			const showPage = function (i) {
				$(tabs).find('.tab').hide().eq(i).show();
				$(tabs)
					.find('.tabs-nav')
					.each((index, element) => {
						$(element)
							.find('li')
							.removeClass('active')
							.eq(i)
							.addClass('active');
					});
			};

			// Initialize tabs and show first tab content
			showPage(0);

			// Add data attributes with tab-content index
			$(tabs)
				.find('.tabs-nav')
				.each((index, element) => {
					$(element)
						.find('li')
						.each((liIndex, liElement) => {
							$(liElement).attr('data-page', liIndex);
						});
				});

			// Add event listener on each tab
			$(tabs)
				.find('.tabs-nav li')
				.on('click', function () {
					showPage(parseInt($(this).attr('data-page'), 10));
				});

			// select tab by hash
			const { hash } = window.location;
			const $hashTab = $(`.tabs-nav li[data-hash="${hash}"]`);
			if (hash && $hashTab.length) {
				$hashTab.trigger('click');
			}
		};

		return this.each(createTabs);
	};
})(jQuery);
