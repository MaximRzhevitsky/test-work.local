<?php

/**
 * Adds the ability to create a subdirectory inside a child theme,
 * in which you can place other child themes.
 *
 * 1)
 * This class must be init in the child theme's functions.php!
 * ```
 * require_once dirname( __DIR__ ) . '/parent-theme/inc/class-wld-sub-themes.php';
 *
 * WLD_Sub_Themes::init();
 * ```
 *
 * 2) Structure
 * ```
 * child-theme
 * -- acf-json
 * -- css
 * -- images
 * -- js
 * -- sub-themes
 * ---- sub-themes-2
 * ------ acf-json
 * ------ css
 * ------ images
 * ------ js
 * ------ template-parts
 * ------ templates
 * ------ theme-functions.php
 * ---- sub-themes-3
 * ------ ...
 * ---- sub-themes-4
 * ------ ...
 * ---- ...
 * -- template-parts
 * -- templates
 * -- functions.php
 * -- style.css
 * -- theme-functions.php
 * ```
 *
 * 3)
 * Also you need to create a copy of "TPL: Flexible Content"
 * Rename it to "TPL: ${Sub Theme Name} Flexible Content"
 * And set its location to:
 * ```
 * "location": [
 *   [
 *     {
 *       "param": "page_template",
 *       "operator": "==",
 *       "value": "sub-themes\/${Sub Theme Directory}\/templates\/tpl-flexible-content.php"
 *     }
 *   ]
 * ],
 * ```
 *
 * @noinspection PhpUnused
 *
 * // phpcs:disable WordPress.WP.DiscouragedConstants.STYLESHEETPATHUsageFound
 */
class WLD_Sub_Themes_Base {
	public static string $directory_name = 'sub-themes';

	public static function init() : void {
		static::$directory_name = apply_filters( 'wld_sub_themes_directory_name', static::$directory_name );

		add_filter(
			'acf/settings/load_json',
			array( static::class, 'acf_load_json' )
		);
		add_filter(
			'acf/settings/save_json',
			array( static::class, 'acf_save_json' )
		);
		add_filter(
			'acf/pre_render_fields',
			array( static::class, 'acf_pre_render_fields' )
		);
		add_filter(
			'theme_templates',
			array( static::class, 'get_theme_templates' ),
			10,
			4
		);
		add_filter(
			'template_include',
			array( static::class, 'get_template_include' )
		);
		add_filter(
			'stylesheet_directory_uri',
			array( static::class, 'get_stylesheet_directory_uri' )
		);
		add_filter(
			'stylesheet_directory',
			array( static::class, 'get_stylesheet_directory' )
		);
		add_filter(
			'pre_option_wld_other_favicon_html',
			array( static::class, 'get_favicon' )
		);
	}

	public static function acf_load_json( array $paths ) : array {
		return array_merge(
			$paths,
			glob( STYLESHEETPATH . '/' . static::$directory_name . '/*/acf-json/', GLOB_ONLYDIR )
		);
	}

