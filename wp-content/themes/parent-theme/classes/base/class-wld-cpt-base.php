<?php

class WLD_CPT_Base {
	public const KEY = 'wld_cpt_slug_';

	public static array $permalinks = array();
	public static array $types      = array();
	public static array $thumbnails = array();

	public static function init() : void {
		add_action( 'init', array( static::class, 'register' ), 30 );
		add_action( 'load-options-permalink.php', array( static::class, 'settings' ) );
		add_filter( 'wld_thumbnails_support', array( static::class, 'set_thumbnail_support' ) );
	}

	public static function set_thumbnail_support( array $types ) : array {
		return array_unique( array_merge( $types, static::$thumbnails ) );
	}

	public static function add( string $post_type, array $args = array() ) : void {
		$args = array_merge(
			array(
				// Default parameters for register_post_type
				'labels'        => '',
				'public'        => false,
				'show_ui'       => true,
				'supports'      => array( 'title', 'revisions', 'editor', 'excerpt' ),
				'menu_icon'     => '',
				'menu_position' => 5,
				'rewrite'       => array(
					'slug'       => '',
					'with_front' => false,
				),
				// Special parameters
				'default_slug'  => '', // If public and empty rewrite, add default slug and create option
				'single_label'  => '',
				'plural_label'  => '',
			),
			$args
		);

		if ( ! in_array( 'revisions', $args['supports'], true ) ) {
			/** @noinspection UnsupportedStringOffsetOperationsInspection */
			$args['supports'][] = 'revisions';
		}

		if ( $args['public'] && empty( $args['rewrite']['slug'] ) ) {
			static::$permalinks[ $post_type ] = $args['default_slug'];
			$args['rewrite']['slug']          = static::get_slug( $post_type );
		}
		if ( empty( $args['menu_icon'] ) && false !== $args['menu_icon'] ) {
			$args['menu_icon'] = 'dashicons-admin-post';
		}
		if ( in_array( 'thumbnail', $args['supports'], true ) ) {
			static::$thumbnails[] = $post_type;
		}
		static::$types[ $post_type ] = $args;
	}

	public static function get_slug( string $post_type ) : string {
		$slug = false;
		if ( isset( static::$permalinks[ $post_type ] ) ) {
			$value = get_option( static::KEY . $post_type );
			if ( trim( $value ) ) {
				$slug = $value;
			} elseif ( empty( static::$permalinks[ $post_type ] ) ) {
				$slug = $post_type;
			} else {
				$slug = static::$permalinks[ $post_type ];
			}
		}

		return $slug;
	}

	public static function register() : void {
		foreach ( static::$types as $post_type => $args ) {
			if ( empty( $args['labels'] ) ) {
				$args['labels'] = static::get_labels( $post_type );
			}
			register_post_type( $post_type, $args );
		}
	}

	public static function get_labels( string $post_type ) : array {
		$single = static::get_single_label( $post_type );
		$plural = static::get_plural_label( $single, $post_type );

		// phpcs:disable WordPress.WP.I18n
		return array(
			'name'                  => $plural,
			'singular_name'         => $single,
			'add_new'               => __( 'Add New', 'theme' ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'theme' ), $single ),
			'edit_item'             => sprintf( __( 'Edit %s', 'theme' ), $single ),
			'new_item'              => sprintf( __( 'New %s', 'theme' ), $single ),
			'view_item'             => sprintf( __( 'View %s', 'theme' ), $single ),
			'view_items'            => sprintf( __( 'View %s', 'theme' ), $plural ),
			'search_items'          => sprintf( __( 'Search %s', 'theme' ), $plural ),
			'not_found'             => sprintf( __( 'No %s found.', 'theme' ), $plural ),
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash.', 'theme' ), $plural ),
			'parent_item_colon'     => sprintf( __( 'Parent %s:', 'theme' ), $plural ),
			'all_items'             => sprintf( __( 'All %s', 'theme' ), $plural ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'theme' ), $single ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'theme' ), $plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'theme' ), $plural ),
			'items_list'            => sprintf( __( '%s list', 'theme' ), $plural ),
		);
		// phpcs:enable WordPress.WP.I18n
	}

	public static function get_single_label( string $post_type ) {
		if ( empty( static::$types[ $post_type ]['single_label'] ) ) {
			$label = ucwords( str_replace( array( '_', '-' ), ' ', $post_type ) );
		} else {
			$label = static::$types[ $post_type ]['single_label'];
		}

		return apply_filters( 'wld_get_cpt_label_single', $label, $post_type );
	}

	public static function get_plural_label( string $singular, string $post_type ) {
		/** @noinspection DuplicatedCode */
		if ( empty( static::$types[ $post_type ]['plural_label'] ) ) {
			$label = match ( strtolower( $singular[ strlen( $singular ) - 1 ] ) ) {
				'y' => substr( $singular, 0, - 1 ) . 'ies',
				's' => $singular . 'es',
				default => $singular . 's',
			};
		} else {
			$label = static::$types[ $post_type ]['plural_label'];
		}

		return apply_filters( 'wld_get_cpt_label_plural', $label, $post_type );
	}

	public static function settings() : void {
		if ( empty( static::$permalinks ) ) {
			return;
		}
		add_settings_section(
			'wld_cpt_permalinks',
			__( 'Theme Custom Post Types', 'theme' ),
			'__return_empty_string',
			'permalink'
		);
		foreach ( static::$permalinks as $post_type => $permalink ) {
			$id    = static::KEY . $post_type;
			$value = sanitize_text_field( $_POST[ $id ] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification

			if ( $value ) {
				$value = trim( $value, '/' );
				$value = esc_url_raw( $value );
				$value = preg_replace( '~https?://~', '', $value );
				update_option( $id, $value, true );
			}

			register_setting( 'permalink', $id );
			add_settings_field(
				$id,
				static::get_single_label( $post_type ),
				static function () use ( $id, $post_type ) {
					$slug = static::get_slug( $post_type );
					$home = home_url( '/' );
					$tags = array();
					if ( class_exists( 'WLD_Tax' ) ) {
						$tags = WLD_Tax::get_tags_by_post_type( $post_type );
						$tags = trim( implode( ', ', $tags ), ', ' );
					}
					?>
					<code><?php echo esc_html( $home ); ?></code>
					<!--suppress HtmlFormInputWithoutLabel -->
					<input name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>"
						   type="text" value="<?php echo esc_attr( $slug ); ?>" class="regular-text code">
					<div class="available-structure-tags hide-if-no-js">
						<div id="custom_selection_updated" aria-live="assertive"
							 class="screen-reader-text"></div>
						<?php if ( ! empty( $tags ) ) : ?>
							<p>
								<?php esc_html_e( 'Available tags: ', 'theme' ); ?>
								<?php echo esc_html( $tags ); ?>
							</p>
						<?php endif; ?>
					</div>
					<?php
				},
				'permalink',
				'wld_cpt_permalinks'
			);
		}
	}
}
