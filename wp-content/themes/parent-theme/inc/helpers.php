<?php
/**
 * ACF helpers
 */
if ( ! function_exists( 'wld_get' ) ) {
	function wld_get( string $selector, ...$args ) : string {
		return WLD_Fields::get( $selector, $args );
	}
}
if ( ! function_exists( 'wld_the' ) ) {
	function wld_the( string $selector, ...$args ) : void {
		echo wld_get( $selector, ...$args );
	}
}
if ( ! function_exists( 'wld_loop' ) ) {
	function wld_loop( $selector_or_posts_array, string $wrapper = '' ) : bool {
		return WLD_Fields::loop( $selector_or_posts_array, $wrapper );
	}
}
if ( ! function_exists( 'wld_wrap' ) ) {
	function wld_wrap( string $selector, string $class = '', bool $force = true, array $attrs = array() ) : bool {
		return WLD_Fields::wrap( $selector, $class, $force, $attrs );
	}
}
if ( ! function_exists( 'wld_has' ) ) {
	function wld_has( ...$selectors_and_maybe_conditional ) : bool {
		return WLD_Fields::has( $selectors_and_maybe_conditional );
	}
}
if ( ! function_exists( 'wld_get_as' ) ) {
	function wld_get_as( string $as_type, string $selector, ...$args ) : string {
		WLD_Fields::next_field_process_as_type( $as_type );

		return wld_get( $selector, ...$args );
	}
}
if ( ! function_exists( 'wld_the_as' ) ) {
	function wld_the_as( string $as_type, string $selector, ...$args ) : void {
		echo wld_get_as( $as_type, $selector, ...$args );
	}
}

/**
 * Templates helpers
 */
