<?php
// Specify styles for .btn as in file styles.css
WLD_TinyMCE::add_editor_styles( '.btn', 'background-color:;color:#fff;' );

// Specify styles for login page
WLD_Login_Style::set( 'btn_bg', '' );
WLD_Login_Style::set( 'btn_color', '' );

// Add custom post types
WLD_CPT::add( 'testimonial' );

WLD_CPT::add(
	'faq',
	array(
		'menu_icon' => 'dashicons-megaphone',
		'supports'  => array( 'title', 'editor' ),
	)
);

// Add custom taxonomies
WLD_Tax::add(
	'faq_category',
	array(
		'object_type' => 'faq',
	)
);

// Add menus
WLD_Nav::add( 'Header Main' );
WLD_Nav::add( 'Header Second' );
WLD_Nav::add( 'Footer Main' );
WLD_Nav::add( 'Footer Links' );

// Add image sizes
WLD_Images::add_size( '30x30' );
