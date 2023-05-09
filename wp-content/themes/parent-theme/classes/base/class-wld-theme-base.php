<?php

class WLD_Theme_Base {
	public static bool $disable_file_edit = true;
	public static bool $woo_enabled       = false;
	public static bool $acf_enabled       = false;
	public static bool $gf_enabled        = false;
	public static bool $yoast_enable      = false;

	public static string $parent_path = '';
	public static string $child_path  = '';
	public static string $parent_url  = '';
	public static string $child_url   = '';

	protected static array $cache_post_ids_taking_into_preview = array();
	protected static array $theme_files_list                   = array();
	protected static array $autoload_files_list                = array();

	public static function init() : void {
		static::$parent_path  = wp_normalize_path( get_template_directory() ) . '/';
		static::$child_path   = wp_normalize_path( get_stylesheet_directory() ) . '/';
		static::$parent_url   = get_template_directory_uri() . '/';
		static::$child_url    = get_stylesheet_directory_uri() . '/';
		static::$woo_enabled  = function_exists( 'WC' );
		static::$acf_enabled  = function_exists( 'acf' );
		static::$gf_enabled   = class_exists( 'GFForms' );
		static::$yoast_enable = defined( 'WPSEO_FILE' );

		defined( 'DISALLOW_FILE_EDIT' ) || define( 'DISALLOW_FILE_EDIT', static::$disable_file_edit );

		static::set_files_to_theme_files_list( '' );
		static::set_files_to_theme_files_list( 'inc' );
		static::set_files_to_theme_files_list( 'classes' );
		static::set_files_to_theme_files_list( 'classes/base' );
		static::set_files_to_theme_files_list( 'classes/traits' );

		if ( static::$woo_enabled ) {
			static::set_files_to_theme_files_list( 'woocommerce' );
			static::set_files_to_theme_files_list( 'woocommerce/inc' );
			static::set_files_to_theme_files_list( 'woocommerce/classes' );
			static::set_files_to_theme_files_list( 'woocommerce/classes/base' );
			static::set_files_to_theme_files_list( 'woocommerce/classes/traits' );
		}

		spl_autoload_register( array( static::class, 'autoloader' ) );

		WLD_Log::init();

		add_action(
			'after_setup_theme',
			array( static::class, 'load_theme_textdomain' )
		);
		add_action(
			'after_setup_theme',
			array( static::class, 'add_theme_supports' )
		);

		static::require_file( 'inc/hooks.php' );
		static::require_file( 'inc/helpers.php' );
		static::init_classes();
		static::require_file( 'inc/scripts-and-styles.php' );
		static::require_file( 'theme-functions.php' );

		if ( static::$woo_enabled ) {
			static::require_file( 'woocommerce/wc-functions.php' );
		}
	}

	public static function init_classes() : void {
		if ( false === static::theme_required() ) {
			return;
		}

		do_action( 'wld_before_init' );

		WLD_AccessiBe_Plugin::init();
		WLD_Accessibility_Menu::init();
		if ( static::$acf_enabled ) {
			WLD_ACF::init();
			WLD_ACF_Add_Field_Helper::init();
			WLD_ACF_Flex_Content::init();
			WLD_ACF_Google_Maps_API::init();
			WLD_ACF_Relationship_All::init();
			WLD_ACF_WYSIWYG_Height::init();
			WLD_ACF_Search::init();
		}
		WLD_Admin_Bar::init();
		WLD_Admin_Notices::init();
		WLD_Admin_Panel::init();
		WLD_BAM_Menu::init();
		WLD_CPT::init();
		WLD_Defer_Scripts::init();
		WLD_Delay_Scripts::init();
		WLD_Dequeue::init();
		WLD_Enqueue_Scripts::init();
		WLD_Enqueue_Styles::init();
		WLD_Extend_WPLink::init();
		WLD_Favicon::init();
		WLD_Fix_GF_Multiple_IDs::init();
		WLD_Fonts::init( apply_filters( 'wld_fonts_type', 'js' ) );
		WLD_Front::init();
		WLD_GA_GTM::init();
		WLD_GDRP::init();
		WLD_GF::init();
		WLD_GF_Custom_Merge_Tags::init();
		WLD_Images::init();
		WLD_Importer::init();
		WLD_KSES::init();
		WLD_Local_External_Scripts::init();
		WLD_Login_Style::init();
		WLD_Nav::init();
		WLD_Not_A_Page::init();
		WLD_Preload_Logo::init();
		WLD_QM::init();
		WLD_Site_Map::init();
		WLD_SVG::init();
		WLD_Tax::init();
		WLD_Clear_Cache_Menu_Order::init();
		WLD_TinyMCE::init();
		WLD_Yoast::init();
		WLD_Yoast_SEO_Score_Fix::init();

		do_action( 'wld_init' );
	}

	public static function get_version() : string {
		static $version;
		if ( null === $version ) {
			$version = wp_get_theme( 'parent-theme' )->get( 'Version' );
		}

		return $version;
	}