if ( ! function_exists( 'wld_get_the_excerpt' ) ) {
	function wld_get_the_excerpt( int $length, bool $filter = false, bool $trim = false, string $after = '' ) : string {
		global $post;
		if ( has_excerpt() ) {
			$output = wp_strip_all_tags( $post->post_excerpt );
		} else {
			$output = get_the_content();
			if ( empty( $output ) && WLD_Theme::$acf_enabled ) {
				$fields = get_fields( $post->ID );
				wld_array_to_excerpt( $fields, $output );
			}
			$output = wp_strip_all_tags( strip_shortcodes( $output ) );
			if ( false === $filter ) {
				$output = str_replace( array( "\r\n", "\r", "\n" ), '', $output );
				$output = trim( str_replace( '&nbsp;', ' ', $output ) );
				$output = preg_replace( '/\s+/', ' ', $output );
			}
			if ( strlen( $output ) > $length ) {
				$output = substr( $output, 0, $length );
				for ( $i = $length - 1; $i >= 0; $i -- ) {
					if ( preg_match( '/(\.|,|!|\?|:|;|\s)/', $output[ $i ] ) ) {
						$output = substr( $output, 0, $i + 1 );
						break;
					}
				}
			}
			if ( $trim ) {
				$output = rtrim( $output, '.,!?:; ' );
			}
		}
		$output = rtrim( $output, "\r\n" ) . $after;
		if ( $filter ) {
			return apply_filters( 'get_the_excerpt', $output );
		}

		return $output;
	}
}
if ( ! function_exists( 'wld_the_excerpt' ) ) {
	function wld_the_excerpt( int $length, bool $filter = false, bool $trim = false, string $after = '' ) : void {
		$output = wld_get_the_excerpt( $length, $filter, $trim, $after );
		if ( $filter ) {
			$output = apply_filters( 'the_excerpt', $output );
		}

		echo wp_kses_post( $output );
	}
}
if ( ! function_exists( 'wld_get_the_replace' ) ) {
	function wld_get_the_replace( ?string $text, string $replace = 'strong' ) : string {
		return str_replace(
			array( '[', ']' ),
			array( '<' . $replace . '>', '</' . $replace . '>' ),
			$text
		);
	}
}
if ( ! function_exists( 'wld_the_replace' ) ) {
	function wld_the_replace( ?string $text, string $replace = 'strong' ) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_replace( $text, $replace );
	}
}
if ( ! function_exists( 'wld_get_the_archive_link' ) ) {
	function wld_get_the_archive_link( $post = null, string $class = 'back-link' ) : string {
		$post = get_post( $post );
		if ( empty( $post ) ) {
			return '';
		}
		$url   = wld_get_the_archive_url( $post );
		$title = wld_get_the_archive_title( $post );
		if ( empty( $url ) || empty( $title ) ) {
			return '';
		}
		$title = __( 'Back to ', 'theme' ) . '"' . $title . '"';

		return '<a href="' . $url . '" class="' . $class . '">' . $title . '</a>';
	}
}
if ( ! function_exists( 'wld_the_archive_link' ) ) {
	function wld_the_archive_link( $post = null, string $class = 'back-link' ) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_archive_link( $post, $class );
	}
}
if ( ! function_exists( 'wld_get_the_archive_url' ) ) {
	function wld_get_the_archive_url( $post = null ) : string {
		$post = get_post( $post );
		if ( empty( $post ) ) {
			return '';
		}
		$post_type = get_post_type( $post );
		if ( 'product' === $post_type && function_exists( 'wc_get_page_id' ) ) {
			$url = get_permalink( wc_get_page_id( 'shop' ) );
		} else {
			$url = get_post_type_archive_link( $post_type );
		}

		return (string) $url;
	}
}
if ( ! function_exists( 'wld_the_archive_url' ) ) {
	function wld_the_archive_url( $post = null ) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_archive_url( $post );
	}
}
if ( ! function_exists( 'wld_get_the_archive_title' ) ) {
	function wld_get_the_archive_title( $post = null ) : string {
		$post = get_post( $post );
		if ( ! $post ) {
			return '';
		}
		$post_type = get_post_type( $post );
		if ( 'post' === $post_type ) {
			return get_the_title( get_option( 'page_for_posts' ) );
		}
		if ( 'product' === $post_type && function_exists( 'wc_get_page_id' ) ) {
			return get_the_title( wc_get_page_id( 'shop' ) );
		}
		$post_type = get_post_type_object( get_post_type( $post ) );
		if ( $post_type ) {
			return $post_type->labels->all_items;
		}

		return '';
	}
}
if ( ! function_exists( 'wld_get_the_by_seo_link' ) ) {
	function wld_get_the_by_seo_link( array $args = array( 'title' => 'Search engine optimization by:' ) ) : string {
		$args = wp_parse_args(
			$args,
			array(
				'name' => '',
				'href' => '',
			)
		);

		return wld_get_the_by_link( $args );
	}
}
if ( ! function_exists( 'wld_the_by_seo_link' ) ) {
	function wld_the_by_seo_link( array $args = array() ) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_by_seo_link( $args );
	}
}
if ( ! function_exists( 'wld_get_the_by' ) ) {
	function wld_get_the_by() : string {
		return wld_get_the_by_link() . wld_get_the_by_seo_link();
	}
}
if ( ! function_exists( 'wld_the_by' ) ) {
	function wld_the_by() : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_by();
	}
}
if ( ! function_exists( 'wld_the_by_link' ) ) {
	function wld_the_by_link( array $args = array() ) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_by_link( $args );
	}
}
if ( ! function_exists( 'wld_get_the_by_link' ) ) {
	function wld_get_the_by_link( array $args = array() ) : string {
		if ( ! is_front_page() ) {
			return '';
		}

		$theme = wp_get_theme();
		$name  = $theme->get( 'Author' );
		$href  = $theme->get( 'AuthorURI' );
		$args  = wp_parse_args(
			$args,
			array(
				'title' => 'Dallas Web Design Agency:',
				'name'  => $name,
				'href'  => $href,
				'attr'  => array(),
			)
		);

		if ( empty( $args['name'] ) || empty( $args['href'] ) ) {
			return '';
		}

		$_attr = array_filter(
			wp_parse_args(
				$args['attr'],
				array(
					'href'   => $args['href'],
					'target' => '_blank',
					'rel'    => 'noopener',
				)
			)
		);

		$attr = '';
		foreach ( $_attr as $k => $v ) {
			$attr .= ' ' . $k . '="' . $v . '"';
		}

		return sprintf(
			'%s%s <span>%s</span></a>',
			wp_kses_attr( 'a', $attr, 'post', wp_allowed_protocols() ),
			esc_html( $args['title'] ),
			esc_html( $args['name'] )
		);
	}
}
if ( ! function_exists( 'wld_get_the_logo' ) ) {
	function wld_get_the_logo(
		string $selector = 'wld_header_logo', string $size = 'full', string $id = 'options', $sizes = ''
	) : string {
		$logo  = '';
		$image = '';
		$attr  = array(
			'loading' => 'wld_header_logo' === $selector ? 'eager' : 'lazy',
			'alt'     => get_bloginfo( 'name' ),
		);
		if ( $sizes ) {
			$attr['sizes'] = $sizes;
		}

		foreach ( array( '', '_2' ) as $i ) {
			$image .= WLD_Images::get_img(
				(int) get_field( $selector . $i, $id, false ),
				$size,
				$attr
			);
		}
		if ( $image ) {
			$logo .= is_front_page() ? $image : '<a href="' . home_url() . '">' . $image . '</a>';
		}

		WLD_Images::the_sprite();

		return '<div class="logo">' . $logo . '</div>';
	}
}
if ( ! function_exists( 'wld_the_logo' ) ) {
	function wld_the_logo(
		string $selector = 'wld_header_logo', string $size = 'full', string $id = 'options', string $sizes = ''
	) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_logo( $selector, $size, $id, $sizes );
	}
}
if ( ! function_exists( 'wld_get_the_nav' ) ) {
	function wld_get_the_nav( string $location_label, bool $is_mobile = false, array $args = array() ) : string {
		$defaults = array( 'accessibility' => false );

		$defaults['bam_block_name'] = WLD_Nav::get_bam_block_name( $location_label );
		if ( $is_mobile ) {
			$defaults['bam_block_name'] .= '-mobile';
		} else {
			$container_navs = array(
				'Header Main'   => true,
				'Header Second' => true,
				'Footer Main'   => true,
			);

			if ( isset( $container_navs[ $location_label ] ) ) {
				$defaults['container'] = 'nav';
			}

			$accessibility_navs = array(
				'Header Main'   => true,
				'Header Second' => true,
				'Footer Links'  => true,
			);

			if ( isset( $accessibility_navs[ $location_label ] ) ) {
				$defaults['accessibility'] = true;
			}
		}

		$defaults['theme_location']       = WLD_Nav::get_location( $location_label );
		$defaults['fallback_cb']          = '__return_empty_string';
		$defaults['is_mobile']            = $is_mobile;
		$defaults['echo']                 = false;
		$defaults['container_aria_label'] = $location_label;
		$defaults['items_wrap']           = '<ul class="%2$s">%3$s</ul>';

		return (string) wp_nav_menu( wp_parse_args( $args, $defaults ) );
	}
}
if ( ! function_exists( 'wld_the_nav' ) ) {
	function wld_the_nav( string $location_label, bool $is_mobile = false, array $args = array() ) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_nav( $location_label, $is_mobile, $args );
	}
}
if ( ! function_exists( 'wld_nav_has_sub_menu' ) ) {
	function wld_nav_has_sub_menu( array $menu_location_labels ) : bool {
		return WLD_Nav::has_sub_menu( $menu_location_labels );
	}
}
if ( ! function_exists( 'wld_get_supported_video_url' ) ) {
	function wld_get_supported_video_url( ?string $url ) : string {
		$video_url = '';

		if ( $url ) {
			$youtube_id = WLD_YouTube::get_id( $url );
			if ( $youtube_id ) {
				$video_url = 'https://www.youtube.com/watch?v=' . $youtube_id;
			} elseif ( str_contains( $url, 'vimeo' ) ) {
				preg_match( '~vimeo\.com/(?>video/)?(\d+)~', $url, $matches );
				if ( isset( $matches[1] ) ) {
					$video_url = 'https://vimeo.com/' . $matches[1];
				}
			}
		}

		return $video_url;
	}
}
if ( ! function_exists( 'wld_get_supported_video_embed_url' ) ) {
	function wld_get_supported_video_embed_url( ?string $url ) : string {
		$video_url = '';

		if ( $url ) {
			$youtube_id = WLD_YouTube::get_id( $url );
			if ( $youtube_id ) {
				$video_url = 'https://www.youtube.com/embed/' . $youtube_id;
			} elseif ( str_contains( $url, 'vimeo' ) ) {
				preg_match( '~vimeo\.com/(?>video/)?(\d+)(\?.+)?~', $url, $matches );
				if ( isset( $matches[1] ) ) {
					if ( $matches[2] ) {
						$params = $matches[2];
					} else {
						$params = '?title=0&byline=0&portrait=0';
					}

					$video_url = 'https://player.vimeo.com/video/' . $matches[1] . $params;
				}
			}
		}

		return $video_url;
	}
}
if ( ! function_exists( 'wld_get_the_pagination' ) ) {
	/** @noinspection DuplicatedCode, HtmlUnknownTarget */
	function wld_get_the_pagination( array $args = array() ) : string {
		global $wp_query;

		$args      = apply_filters( 'wld_pagination_args', $args );
		$old_query = $wp_query;
		if ( isset( $args['query'] ) ) {
			// We deliberately change the global variable here to change the output later we reset it.
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_query = $args['query'];
		}

		$format = '';
		if ( isset( $args['format'] ) ) {
			$format = trim( str_replace( '=%#%', '', $args['format'] ), '?' );
		}

		$defaults = array(
			'type'      => 'list',
			'prev_text' => esc_html__( '&#8592; Previous', 'theme' ),
			'next_text' => esc_html__( 'Next &#8594;', 'theme' ),
		);

		$pagination = paginate_links( wp_parse_args( apply_filters( 'wld_pagination_args', $args ), $defaults ) );

		if ( ! empty( $args['first_last'] ) ) {

			$total   = $wp_query->max_num_pages;
			$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

			$first_url = get_pagenum_link();
			$last_url  = get_pagenum_link( $total );
			if ( $format ) {
				$last_url  = str_replace(
					array( '%_%', '%#%' ),
					array( $args['format'], $total ),
					trailingslashit( explode( '?', $first_url )[0] ) . '%_%'
				);
				$first_url = remove_query_arg( $format, $first_url );
			}

			$first_text  = esc_html__( 'First Page', 'theme' );
			$first_link  = sprintf( '<a href="%s" class="first-page">%s</a>', $first_url, $first_text );
			$last_text   = esc_html__( 'Last Page', 'theme' );
			$last_link   = sprintf( '<a href="%s" class="last-page">%s</a>', $last_url, $last_text );
			$pattern     = array();
			$replacement = array();

			if ( $current > 1 ) {
				$pattern[]     = '/(<ul[^>]*>)/';
				$replacement[] = "$1<li>$first_link</li>";
			}

			if ( $current < $total ) {
				$pattern[]     = '/(<\/ul>)/';
				$replacement[] = "<li>$last_link</li>$1";
			}

			$pagination = preg_replace( $pattern, $replacement, $pagination );
		}

		$pagination = str_replace(
			array( "<ul class='page-numbers" ),
			array( "<ul class='page-numbers pagination" ),
			$pagination
		);

		// We reset the variable to its initial state, changed above.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$wp_query = $old_query;

		return (string) $pagination;
	}
}
if ( ! function_exists( 'wld_the_pagination' ) ) {
	function wld_the_pagination( array $args = array() ) : void {
		// todo: Escape output.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wld_get_the_pagination( $args );
	}
}
if ( ! function_exists( 'wld_array_to_excerpt' ) ) {
	function wld_array_to_excerpt( $values, string &$excerpt ) : void {
		$keys = array( 'text', 'content', 'title', 'subtitle' );
		if ( is_array( $values ) ) {
			foreach ( $values as $key => $value ) {
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) && ! isset( $value['filename'] ) ) {
						wld_array_to_excerpt( $value, $excerpt );
					} elseif ( is_string( $value ) && in_array( $key, $keys, true ) ) {
						$excerpt .= $value . "\n ";
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'wld_remove_filter_for_class' ) ) {
	function wld_remove_filter_for_class( string $hook, string $class, string $method, int $priority = 10 ) : void {
		global $wp_filter;

		$callbacks = $wp_filter[ $hook ][ $priority ] ?? array();
		if ( $callbacks ) {
			foreach ( $callbacks as $id => $filter ) {
				if (
					is_array( $filter['function'] ) &&
					! empty( $filter['function'][0] ) &&
					! empty( $filter['function'][1] ) &&
					$filter['function'][1] === $method
				) {
					if ( is_object( $filter['function'][0] ) ) {
						$filter_class = get_class( $filter['function'][0] );
					} else {
						$filter_class = $filter['function'][0];
					}
					if ( $class === $filter_class ) {
						unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $id ] );
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'wld_get_the_header_classes' ) ) {
	function wld_get_the_header_classes() : string {
		$classes = apply_filters( 'wld_header_classes', array( 'page-header' ) );
		foreach ( $classes as $i => $class ) {
			$classes[ $i ] = sanitize_html_class( $class );
		}

		return implode( ' ', array_unique( $classes ) );
	}
}
