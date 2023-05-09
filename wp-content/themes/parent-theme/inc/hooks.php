<?php
if ( ! function_exists( '_hook_disabled_unused_widgets' ) ) {
	global $wp_widget_factory;

	function _hook_disabled_unused_widgets() : void {
		global $wp_registered_widgets, $wp_widget_factory;

		$disabled = array(
			'WP_Widget_Pages',
			'WP_Widget_Calendar',
			'WP_Widget_Media_Audio',
			'WP_Widget_Media_Image',
			'WP_Widget_Media_Gallery',
			'WP_Widget_Media_Video',
			'WP_Widget_Meta',
			'WP_Widget_Text',
			'WP_Widget_Recent_Posts',
			'WP_Widget_Recent_Comments',
			'WP_Widget_RSS',
			'WP_Widget_Tag_Cloud',
			'WP_Nav_Menu_Widget',
			'WP_Widget_Custom_HTML',
		);

		$disabled   = apply_filters( 'wld_get_disabled_widgets', $disabled );
		$keys       = array_diff( array_keys( $wp_widget_factory->widgets ), $disabled );
		$registered = array_keys( $wp_registered_widgets );
		$registered = array_map( '_get_widget_id_base', $registered );

		foreach ( $keys as $key ) {
			// Don't register new widget if old widget with the same id is already registered.
			if ( in_array( $wp_widget_factory->widgets[ $key ]->id_base, $registered, true ) ) {
				unset( $wp_widget_factory->widgets[ $key ] );
				continue;
			}

			$wp_widget_factory->widgets[ $key ]->_register();
		}
	}

	add_action( 'widgets_init', '_hook_disabled_unused_widgets' );
	remove_action( 'widgets_init', array( $wp_widget_factory, '_register_widgets' ), 100 );
}
if ( ! function_exists( '_hook_widgets_init' ) ) {
	function _hook_widgets_init() : void {
		register_sidebar(
			array(
				'id'            => 'blog_sidebar',
				'name'          => __( 'Blog Sidebar', 'theme' ),
				'description'   => __( 'This is a sidebar for blog widgets', 'theme' ),
				'before_widget' => '<div class="widget">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="archive-title">',
				'after_title'   => '</h2>',
			)
		);
	}

	add_action( 'widgets_init', '_hook_widgets_init' );
}