	public static function acf_save_json() : string {
		$theme_slug = '/';

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['post_title'] ) && preg_match( '/FC:([^:]+): \S+/', $_POST['post_title'], $matches ) ) {
			$theme_slug .= sanitize_title( $matches[1] ) . '/';
		}

		return STYLESHEETPATH . $theme_slug . 'acf-json';
	}

	public static function acf_pre_render_fields( array $fields ) : array {
		foreach ( $fields as $field_key => $field ) {
			if (
				isset( $field['wrapper']['id'] ) &&
				'tpl_flexible_content_content' === $field['wrapper']['id'] &&
				! empty( $field['layouts'] )
			) {
				$sub_theme_name = static::get_sub_theme_name();
				foreach ( $field['layouts'] as $key => $layout ) {
					if ( $sub_theme_name ) {
						if ( ! preg_match( '/^' . $sub_theme_name . '-/', $layout['name'] ) ) {
							unset( $field['layouts'][ $key ] );
							continue;
						}

						$field['layouts'][ $key ]['label'] = trim( explode( ':', $layout['label'] )[1] );
					} elseif ( str_contains( $layout['label'], ':' ) ) {
						unset( $field['layouts'][ $key ] );
					}
				}
				$fields[ $field_key ] = $field;
			}
		}

		return $fields;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function get_theme_templates( array $post_templates, WP_Theme $wp_theme, ?WP_Post $post, string $post_type ) : array {
		if ( 'page' === $post_type ) {
			$sub_themes = glob( STYLESHEETPATH . '/' . static::$directory_name . '/*/', GLOB_ONLYDIR );
			foreach ( $sub_themes as $sub_theme ) {
				$name = ucwords( str_replace( '-', ' ', basename( $sub_theme ) ) );
				$path = ltrim( str_replace( STYLESHEETPATH, '', $sub_theme ), '/' );
				$key  = $path . 'templates/tpl-flexible-content.php';

				$post_templates[ $key ] = $name;
			}
		}

		return $post_templates;
	}

	public static function get_template_include( string $template ) : string {
		$sub_theme_path = static::get_sub_theme_path();
		if ( $sub_theme_path ) {
			$theme_functions_path = STYLESHEETPATH . '/' . $sub_theme_path . 'theme-functions.php';
			if ( file_exists( $theme_functions_path ) ) {
				require $theme_functions_path;
			}

			if ( ! file_exists( STYLESHEETPATH . '/' . $template ) ) {
				static::the_sub_theme_content();
				exit;
			}
		}

		return $template;
	}

	public static function get_stylesheet_directory_uri( string $stylesheet_dir_uri ) : string {
		return $stylesheet_dir_uri . static::get_sub_theme_path( '', '/' );
	}

	public static function get_stylesheet_directory( string $stylesheet_dir ) : string {
		return $stylesheet_dir . static::get_sub_theme_path( '', '/' );
	}

	public static function get_favicon( $value ) {
		if ( static::get_sub_theme_path() ) {
			$favicon_id = (int) get_field( 'favicon', false, false );
			if ( $favicon_id ) {
				$favicon_url = wp_get_attachment_image_url( $favicon_id, 'full' );
				if ( $favicon_url ) {
					$favicon_url = preg_replace( '/^https?:/', '', $favicon_url );

					/** @noinspection HtmlUnknownTarget */
					return sprintf(
						'<link rel="shortcut icon" href="%s">',
						esc_url( $favicon_url )
					);
				}
			}

			return '';
		}

		return $value;
	}

	public static function get_sub_theme_name() : string {
		return trim( str_replace( static::$directory_name, '', static::get_sub_theme_path() ), '/' );
	}

	public static function get_sub_theme_path( string $after = '/', string $before = '' ) : string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['action'] ) && 'acf/ajax/check_screen' === $_POST['action'] ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$template_slug = $_POST['page_template'];
		} else {
			$template_slug = get_page_template_slug();
		}
		$template_search = '/templates/tpl-flexible-content.php';
		if ( str_contains( $template_slug, $template_search ) ) {
			return $before . str_replace( $template_search, '', $template_slug ) . $after;
		}

		return '';
	}

	/**
	 * @noinspection HtmlRequiredLangAttribute, HtmlRequiredTitleElement
	 */
	public static function the_sub_theme_content() : void {
		$sub_theme_path = static::get_sub_theme_path();

		do_action( 'get_header', '', array() );

		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<!DOCTYPE html><html ' . get_language_attributes() . '><head>';
		wp_head();
		echo '</head><body ';
		body_class();
		echo '>';
		wp_body_open();

		if ( file_exists( WLD_File::get_assets_path( '/template-parts/header.php' ) ) ) {
			get_template_part( $sub_theme_path . 'template-parts/header' );
		} else {
			get_template_part( 'template-parts/header' );
		}

		if ( have_posts() ) {
			the_post();
			if ( post_password_required() ) {
				// todo: Escape output.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo get_the_password_form();
			} else {
				$need_run = apply_filters( 'wld_need_run_content', true );

				if ( $need_run ) {
					$sub_theme_name = static::get_sub_theme_name();
					do_action( 'wld_run_content' );
					if ( have_rows( 'content' ) ) {
						while ( have_rows( 'content' ) ) {
							the_row();
							WLD_ACF_Flex_Content::the_content(
								$sub_theme_path . 'template-parts/flexible-content/',
								ltrim( str_replace( $sub_theme_name, '', get_row_layout() ), '-' )
							);
						}
					}
					do_action( 'wld_end_content' );
				} else {
					do_action( 'wld_not_need_run_content' );
				}
			}
		}

		do_action( 'get_footer', '', array() );

		if ( file_exists( WLD_File::get_assets_path( '/template-parts/footer.php' ) ) ) {
			get_template_part( $sub_theme_path . 'template-parts/footer' );
		} else {
			get_template_part( 'template-parts/footer' );
		}

		wp_footer();

		echo '</body></html>';
	}
}
