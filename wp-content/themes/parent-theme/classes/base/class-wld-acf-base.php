<?php

class WLD_ACF_Base {
	public static function init() : void {
		add_action(
			'acf/init',
			array( static::class, 'register_fields' )
		);
		add_action(
			'acf/init',
			array( static::class, 'add_settings_page' )
		);
		add_filter(
			'acf/settings/save_json',
			array( static::class, 'save_json' )
		);
		add_filter(
			'acf/settings/show_admin',
			array( static::class, 'show_admin_menu' )
		);
		add_filter(
			'acf/load_field/type=checkbox',
			array( static::class, 'get_custom_choices' )
		);
		add_filter(
			'acf/load_field/type=radio',
			array( static::class, 'get_custom_choices' )
		);
		add_filter(
			'acf/update_value/type=checkbox',
			array( static::class, 'save_custom_choices' ),
			10,
			3
		);
		add_filter(
			'acf/update_value/type=radio',
			array( static::class, 'save_custom_choices' ),
			10,
			3
		);
		add_action(
			'acf/init',
			array( static::class, 'removed_wptexturize' )
		);
		add_filter(
			'acf_the_content',
			array( static::class, 'replace_trailing_slash' )
		);
	}

	public static function register_fields() : void {
		acf_include( 'includes/fields/class-acf-field-group.php' );
		acf_include( 'includes/fields/class-acf-field-image.php' );

		acf_register_field_type( 'WLD_ACF_Replace_Text_Field' );
		acf_register_field_type( 'WLD_ACF_Contact_Link_Field' );
		acf_register_field_type( 'WLD_ACF_Copyright_Field' );
		acf_register_field_type( 'WLD_ACF_Menu_Field' );
		acf_register_field_type( 'WLD_ACF_Social_Links_Field' );
		acf_register_field_type( 'WLD_ACF_Title_Field' );
		acf_register_field_type( 'WLD_ACF_Background_Field' );

		if ( WLD_Theme::$gf_enabled ) {
			acf_register_field_type( 'WLD_ACF_Forms_Field' );
		}
	}

	public static function add_settings_page() : void {
		acf_add_options_page(
			array(
				'page_title' => __( 'Theme Settings', 'theme' ),
				'menu_title' => __( 'Theme Settings', 'theme' ),
				'menu_slug'  => 'theme-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
				'autoload'   => true,
			)
		);
	}

	public static function save_json() : string {
		return WLD_Theme::$child_path . 'acf-json';
	}

	public static function show_admin_menu() : bool {
		// phpcs:ignore WordPress.Security.NonceVerification
		$post_type = sanitize_text_field( $_GET['post_type'] ?? '' );

		return 'local' === wp_get_environment_type() || 'acf-field-group' === $post_type;
	}

	public static function get_custom_choices( $field ) {
		if ( isset( $field['allow_custom'] ) && 1 === $field['allow_custom'] && ! acf_is_screen( 'acf-field-group' ) ) {
			$choices          = get_option( 'wld_custom_choices_' . $field['key'] );
			$field['choices'] = $choices && is_array( $choices ) ? $choices : array();
		}

		return $field;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function save_custom_choices( $values, $post_id, $field ) {
		if ( isset( $field['allow_custom'] ) && 1 === $field['allow_custom'] && is_array( $values ) ) {
			$update  = false;
			$choices = get_option( 'wld_custom_choices_' . $field['key'] );
			$choices = $choices && is_array( $choices ) ? $choices : array();
			foreach ( $values as $value ) {
				if ( ! in_array( $value, $choices, true ) ) {
					$update            = true;
					$choices[ $value ] = $value;
				}
			}
			if ( $update ) {
				update_option( 'wld_custom_choices_' . $field['key'], $choices, false );
			}
		}

		return $values;
	}

	public static function removed_wptexturize() : void {
		remove_filter( 'acf_the_content', 'wptexturize' );
	}

	public static function replace_trailing_slash( string $html ) : string {
		return str_replace( '/>', '>', $html );
	}
}
