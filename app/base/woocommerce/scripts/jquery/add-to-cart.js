(function ($) {
	$.fn.wldAddToCart = function () {
		const $form = this;
		const $btn = $form.find('[type="submit"]');
		const $qty = $form.find('[name="quantity"]');
		const $sku = $form.find('.sku');
		const $variation = $form.find('[name="variation_id"]');

		$btn.attr('data-quantity', $qty.length ? $qty.val() : 1)
			.attr(
				'data-product_id',
				$variation.length ? $variation.val() : $btn.val()
			)
			.attr('data-product_sku', $sku.length ? $sku.text() : '')
			.addClass('ajax_add_to_cart add_to_cart_button')
			.on('click', (e) => {
				if ($btn.hasClass('disabled')) {
					e.preventDefault();
					e.stopPropagation();
				}
			});

		$qty.on('change', () => $btn.attr('data-quantity', $qty.val()));
		$variation.on('change', () =>
			$btn.attr('data-product_id', $variation.val())
		);
	};
})(jQuery);