	public static function autoloader( string $class_or_trait_name ) : void {
		if ( 0 !== strncmp( $class_or_trait_name, 'WLD_', 4 ) ) {
			return;
		}

		if ( isset( static::$autoload_files_list[ $class_or_trait_name ] ) ) {
			/** @noinspection PhpIncludeInspection, RedundantSuppression */
			require static::$autoload_files_list[ $class_or_trait_name ];

			return;
		}

		$file_name  = str_replace( '_', '-', strtolower( $class_or_trait_name ) ) . '.php';
		$is_woo     = 0 === strncmp( $class_or_trait_name, 'WLD_WC_', 7 );
		$is_base    = str_ends_with( $class_or_trait_name, '_Base' ); // Here I decided to focus on the end, in order to check for mane and woo with one condition
		$root       = $is_woo ? 'woocommerce/classes' : 'classes';
		$class_name = $root . ( $is_base ? '/base/' : '/' ) . 'class-' . $file_name;
		$trait_name = $root . '/traits/trait-' . $file_name;

		foreach ( array( $class_name, $trait_name ) as $theme_file ) {
			if ( isset( static::$theme_files_list[ $theme_file ] ) ) {
				static::$autoload_files_list[ $class_or_trait_name ] = static::$theme_files_list[ $theme_file ];
				/** @noinspection PhpIncludeInspection, RedundantSuppression */
				require static::$theme_files_list[ $theme_file ];
				break;
			}
		}
	}

	protected static function require_file( string $file_path_from_theme ) : void {
		if ( isset( static::$theme_files_list[ $file_path_from_theme ] ) ) {
			/** @noinspection PhpIncludeInspection, RedundantSuppression */
			require static::$theme_files_list[ $file_path_from_theme ];
		}
	}

	protected static function set_files_to_theme_files_list( string $directory_path_from_theme ) : void {
		if ( $directory_path_from_theme ) {
			$directory_path_from_theme .= '/';
		}

		$files_list  = array();
		$scan_paths  = array();
		$parent_path = static::$parent_path . $directory_path_from_theme;
		$child_path  = static::$child_path . $directory_path_from_theme;

		if ( is_dir( $parent_path ) ) {
			$scan_paths[] = $parent_path;
		}

		if ( is_dir( $child_path ) ) {
			$scan_paths[] = $child_path;
		}

		foreach ( $scan_paths as $scan_path ) {
			$file_names = scandir( $scan_path );
			foreach ( $file_names as $file_name ) {
				if ( '.' === $file_name || '..' === $file_name || ! str_contains( $file_name, '.' ) ) {
					continue;
				}

				$files_list[ $directory_path_from_theme . $file_name ] = $scan_path . $file_name;
			}
		}

		if ( $files_list ) {
			static::$theme_files_list = array_merge( static::$theme_files_list, $files_list );
		}
	}

	public static function get_post_id_taking_into_preview() {
		$post_id = get_the_ID();
		if ( isset( static::$cache_post_ids_taking_into_preview[ $post_id ] ) ) {
			return static::$cache_post_ids_taking_into_preview[ $post_id ];
		}

		// phpcs:disabled WordPress.Security.NonceVerification
		if (
			isset( $_GET['preview'] ) &&
			(
				( isset( $_GET['preview_id'] ) && $post_id === (int) $_GET['preview_id'] ) ||
				( isset( $_GET['page_id'] ) && $post_id === (int) $_GET['page_id'] )
			)
		) {
			$revisions = wp_get_post_revisions( $post_id, array( 'numberposts' => 1 ) );
			$revision  = array_shift( $revisions );
			if ( $revision && $revision->post_parent === $post_id ) {
				$post_id = (int) $revision->ID;
			}
		}
		// phpcs:enabled WordPress.Security.NonceVerification

		static::$cache_post_ids_taking_into_preview[ $post_id ] = $post_id;

		return $post_id;
	}

	public static function never() : bool {
		return false;
	}

	public static function theme_required() : bool {
		$title    = '';
		$mess     = '';
		$in_admin = is_admin() || 'wp-login.php' === $GLOBALS['pagenow'];
		$required = true;
		if ( ! WLD_Theme::$acf_enabled ) {
			$t = esc_html__( 'ACF PRO Disabled', 'theme' );
			$m = esc_html__(
				'For the theme to work, you need to install and enable the "ACF PRO" plugin.',
				'theme'
			);
			if ( $in_admin ) {
				add_action(
					'admin_notices',
					static function () use ( $t, $m ) {
						?>
						<div class="notice notice-error">
							<h3><?php echo esc_html( $t ); ?></h3>
							<?php echo wp_kses_post( wpautop( $m ) ); ?>
						</div>
						<?php
					},
					10,
					0
				);
			} else {
				$title .= ' & ' . $t;
				$mess  .= "\n\n" . $m;
			}
			$required = false;
		}

		if ( $in_admin || $required ) {
			return $required;
		}

		/** @noinspection ForgottenDebugOutputInspection */
		wp_die( wp_kses_post( nl2br( trim( $mess ) ) ), esc_html( trim( $title, '& ' ) ), 500 );
	}

	public static function load_theme_textdomain() : void {
		if ( is_dir( WLD_File::get_path( 'languages' ) ) ) {
			load_theme_textdomain( 'theme', WLD_File::get_path( 'languages' ) );
		}
	}

	public static function add_theme_supports() : void {
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'woocommerce' );
		add_theme_support(
			'html5',
			array(
				'comment-list',
				'comment-form',
				'search-form',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);
		add_theme_support(
			'post-thumbnails',
			apply_filters( 'wld_thumbnails_support', array( 'post' ) )
		);
	}
}
