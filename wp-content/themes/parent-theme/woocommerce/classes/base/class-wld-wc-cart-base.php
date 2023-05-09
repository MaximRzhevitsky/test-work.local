<?php

class WLD_WC_Cart_Base {
	public static function init() : void {
		add_action(
			'woocommerce_before_cart_table',
			array( static::class, 'add_title' )
		);

		add_action(
			'woocommerce_before_shipping_calculator',
			static function () {
				ob_start();
			}
		);

		add_action(
			'woocommerce_after_shipping_calculator',
			array( static::class, 'add_address_field_in_shipping_calculator' )
		);

		add_action(
			'woocommerce_calculated_shipping',
			array( static::class, 'save_address_field_in_shipping_calculator' )
		);

		add_action(
			'woocommerce_after_cart_totals',
			array( static::class, 'payment_accepted_icons' )
		);

		remove_action( 'woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal' );
		add_action(
			'woocommerce_widget_shopping_cart_total',
			array( static::class, 'widget_shopping_cart_total' )
		);
	}

	public static function widget_shopping_cart_total() {
		$price_kses = array(
			'span'   => array(
				'class' => array(),
			),
			'p'      => array(),
			'strong' => array(),
			'bdi'    => array(),
		);
		?>
		<span class="menu-cart__subtotal">
			<strong class="menu-cart__sub-title"><?php esc_html_e( 'Subtotal', 'theme' ); ?></strong>
			<span class="woocommerce-Price-amount amount">
				<?php echo wp_kses( WC()->cart->get_cart_subtotal(), $price_kses ); ?>
			</span>
		</span>
		<span class="menu-cart__tax">
			<strong class="menu-cart__sub-title"><?php esc_html_e( 'Tax', 'theme' ); ?></strong>
			<span class="woocommerce-Price-amount amount"><?php wc_cart_totals_taxes_total_html(); ?></span>
		</span>
		<span class="menu-cart__total">
			<strong class="menu-cart__sub-title"><?php esc_html_e( 'Total', 'theme' ); ?></strong>
			<span class="woocommerce-Price-amount amount">
				<?php echo wp_kses( WC()->cart->get_cart_subtotal(), $price_kses ); ?>
			</span>
		</span>
		<?php
	}

	public static function add_title() : void {
		echo '<h2>' . esc_html__( 'My Cart', 'theme' ) . '</h2>';
	}

	public static function add_address_field_in_shipping_calculator() : void {
		$value       = WC()->customer->get_shipping_address_1();
		$placeholder = __( 'House number and street name', 'theme' );

		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo str_replace(
			'<p><button type="submit"',
			'
			<p class="form-row form-row-wide" id="calc_shipping_address_1_field">
				<input type="text" class="input-text " value="' . $value . '" placeholder="' . $placeholder . '"
					name="calc_shipping_address_1" id="calc_shipping_address_1">
			</p>
			<p><button type="submit"',
			ob_get_clean()
		);
	}

	public static function save_address_field_in_shipping_calculator() : void {
		$address_1 = wc_clean( wp_unslash( $_POST['calc_shipping_address_1'] ?? '' ) ); // phpcs:ignore
		if ( $address_1 ) {
			WC()->customer->set_shipping_address_1( $address_1 );
			if ( ! WC()->customer->get_billing_first_name() ) {
				WC()->customer->set_billing_address_1( $address_1 );
			}
		}

		WC()->customer->save();
	}

	public static function payment_accepted_icons() : void {
		?>
		<?php if ( wld_has( 'wld_accepting_payment_systems' ) ) : ?>
			<div class="accepting-payment-systems">
				<p><?php esc_html_e( 'We Accept:', 'theme' ); ?></p>
				<div class="block-images">
					<?php while ( wld_loop( 'wld_accepting_payment_systems' ) ) : ?>
						<?php wld_the( 'image' ); ?>
					<?php endwhile; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php
	}
}
