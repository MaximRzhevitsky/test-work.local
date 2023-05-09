<?php

class WLD_Accessibility_Menu_Base {
	public static function init() : void {
		add_filter(
			'wp_nav_menu_args',
			array( static::class, 'add_role_menubar_at_menu' )
		);
		add_filter(
			'wp_nav_menu_items',
			array( static::class, 'add_role_menu_at_ul' ),
			10,
			2
		);
		add_filter(
			'wp_nav_menu_items',
			array( static::class, 'add_role_none_at_li' ),
			10,
			2
		);
		add_filter(
			'nav_menu_link_attributes',
			array( static::class, 'add_role_menuitem_at_a' ),
			10,
			3
		);
		add_filter(
			'nav_menu_link_attributes',
			array( static::class, 'add_aria_popup_at_a' ),
			10,
			3
		);
		add_filter(
			'wp_nav_menu_objects',
			array( static::class, 'add_has_children' ),
			10,
			2
		);
	}

	public static function add_role_menubar_at_menu( array $args ) : array {
		if ( ! empty( $args['accessibility'] ) ) {
			$args['items_wrap'] = str_replace(
				'<ul',
				'<ul role="menubar" data-accessibility-menu',
				$args['items_wrap']
			);
		}

		return $args;
	}

	public static function add_role_menu_at_ul( string $items, object $args ) : string {
		if ( ! empty( $args->accessibility ) ) {
			$items = str_replace(
				'<ul',
				'<ul role="menu"',
				$items
			);
		}

		return $items;
	}

	public static function add_role_none_at_li( string $items, object $args ) : string {
		if ( ! empty( $args->accessibility ) ) {
			$items = str_replace(
				'<li',
				'<li role="none"',
				$items
			);
		}

		return $items;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function add_role_menuitem_at_a( array $atts, WP_Post $menu_item, stdClass $args ) : array {
		if ( ! empty( $args->accessibility ) ) {
			$atts['role'] = 'menuitem';
		}

		return $atts;
	}

	public static function add_aria_popup_at_a( array $atts, WP_Post $menu_item, stdClass $args ) : array {
		/** @noinspection PhpUndefinedFieldInspection */
		if ( ! empty( $args->accessibility ) && $menu_item->has_children ) {
			$atts['aria-haspopup'] = 'true';
			$atts['aria-expanded'] = 'false';
		}

		return $atts;
	}

	public static function add_has_children( array $items, object $args ) : array {
		if ( ! empty( $args->accessibility ) ) {
			foreach ( $items as $item ) {
				$item->has_children = in_array( 'menu-item-has-children', $item->classes, true );
			}
		}

		return $items;
	}
}
