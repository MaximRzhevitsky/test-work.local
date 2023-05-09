<?php

class WLD_Admin_Bar_Base {
	public static function init() : void {
		add_action(
			'admin_bar_menu',
			array( static::class, 'hide_comments' ),
			999
		);
		add_action(
			'show_admin_bar',
			array( static::class, 'is_show' )
		);
	}

	public static function is_show( bool $show ) : bool {
		if ( $show ) {
			$user_can = current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' );
			$show     = apply_filters( 'wld_show_admin_bar', $user_can );
			if ( $show ) {
				add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
				add_action( 'admin_bar_menu', array( static::class, 'add_button' ) );
				add_action( 'wp_print_footer_scripts', array( static::class, 'script' ), PHP_INT_MAX );
				add_action( 'wp_head', array( static::class, 'style' ) );
			}
		}

		return $show;
	}

	public static function add_button() : void {
		/**
		 * @var WP_Admin_Bar $wp_admin_bar
		 */
		global $wp_admin_bar;
		if ( ! is_admin_bar_showing() || is_admin() ) {
			return;
		}
		$title = '<button type="button" role="button" class="ab-item"><span class="ab-icon"></span></button>';
		$wp_admin_bar->add_node(
			array(
				'id'    => 'collapse',
				'title' => $title,
				'theme' => false,
				'meta'  => array(
					'title' => 'collapse',
				),
			)
		);
	}

	public static function script() : void {
		?>
		<script>
			( function () {
				const wpBar = document.querySelector('#wpadminbar');
				const qmBar = wpBar.querySelector('#wp-admin-bar-query-monitor');
				const button = document.querySelector('#wp-admin-bar-collapse');

				function toggle(open) {
					if (typeof open === 'boolean') {
						wpBar.classList.toggle('close', open);
					} else {
						wpBar.classList.toggle('close');
					}
				}

				function qmLoad() {
					setTimeout(() => {
						if (qmBar.querySelector('#wp-admin-bar-query-monitor-db_queries')) {
							if (!qmBar.classList.contains('qm-all-clear')) {
								toggle(true);
							}
						} else {
							qmLoad();
						}
					}, 500);
				}

				wpBar.classList.add('init', 'close');
				button.addEventListener('click', toggle);

				if (qmBar && typeof jQuery !== 'undefined') {
					qmLoad();

					jQuery(document).ajaxSuccess(function (event, response) {
						const errors = response.getResponseHeader('X-QM-php_errors-error-count');
						if (errors) {
							toggle(true);
						}
					});
				}
			} )();
		</script>
		<?php
	}

	public static function style() : void {
		echo '
		<style>
			html {
				scroll-padding-top: 0;
			}
			#wpadminbar:not(.init) {
				opacity:0;
			}
			#wpadminbar #wp-admin-bar-collapse button {
				background: transparent;
				border: 0;
				outline: 0;
				cursor: pointer;
			}
			#wpadminbar #wp-admin-bar-collapse > .ab-item .ab-icon:before {
				content: "\\f333";
				top: 2px;
			}

			#wpadminbar.close {
				opacity: 0.1;
				width: 46px;
				min-width: 0;
			}

			#wpadminbar.close #wp-toolbar>ul>li:not(#wp-admin-bar-collapse) {
				display: none !important;
			}
			@media all and (max-width: 782px) {
				#wpadminbar {
					display: none !important;
				}
			}
		</style>
		';
	}

	public static function hide_comments( WP_Admin_Bar $wp_admin_bar ) : void {
		$wp_admin_bar->remove_menu( 'comments' );
	}
}
