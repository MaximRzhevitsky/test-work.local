<?php

class WLD_AccessiBe_Plugin_Base {
	public static function init() : void {
		if ( class_exists( 'Accessibe' ) ) {
			add_action(
				'wp_footer',
				array( static::class, 'render_js_in_footer' )
			);
			remove_action(
				'wp_footer',
				array( 'Accessibe', 'render_js_in_footer' )
			);
		}
	}

	public static function render_js_in_footer() : void {
		ob_start();

		Accessibe::render_js_in_footer();

		// We ignore it because we get the code itself from the plugin and cannot be held responsible for it.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo preg_replace(
			array(
				'/<script>/',
				'/\(\)\);<\/script>/',
			),
			array(
				'<script defer>themeUserActiveAction',
				',6000,!!window.localStorage.getItem("acsbState"));</script>',
			),
			preg_replace_callback(
				'/(?<=src=\')[^\']+(?=\')/',
				static function ( $matches ) {
					return WLD_Local_External_Scripts::local( $matches[0], 'accessibe.js' );
				},
				ob_get_clean()
			)
		);
	}
}
