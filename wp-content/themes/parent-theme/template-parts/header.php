<header class="<?php echo esc_attr( wld_get_the_header_classes() ); ?>">
	<div class="page-header__wrapper">
		<?php if ( WLD_Is::woocommerce_enabled() ) : ?>
			<button class="open-mobile-menu-button" aria-controls="mobile-menu" aria-expanded="false">
				<span class='open-mobile-menu-button__item open-mobile-menu-button__item_style-1'></span>
				<span class='open-mobile-menu-button__item open-mobile-menu-button__item_style-2'></span>
				<span class='open-mobile-menu-button__item open-mobile-menu-button__item_style-3'></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Open Menu', 'theme' ); ?></span>
			</button>
			<?php get_template_part( 'template-parts/header-content' ); ?>
			<?php WLD_WC_Cart_In_Menu::the_open_button(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/header-content' ); ?>
			<button class="open-mobile-menu-button" aria-controls="mobile-menu" aria-expanded="false">
				<span class='open-mobile-menu-button__item open-mobile-menu-button__item_style-1'></span>
				<span class='open-mobile-menu-button__item open-mobile-menu-button__item_style-2'></span>
				<span class='open-mobile-menu-button__item open-mobile-menu-button__item_style-3'></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Open Menu', 'theme' ); ?></span>
			</button>
		<?php endif; ?>
	</div>
</header>
