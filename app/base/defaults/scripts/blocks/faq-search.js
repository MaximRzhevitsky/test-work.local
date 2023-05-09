/**
 * todo: Rewrite to vanilla JS and move the connection to main + liveDom.
 */
import liveDom from '../libs/live-dom';

$.expr[':'].icontains = function (el, i, m) {
	const search = m[3];
	if (!search) return false;

	const pattern = new RegExp(search, 'i');
	return pattern.test($(el).text());
};

liveDom('.faq').init(function () {
	const $section = $(this);
	const $scrollTo = $('.tabs__tablist');
	const $elem = $section.find('.accordion');

	$('.form__submit', $section).on('click', (e) => {
		e.preventDefault();
		const value = $('.form__input', $section).val();

		if (value) {
			$elem.hide().filter(`:icontains(${value})`).show();
			$('html,body').animate(
				{
					scrollTop:
						$scrollTo.offset().top -
						$('.page-header').outerHeight() -
						30,
				},
				1000
			);
		} else {
			$elem.show();
		}
	});
});
