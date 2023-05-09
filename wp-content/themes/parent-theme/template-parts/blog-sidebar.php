<div class="blog-content__sidebar">
	<div class="widget">
		<h2><?php esc_html_e( 'Search', 'theme' ); ?></h2>
	</div>
	<div class="widget">
		<form role="search" method="get" action="<?php echo esc_attr( home_url() ); ?>" class="wp-block-search__button-inside wp-block-search__icon-button aligncenter wp-block-search">
			<label for="wp-block-search__input-1" class="wp-block-search__label"><?php esc_html_e( 'Search', 'theme' ); ?></label>
			<div class="wp-block-search__inside-wrapper ">
				<input type="search" id="wp-block-search__input-1" class="wp-block-search__input" name="s" value="" placeholder="" required="">
				<button type="submit" class="wp-block-search__button has-icon wp-element-button" aria-label="Search">
					<svg class="search-icon" viewBox="0 0 24 24" width="24" height="24">
						<path d="M13.5 6C10.5 6 8 8.5 8 11.5c0 1.1.3 2.1.9 3l-3.4 3 1 1.1 3.4-2.9c1 .9 2.2 1.4 3.6 1.4 3 0 5.5-2.5 5.5-5.5C19 8.5 16.5 6 13.5 6zm0 9.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"></path>
					</svg>
				</button>
			</div>
		</form>
	</div>
	<div class="widget">
		<h2><?php esc_html_e( 'Stay Connected', 'theme' ); ?></h2>
	</div>
	<div class="widget">
		<?php echo do_shortcode( '[gravityform id="7" title="true"]' ); ?>
	</div>
	<?php while( wld_loop('wld_social_links_sidebar') ) : ?>
		<div class="widget">
			<h2 class="widget__title"><?php wld_the( 'title' ); ?></h2>
		</div>
		<?php wld_the( 'social_links' ); ?>
	<?php endwhile; ?>
</div>
