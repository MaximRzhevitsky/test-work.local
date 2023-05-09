<?php

use MatthiasMullie\Minify;

class WLD_GF_Load_Optimization_Base {
	protected const INITIALIZE = 'gform.initializeOnLoaded';

	protected static int $form_id = 0;
	protected static int $index = 0;

	protected static array $scripts = array();
	protected static array $init_scripts = array();

	public static function init(): void {
		add_action(
			'init',
			array( static::class, 'shortcode' ),
			11
		);
		add_filter(
			'template_include',
			array( static::class, 'json' )
		);
	}

	public static function shortcode(): void {
		$tags = array( 'gravityforms', 'gravityform' );

		foreach ( $tags as $tag ) {
			remove_shortcode( $tag );
			add_shortcode(
				$tag,
				static function ( $atts ) {
					return sprintf(
						'
						<div data-optimisation-gf-form-id="%d"
							 data-title="%d"
							 data-description="%d"
							 aria-live="polite"
						><span tabindex="0">%s</span></div>',
						$atts['id'] ?? 0,
						$atts['title'] ?? 0,
						$atts['description'] ?? 0,
						esc_html__( 'The form is loading', 'theme' )
					);
				}
			);
		}
	}

	public static function json( $template ) {
		// Here we convert all data to safe types, there is no need to protect
		// phpcs:disable WordPress.Security.NonceVerification
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			/** @noinspection JsonEncodingApiUsageInspection */
			$data = empty( $_POST ) ? json_decode( file_get_contents( 'php://input' ), true ) : $_POST;
		} else {
			$data = $_GET;
		}
		// phpcs:enable WordPress.Security.NonceVerification

		if ( isset( $data['optimisation_gf_form_id'] ) ) {
			static::$form_id     = (int) $data['optimisation_gf_form_id'];
			$display_title       = (bool) ( $data['title'] ?? '' );
			$display_description = (bool) ( $data['description'] ?? '' );
			$show_html           = isset( $data['html'] );

			add_filter( 'gform_init_scripts_footer', '__return_false' );
			if ( class_exists( 'QM_Dispatchers' ) ) {
				remove_action( 'shutdown', array( QM_Dispatchers::get( 'html' ), 'dispatch' ), 0 );
			}
			if ( class_exists( 'Hotjar' ) ) {
				wld_remove_filter_for_class( 'wp_head', 'Hotjar', 'hotjar_script' );
			}

			if ( $show_html ) {
				do_action( 'wp_head' );
				gravity_form(
					static::$form_id,
					$display_title,
					$display_description,
					false,
					null,
					true,
					false
				);
				do_action( 'wp_footer' );
				exit();
			}

			$form_html = gravity_form(
				static::$form_id,
				$display_title,
				$display_description,
				false,
				null,
				true,
				false,
				false
			);

			ob_start();

			do_action( 'wp_head' );
			do_action( 'wp_footer' );

			$footer_html = ob_get_clean();

			preg_match( '/id=[\'"]gform_wrapper_(\d+)[\'"]/', $form_html, $matches );
			static::$form_id = (int) $matches[1];

			static::get_scripts_from_html( $form_html, true );
			static::get_scripts_from_html( $footer_html );

			wp_send_json_success(
				array(
					'html'    => $form_html,
					'scripts' => array_merge( static::$scripts, static::$init_scripts ),
				)
			);

			return '';
		}

		return $template;
	}

	protected static function get_scripts_from_html( string &$html, bool $clear = false ): void {
		$re = '~<script(?<attributes>[^>]*)>(?<inline>.*?)</script>~ms';

		$matches = array();
		if ( $clear ) {
			$html = preg_replace_callback(
				$re,
				static function ( $match ) use ( &$matches ) {
					$matches[] = $match;

					return '';
				},
				$html
			);
		} else {
			preg_match_all( $re, $html, $matches, PREG_SET_ORDER );
		}

		foreach ( $matches as $match ) {
			$re = '~(?<name>\w+)(=(?:[\'"])?(?<value>[^\'"]+)(?:[\'"])?)?~ms';
			preg_match_all( $re, $match['attributes'], $attributes_matches, PREG_SET_ORDER );

			$attributes = array(
				'id'   => '',
				'src'  => '',
				'type' => '',
			);
			foreach ( $attributes_matches as $attributes_match ) {
				$attributes[ $attributes_match['name'] ] = $attributes_match['value'] ?? '';
			}

			if ( 'application/ld+json' === $attributes['type'] || 'text/template' === $attributes['type'] ) {
				continue;
			}

			$script = array(
				'id'   => $attributes['id'] ?: static::get_inline_id(),
				'type' => $attributes['type'],
			);
			if ( false !== strpos( $match['inline'], static::INITIALIZE ) ) {
				$script['src']          = static::inline_script_to_src(
					str_replace(
						static::INITIALIZE,
						'',
						trim( $match['inline'], " \t\n\r\0\x0B;" ) . '()'
					)
				);
				static::$init_scripts[] = $script;
			} else {
				$script['src']     = $attributes['src'] ?: static::inline_script_to_src( $match['inline'] );
				static::$scripts[] = $script;
			}
		}
	}

	protected static function get_inline_id(): string {
		return 'gform-inline-' . static::$form_id . '-' . ( ++ static::$index );
	}

	public static function inline_script_to_src( string $inline_script, bool $separate_stream = false ): string {
		require_once WLD_File::get_parent_path( 'inc/libs/minify/src/Minify.php' );
		require_once WLD_File::get_parent_path( 'inc/libs/minify/src/JS.php' );

		$src_script = ( new Minify\JS( $inline_script ) )->minify();

		$src_script = preg_replace(
			array(
				'/(\.\w+\([^()]+\))([^.,;:<>=+|&}?)![])/',
				'/(?<!else|{|}|;) if/',
				'/}(?!catch|else|[|{},;:()[\]])/',
				'/(=(?!new|function|typeof|void)[[:word:]]+) /',
			),
			array( '$1;$2', ';$0', '$0;', '$1; ' ),
			$src_script
		);

		if ( $separate_stream ) {
			$src_script = 'setTimeout(function(){' . $src_script . ';},10)';
		}

		// base64_encode is needed, because we need to get the base64 for the SRC attribute of the script tag.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return 'data:text/javascript;base64,' . base64_encode( $src_script );
	}
}
