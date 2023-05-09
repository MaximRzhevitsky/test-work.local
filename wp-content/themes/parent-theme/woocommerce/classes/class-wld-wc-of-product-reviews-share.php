<?php

class WLD_WC_OF_Product_Reviews_Share {
	protected static bool $is_enqueue = false;

	public static function init(): void {
		add_action(
			'comment_footer',
			array( static::class, 'output_share_block' ),
			10
		);
		add_action(
			'wp_enqueue_scripts',
			array( static::class, 'register_script' ),
			10
		);
	}

	public static function output_share_block( WP_Comment $comment ): void {

		/** @noinspection HtmlUnknownTarget */
		printf(
			'
				<div class="share share_close" role="group" data-title="%s" data-text="%s">
					<h4 class="screen-reader-text">%s</h4>
					<button class="share__toggle-button">%s</button>
					<ul class="share__list">
						<li><button class="share__open-button" data-network="facebook">%s</button></li>
						<li><button class="share__open-button" data-network="twitter">%s</button></li>
						<li><button class="share__open-button" data-network="linkedin">%s</button></li>
					</ul>
				</div>
			',
			esc_attr( wp_strip_all_tags( get_comment_meta( $comment->comment_ID, 'title', true ) ) ),
			esc_attr( wp_strip_all_tags( $comment->comment_content ) ),
			esc_html__( 'Share this comment in social networks?', 'parent-theme' ),
			esc_html__( 'Share', 'parent-theme' ),
			esc_html__( 'Facebook', 'parent-theme' ),
			esc_html__( 'Twitter', 'parent-theme' ),
			esc_html__( 'LinkedIn', 'parent-theme' )
		);

		static::enqueue_scripts();
	}

	public static function register_script(): void {
		wp_register_script( 'theme-reviews-share', false, array(), 1, true );
	}

	protected static function enqueue_scripts(): void {
		if ( static::$is_enqueue || ! is_product() ) {
			return;
		}

		static::$is_enqueue = true;

		wp_enqueue_script( 'theme-reviews-share' );

		/** @noinspection JSUnresolvedVariable */
		wp_add_inline_script(
			'theme-reviews-share',
			/** @lang JavaScript */ <<<JS
			( function() {
				async function initBlock(block) {
					const title = encodeURIComponent(block.dataset.title);
					const text = encodeURIComponent(block.dataset.text);
					const url = encodeURIComponent(window.location.href);
					const toggleButton = block.querySelector( '.share__toggle-button' );
					const openButtons = block.querySelectorAll( '.share__open-button' );

					function openNetwork( network ) {
						let openUrl = '';

						if ( 'facebook' === network) {
							openUrl = 'https://www.facebook.com/sharer.php' +
								'?p[title]=' + title  +
								'&p[summary]=' + text  +
								'&p[url]=' + url
						} else if ('twitter' === network) {
							openUrl = 'https://twitter.com/share' +
								'?text=' + title  + ' ' + text +
								'&url=' + url;
						} else if ('linkedin' === network) {
							openUrl = 'https://www.linkedin.com/shareArticle?mini=true' +
								'&title=' + title  + '&summary=' + text + '&url=' + url;
						} else {
							return;
						}

						window.open( openUrl, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');
					}

					toggleButton.addEventListener( 'click', () => block.classList.toggle( 'share_close' ) );

					openButtons.forEach( ( button ) => {
						button.addEventListener( 'click', () => openNetwork( button.dataset.network ) );
					} );
				}

				document.querySelectorAll( '.share' ).forEach( (block) => initBlock( block ) );
			} )();
JS
		);
	}
}
