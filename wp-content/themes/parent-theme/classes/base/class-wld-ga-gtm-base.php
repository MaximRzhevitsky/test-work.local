<?php
/**
 * phpcs:disable WordPress.WP.EnqueuedResources
 *
 * @noinspection ES6ConvertVarToLetConst, JSUnresolvedVariable, EqualityComparisonWithCoercionJS,
 *               JSSuspiciousEqPlus, PhpFormatFunctionParametersMismatchInspection, HtmlUnknownTarget
 */

class WLD_GA_GTM_Base {
	private static string $ga_id   = '';
	private static string $gtag_id = '';
	private static string $gtm_id  = '';

	public static function init() : void {
		add_action(
			'acf/init',
			array( static::class, 'acf_init' )
		);
	}

	public static function acf_init() : void {
		if ( 'production' === wp_get_environment_type() ) {
			static::$ga_id   = (string) get_field( 'wld_api_ga_id', 'options' );
			static::$gtag_id = (string) get_field( 'wld_api_gtag_id', 'options' );
			static::$gtm_id  = (string) get_field( 'wld_api_gtm_id', 'options' );

			if ( static::$ga_id || static::$gtag_id || static::$gtm_id ) {
				add_action( 'wp_head', array( static::class, 'head' ), 1 );
			}
			if ( static::$gtm_id ) {
				add_action( 'wp_body_open', array( static::class, 'body' ), 1 );
			}
		}
	}

	public static function head() : void {
		$function = 'themeUserActiveActionLoadScript';
		$param    = 4000;

		if ( static::$ga_id ) {
			// Google Analytics https://developers.google.com/analytics/devguides/collection/analyticsjs
			printf(
				"
				<script id=\"ga-inline-js\">
					window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments);};ga.l=+new Date;
					ga('create','%s','auto');
					ga('send','pageview');
					%s('%s', 'ga-js', '%s');
				</script>",
				esc_js( static::$ga_id ),
				esc_js( $function ),
				esc_js(
					esc_url(
						WLD_Local_External_Scripts::local(
							'https://www.google-analytics.com/analytics.js',
							'google-analytics.js'
						)
					)
				),
				esc_js( $param )
			);
		}

		if ( static::$gtag_id ) {
			// Google tag https://developers.google.com/gtagjs/devguide/snippet
			printf(
				"
				<script id=\"gtag-inline-js\">
					%s('%s', 'gtag-js', '%s');
					window.dataLayer = window.dataLayer || [];
					function gtag(){dataLayer.push(arguments);}
					gtag('js',new Date());
					gtag('config','%s');
				</script>",
				esc_js( $function ),
				esc_js(
					esc_url(
						WLD_Local_External_Scripts::local(
							'https://www.googletagmanager.com/gtag/js?id=' . static::$gtag_id,
							'google-tag.js'
						)
					)
				),
				esc_js( $param ),
				esc_js( static::$gtag_id )
			);
		}

		if ( static::$gtm_id ) {
			// Google Tag Manager https://developers.google.com/tag-manager/quickstart
			printf(
				"
				<script id=\"gtm-inline-js\">
					(function(w,l){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});})(window,'dataLayer');
					%s('%s', 'gtm-js', '%s');
				</script>",
				esc_js( $function ),
				esc_js(
					esc_url(
						WLD_Local_External_Scripts::local(
							'https://www.googletagmanager.com/gtm.js?id=' . static::$gtm_id,
							'google-tag-manager.js'
						)
					)
				),
				esc_js( $param )
			);
		}
	}

	public static function body() : void {
		printf(
			'
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s"
				height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			',
			esc_attr( static::$gtm_id )
		);
	}
}
