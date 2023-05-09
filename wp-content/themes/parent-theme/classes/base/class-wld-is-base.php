<?php

class WLD_Is_Base {
	public static function user_logged_in() : bool {
		static $is_user_logged_in;
		if ( null === $is_user_logged_in ) {
			$is_user_logged_in = is_user_logged_in();
		}

		return $is_user_logged_in;
	}

	public static function wp_login() : bool {
		static $is_wp_login;
		if ( null === $is_wp_login ) {
			$is_login_php         = 'wp-login.php' === ( $GLOBALS['pagenow'] ?? '' );
			$is_other_admin_pages = is_admin() && ! static::user_logged_in() && ! static::ajax();
			$is_wp_login          = $is_login_php || $is_other_admin_pages;
		}

		return $is_wp_login;
	}

	public static function admin_panel() : bool {
		static $is_admin_panel;
		if ( null === $is_admin_panel ) {
			$is_admin_ajax  = static::user_ajax() && static::referrer_from_admin_panel();
			$is_admin_page  = is_admin() && ! static::user_ajax() && static::user_logged_in();
			$is_admin_panel = $is_admin_ajax || $is_admin_page;
		}

		return $is_admin_panel;
	}

	public static function frontend() : bool {
		static $is_frontend;
		if ( null === $is_frontend ) {
			$is_frontend = ! static::admin_panel() && ! static::wp_login() && ! static::ajax();
		}

		return $is_frontend;
	}

	public static function ajax() : bool {
		static $is_ajax;
		if ( null === $is_ajax ) {
			$is_ajax = wp_doing_ajax();
		}

		return $is_ajax;
	}

	public static function user_ajax() : bool {
		return static::ajax() && static::user_logged_in();
	}

	/** @noinspection PhpUnused */
	public static function guest_ajax() : bool {
		return static::ajax() && ! static::user_logged_in();
	}

	public static function production() : bool {
		return 'production' === static::get_environment();
	}

	/** @noinspection PhpUnused */
	public static function staging() : bool {
		return 'staging' === static::get_environment();
	}

	public static function development() : bool {
		return 'local' === static::get_environment() || 'development' === static::get_environment();
	}

	public static function local() : bool {
		return 'local' === static::get_environment();
	}

	/** @noinspection PhpUnused */
	public static function not_production() : bool {
		return ! static::production();
	}

	public static function referrer_from_admin_panel() : bool {
		return stripos( wp_get_referer(), admin_url() ) === 0;
	}

	protected static function get_environment() : string {
		static $environment;
		if ( null === $environment ) {
			$environment = wp_get_environment_type();
		}

		return $environment;
	}

	public static function flex_page( $post = null ) : bool {
		static $flex;
		if ( null === $flex ) {
			$flex = 'templates/tpl-flexible-content.php' === get_page_template_slug( $post );
		}

		return $flex;
	}

	public static function woocommerce_page() : bool {
		static $woocommerce_page;
		if ( null === $woocommerce_page ) {
			if ( static::woocommerce_enabled() ) {
				$woocommerce_page = is_woocommerce() || is_account_page() || is_cart() || is_checkout();
			} else {
				$woocommerce_page = false;
			}
		}

		return $woocommerce_page;
	}

	public static function woocommerce_enabled() : bool {
		static $woocommerce_enabled;
		if ( null === $woocommerce_enabled ) {
			$woocommerce_enabled = function_exists( 'WC' );
		}

		return $woocommerce_enabled;
	}

	public static function breadcrumb_enabled() : bool {
		static $breadcrumb_enabled;
		if ( null === $breadcrumb_enabled ) {
			$breadcrumb_enabled = apply_filters( 'wld_is_breadcrumb_enabled', false );
			if ( ! has_filter( 'wld_is_breadcrumb_enabled' ) ) {
				$breadcrumb_enabled = function_exists( 'yoast_breadcrumb' ) && ! is_front_page() && ! is_404();
			}
		}

		return $breadcrumb_enabled;
	}
}
