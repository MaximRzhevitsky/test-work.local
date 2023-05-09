<?php

class WLD_Nav_Base {
	public static array $navs = array();

	public static function init() : void {
		add_action( 'after_setup_theme', array( static::class, 'register' ) );
	}

	public static function get_location( string $label ) : string {
		return mb_strtolower( str_replace( ' ', '_', $label ) . '_location' );
	}

	public static function register() : void {
		register_nav_menus( static::$navs );
	}

	public static function add( string $label ) : void {
		static::$navs[ static::get_location( $label ) ] = $label;
	}

	public static function get_bam_block_name( string $label ) : string {
		return mb_strtolower( 'menu-' . str_replace( ' ', '-', $label ) );
	}

	public static function has_sub_menu( array $menu_location_labels ) : bool {
		$locations = get_nav_menu_locations();
		foreach ( $menu_location_labels as $menu_location_label ) {
			$menu_locations = static::get_location( $menu_location_label );
			$menu_id        = $locations[ $menu_locations ] ?? 0;
			if ( $menu_id ) {
				$menu_items = wp_get_nav_menu_items( $menu_id );
				if ( $menu_items ) {
					foreach ( $menu_items as $menu_item ) {
						if ( $menu_item->menu_item_parent ) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}
}
