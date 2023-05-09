<?php
get_header();

if ( is_singular() ) {
	if ( is_single() ) {
		while ( wld_loop( 'wld_blog_menu' ) ) {
			get_template_part( 'template-parts/blog-menu' );
		}
		get_template_part( 'template-parts/blog-title' );
		get_template_part( 'template-parts/blog-post' );
		while ( wld_loop( 'wld_blog_form' ) ) {
			get_template_part( 'template-parts/blog-form' );
		}
	} else {
		get_template_part( 'template-parts/default-page' );
	}
} elseif ( is_home() || is_archive() ) {
	while ( wld_loop( 'wld_blog_banner' ) ) {
		get_template_part( 'template-parts/blog-banner' );
	}
	while ( wld_loop( 'wld_blog_menu' ) ) {
		get_template_part( 'template-parts/blog-menu' );
	}
	get_template_part( 'template-parts/blog-posts' );
	while ( wld_loop( 'wld_blog_form' ) ) {
		get_template_part( 'template-parts/blog-form' );
	}
} elseif ( is_search() ) {
	$search_title = sprintf( '<h1 class="title">%s</h1>', esc_html__( 'Search', 'theme' ) );
	get_template_part( 'template-parts/search-form', null, array( 'title' => $search_title ) );
	get_template_part( 'template-parts/search-results' );
} else {
	get_template_part( 'template-parts/error-404' );
}

get_footer();
