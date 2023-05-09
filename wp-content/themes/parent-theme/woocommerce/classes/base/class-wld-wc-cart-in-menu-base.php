<?php

class WLD_WC_Cart_In_Menu_Base {
	protected static bool $show_total = false;
	protected static bool $show_count = false;

	public static function init( array $args = array() ) : void {
		$args = array_merge(
			array(
				'show_total' => false,
				'show_count' => true,
			),
			$args
		);

		static::$show_total = $args['show_total'];
		static::$show_count = $args['show_count'];

		add_action(
			'wp_footer',
			array( static::class, 'the_mini_cart' ),
			10,
			2
		);
		add_filter(
			'woocommerce_add_to_cart_fragments',
			array( static::class, 'add_fragments' )
		);
		add_filter(
			'nav_menu_item_title',
			array( static::class, 'add_span' ),
			10,
			2
		);
		add_filter(
			'woocommerce_get_checkout_url',
			array( static::class, 'set_checkout_url' )
		);
	}

	public static function add_fragments( array $fragments ) : array {
		if ( static::$show_total ) {
			$fragments['.cart-total'] = static::get_span_total();
		}

		if ( static::$show_count ) {
			$fragments['.cart-count'] = static::get_span_count();
		}

		if ( isset( $_GET['wc-ajax'] ) && 'remove_from_cart' === $_GET['wc-ajax'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$fragments['.menu-cart'] = static::get_html(
				'cart/menu-cart.php',
				array(
					'hidden' => false,
				),
			);
		} else {
			$fragments['.menu-cart'] = static::get_html(
				'cart/menu-cart.php',
				array(
					'hidden' => true,
				),
			);
		}

		if (
			true === wc_string_to_bool( $_POST['is_menu_cart'] ?? '' ) && // phpcs:ignore
			str_starts_with( wp_get_referer(), wc_get_cart_url() )
		) {
			WC()->cart->calculate_totals();
			$cart                   = '.woocommerce-cart .default-page .inner > .woocommerce';
			$fragments[ $cart ]     = '';
			$request_url            = $_SERVER['REQUEST_URI'];
			$_SERVER['REQUEST_URI'] = wp_get_referer();

			$fragments[ $cart ] .= '<div class="woocommerce">';
			if ( WC()->cart->is_empty() ) {
				$fragments[ $cart ] .= static::get_html( 'cart/cart-empty.php' );
			} else {
				$fragments[ $cart ] .= static::get_html( 'cart/cart.php' );
			}
			$fragments[ $cart ] .= '</div>';

			$_SERVER['REQUEST_URI'] = $request_url;
		}

		if ( isset( $fragments['div.widget_shopping_cart_content'] ) ) {
			unset( $fragments['div.widget_shopping_cart_content'] );
		}

		return $fragments;
	}

	public static function add_span( string $title, $item ) : string {
		if ( static::$show_total && in_array( 'cart', $item->classes, true ) ) {
			$title .= static::get_span_total( true );
		}

		if ( static::$show_count && in_array( 'cart', $item->classes, true ) ) {
			$title .= static::get_span_count( true );
		}

		return $title;
	}

	public static function get_span_total( bool $empty = false ) : string {
		$total = $empty ? '' : wc_price( WC()->cart->get_cart_contents_total() );

		return ' <span class="cart-total">' . $total . '</span>';
	}

	public static function get_span_count( bool $empty = false ) : string {
		return ' <span class="cart-count">' . ( $empty ? '' : WC()->cart->get_cart_contents_count() ) . '</span>';
	}

	public static function the_span_count() : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo static::get_span_count();
	}

	public static function the_mini_cart() : void {
		echo '<aside class="menu-cart" aria-hidden="true"></aside>';
	}

	public static function the_open_button() : void {
		$url = wc_get_cart_url();
		?>
		<a class="menu-cart-link menu-cart-link_hide-on-desktop"
		   href="<?php echo esc_url( $url ); ?>"
		   role="button"
		   aria-expanded="false">
			<?php esc_html_e( 'Open Cart', 'theme' ); ?>
			<?php static::the_span_count(); ?>
		</a>
		<?php
	}

	public static function set_checkout_url( $url ) : string {
		if ( is_user_logged_in() || is_account_page() ) {
			return $url;
		}

		if ( doing_action( 'woocommerce_widget_shopping_cart_buttons' ) ) {
			$myaccount_page_id = wc_get_page_id( 'myaccount' );
			if ( $myaccount_page_id > 0 ) {
				$url = add_query_arg(
					array(
						'_wp_http_referer' => $url,
					),
					wc_get_page_permalink( 'myaccount' )
				);
			}
		}

		return $url;
	}

	protected static function get_html( string $template_name, array $args = array() ) : string {
		return wc_get_template_html( $template_name, $args );
	}
}
