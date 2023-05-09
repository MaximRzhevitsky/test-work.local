jQuery(($) => {
	const $header = $('header.header');

	let count = 0;

	function init() {
		if (typeof $.scroll_to_notices === 'function') {
			$.scroll_to_notices = ($scrollElement) => {
				if ($scrollElement.length) {
					$('html, body').animate(
						{
							scrollTop:
								$scrollElement.offset().top -
								( 100 + $header.height() ),
						},
						1000
					);
				}
			};
		} else if (count < 20) {
			count++;
			setTimeout(init, 500);
		}
	}

	if ($header.length) {
		init();
	}
});
