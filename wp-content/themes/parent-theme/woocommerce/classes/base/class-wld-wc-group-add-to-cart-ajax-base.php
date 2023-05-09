<?php

class WLD_WC_Group_Add_To_Cart_AJAX_Base {
	protected static bool $is_enqueue = false;

	public static function init() : void {
		add_action(
			'woocommerce_after_add_to_cart_form',
			array( static::class, 'enqueue_scripts' )
		);
	}

	public static function enqueue_scripts() : void {
		global $product;

		if ( static::$is_enqueue || empty( $product ) || ! $product->is_type( 'grouped' ) ) {
			return;
		}

		static::$is_enqueue = true;

		wp_enqueue_script( 'wp-api-fetch' );

		wp_add_inline_script(
			'wp-api-fetch',
			'const GROUP_ADD_TO_CART_WC_NONCE = "' . esc_js( wp_create_nonce( 'wc_store_api' ) ) . '";'
		);

		/** @noinspection JSUnresolvedVariable */
		wp_add_inline_script(
			'wp-api-fetch',
			/** @lang JavaScript */ <<<JS
			( function() {
				const forms = document.querySelectorAll('.cart.grouped_form');

				forms.forEach((form)=>{
					form.addEventListener( 'submit', async (e) => {
						e.preventDefault();

						const buttons = form.querySelectorAll('.add_to_cart_button');
						const quantityInputs = Array.from(form.querySelectorAll('.qty'));
						const errorMessages = [];
						let hasSuccess = false;

						buttons.forEach((button) => {
							button.classList.remove('added');
							button.classList.add('loading');
						});

						for (const quantityInput of quantityInputs) {
							const productId = quantityInput.name.replace(/[^0-9]/g, '');
							const quantity = quantityInput.value;

							if (productId > 0 && quantity > 0) {
								try {
								    await addItem(productId, quantity);
									hasSuccess = true;
									console.info('hasSuccess',hasSuccess );
								} catch (error) {
									errorMessages.push(error.message)
								}
							}
						}

						errorMessages.forEach((errorMessage) => alert(errorMessage));

						if ( hasSuccess ) {
							refreshFragments( buttons ).then( () => {
								buttons.forEach((button) => button.classList.remove('loading'));
							});
						} else {
							buttons.forEach((button) => button.classList.remove('loading'));
						}
					} );
				});

				async function addItem(productId, quantity) {
					return wp.apiFetch( {
						path: '/wc/store/cart/add-item',
						method: 'POST',
						headers: {
							'X-WC-Store-API-Nonce': GROUP_ADD_TO_CART_WC_NONCE
						},
						data: {
							id: productId,
							quantity
						}
					} );
				}

				async function refreshFragments( buttons ) {
					return new Promise((resolve, reject) => {
						 if ( typeof $ !== 'undefined' && typeof wc_cart_fragments_params !== 'undefined' ) {
							 $.post( {
								url: wc_cart_fragments_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' ),
								data: {
									time: new Date().getTime()
								},
								timeout: wc_cart_fragments_params.request_timeout,
								success: function( data ) {
									if ( data && data.fragments ) {
										 $( document.body ).trigger(
											 'added_to_cart',
											 [ data.fragments, data.cart_hash, $( buttons[0] ) ]
										 );
										resolve();

										return;
									}

									reject();
								},
								error: function() {
									$( document.body ).trigger( 'wc_fragments_ajax_error' );
									reject();
								}
							} );
						 }
					});
				}
			} )();
JS
		);
	}
}
