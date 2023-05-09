<?php

class WLD_Front_Base {
	public static function init() : void {
		add_filter( 'the_generator', '__return_empty_string' );
		add_filter( 'excerpt_more', '__return_empty_string' );

		add_action(
			'pre_handle_404',
			array( static::class, 'page_exist_pre_handle_404' ),
			1
		);
		add_filter(
			'body_class',
			array( static::class, 'add_body_classes' )
		);
		add_action(
			'wp_head',
			array( static::class, 'the_head_meta' ),
			1
		);
		add_action(
			'init',
			array( static::class, 'clear_head' )
		);
		add_action(
			'init',
			array( static::class, 'remove_emoji' )
		);
		add_filter(
			'the_content',
			array( static::class, 'replace_trailing_slash' )
		);
		add_action(
			'wp_head',
			static function () {
				ob_start( array( static::class, 'replace_trailing_slash' ) );
			},
			PHP_INT_MIN
		);
		add_action(
			'wp_head',
			'ob_end_flush',
			PHP_INT_MAX,
			0
		);
		add_action(
			'wp_footer',
			static function () {
				ob_start( array( static::class, 'replace_trailing_slash' ) );
			},
			PHP_INT_MIN
		);
		add_action(
			'wp_footer',
			'ob_end_flush',
			PHP_INT_MAX,
			0
		);
	}

	public static function page_exist_pre_handle_404( $preempt ) {
		global $wp_query, $wp, $wp_the_query;

		if ( null === $wp_query->post && $wp->request ) {
			$page = get_page_by_path( $wp->request );
			if ( $page ) {
				// We deliberately use this feature to replace the page.
				// phpcs:ignore WordPress.WP.DiscouragedFunctions
				query_posts( 'pagename=' . $wp->request );

				// We deliberately replace the global variable with the new page.
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$wp_the_query = $wp_query;
			}
		}

		return $preempt;
	}

	public static function add_body_classes( $classes ) {
		if ( is_front_page() ) {
			$classes[] = 'home-page';
		} else {
			$classes[] = 'inner-page';
		}

		return $classes;
	}

	public static function the_head_meta() : void {
		?>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
	}

	public static function clear_head() : void {
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}

	public static function remove_emoji() : void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter(
			'tiny_mce_plugins',
			static function ( $plugins ) {
				if ( is_array( $plugins ) ) {
					return array_diff( $plugins, array( 'wpemoji' ) );
				}

				return array();
			}
		);
		add_filter( 'emoji_svg_url', '__return_empty_string' );
	}

	public static function replace_trailing_slash( string $html ) : string {
		return str_replace( '/>', '>', $html );
	}
}
