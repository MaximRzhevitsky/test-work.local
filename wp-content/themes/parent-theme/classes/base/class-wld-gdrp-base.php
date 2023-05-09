<?php

// todo: I think it's worth merging all style files into one, since they are not large,
// and we still need them on all pages, and it's better to make one request.

class WLD_GDRP_Base {
	public static function init() : void {
		if ( ! class_exists( 'Cookie_Law_Info' ) || is_admin() ) {
			return;
		}

		add_action(
			'wp_head',
			array( static::class, 'the_style' ),
			3
		);
		add_action(
			'wp_enqueue_scripts',
			array( static::class, 'move_to_footer' )
		);
		add_action(
			'wp_enqueue_scripts',
			array( static::class, 'dequeue_styles' )
		);
		add_action(
			'wp_print_footer_scripts',
			array( static::class, 'dequeue_styles' ),
			9
		);
		add_filter(
			'script_loader_tag',
			array( static::class, 'set_defer' ),
			10,
			2
		);
		add_filter(
			'cli_show_cookie_bar_only_on_selected_pages',
			array( static::class, 'fixed_html_validation' )
		);
		add_filter(
			'wt_cli_change_privacy_overview_title_tag',
			array( static::class, 'fixed_title_level' )
		);
	}

	public static function the_style() : void {
		$the_options = Cookie_Law_Info::get_settings();

		/** @ignore all */
		$css = sprintf(
			'
				.cookie-law-info-preload #cookie-law-info-bar {
					background-color: %s;
					color: %s;
					font-family: inherit;
					bottom: 0;
					position: fixed;
					display: block;
					padding: 14px 25px
				}
				.cookie-law-info-preload .cli_settings_button {
					margin: 0 5px 0 0;
					color: %s;
					background-color: %s
				}
				.cookie-law-info-preload .cli_action_button {
					color: %s;
					background-color: %s
				}
				@media (max-width: 985px) {
					.cookie-law-info-preload #cookie-law-info-bar {
						padding: 25px
					}
				}
				.cookie-law-info-hidden .cookie-law-info-bar,
				.cookie-law-info-hidden .cookie-law-info-again,
				.cookie-law-info-hidden .cli-modal,
				.cookie-law-info-hidden .li-modal-backdrop {
					display: none
				}
			',
			$the_options['background'] ?? '',
			$the_options['text'] ?? '',
			$the_options['button_4_link_colour'] ?? '',
			$the_options['button_4_button_colour'] ?? '',
			$the_options['button_7_button_colour'] ?? '',
			$the_options['button_7_link_colour'] ?? ''
		);
		?>
		<style id="theme-gdrp-css-inline">
			<?php
			// todo: We need to figure out how to escape the CSS output. It seems to be sufficient to make sure it doesn't include a closing tag, and perhaps all external URLs should be replaced.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo static::minify( $css );
			?>
		</style>
		<script id="theme-gdrp-js-inline" type="module">
			const js = '/wp-content/themes/child-theme/assets/js/modules/cookie-law-info.js';
			if (!document.cookie.match(/^(.*;)?\s*viewed_cookie_policy\s*=\s*[^;]+(.*)?$/)) {
				document.documentElement.classList.add('cookie-law-info-preload');
				import(js);
			} else {
				document.documentElement.classList.add('cookie-law-info-hidden');
				setTimeout(() => import(js), 5000);
			}
		</script>
		<?php
	}

	public static function move_to_footer() : void {
		wp_dequeue_script( 'cookie-law-info' );
		wp_dequeue_script( 'cookie-law-info-ccpa' );

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_enqueue_script( 'cookie-law-info', '', array( 'jquery' ), false, true );
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_enqueue_script( 'cookie-law-info-ccpa', '', array( 'jquery', 'cookie-law-info' ), false, true );
	}

	public static function dequeue_styles() : void {
		wp_dequeue_style( 'cookie-law-info' );
		wp_dequeue_style( 'cookie-law-info-gdpr' );
		wp_dequeue_style( 'cookie-law-info-table' );
	}

	public static function set_defer( $tag, $handle ) {
		$defer_handles = array(
			'cookie-law-info'      => true,
			'cookie-law-info-ccpa' => true,
		);

		if ( isset( $defer_handles[ $handle ] ) ) {
			return str_replace( ' src', ' defer="defer" src', $tag );
		}

		return $tag;
	}

	public static function fixed_html_validation( string $notify_html ) : string {
		return str_replace(
			array(
				'<span><div',
				'</div></span>',
			),
			array(
				'<div><div',
				'</div></div>',
			),
			$notify_html
		);
	}

	public static function fixed_title_level( string $title ) : string {
		return '<h2>' . $title . '</h2>';
	}

	protected static function minify( string $text ) : string {
		return trim(
			preg_replace(
				array(
					'/\r\n\t/',
					'/\s{2,}/',
				),
				array(
					'',
					' ',
				),
				$text
			)
		);
	}
}
