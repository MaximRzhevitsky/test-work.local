jQuery(($) => {
	let updateTimeout = false;

	function trigger($qty) {
		$qty.trigger('change');
		updateTimeout = false;
		$('[name="update_cart"]').trigger('click');
	}

	function update() {
		if (updateTimeout !== false) {
			clearTimeout(updateTimeout);
		}

		const $btn = $(this);
		const $qty = $btn.closest('.quantity').find('.qty');
		const value = parseInt($qty.val(), 10);
		const min = $qty.attr('min') ? parseInt($qty.attr('min'), 10) : 1;
		const max = $qty.attr('max') ? parseInt($qty.attr('max'), 10) : 999;

		if ($btn.hasClass('minus')) {
			if (value > min && value > 1) {
				$qty.val(value - 1);
			} else {
				$btn.attr('disabled', true);
			}
			$btn.closest('.quantity').find('.plus').attr('disabled', false);
		} else {
			if (value < max) {
				$qty.val(value + 1);
			} else {
				$btn.attr('disabled', true);
			}
			$btn.closest('.quantity').find('.minus').attr('disabled', false);
		}

		updateTimeout = setTimeout(trigger, 1000, $qty);
	}

	function change() {
		const $qty = $(this);
		const max = $qty.attr('max') ? $qty.attr('max') : '999';

		if (parseInt($qty.val(), 10) === 0) {
			$qty.val(1);
		}

		if ($qty.val().length > max.length) {
			$qty.val($qty.val().slice(0, max.length));
		} else if (parseInt($qty.val(), 10) > parseInt(max, 10)) {
			$qty.val(max);
		}
	}

	$(document.body)
		.on('click', '.quantity .qty-btn:not([disabled])', update)
		.on('input', '.quantity .qty', change);
});
