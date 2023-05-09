<?php
$wrap_class = 'mobile-menu';
if ( ! WLD_Nav::has_sub_menu( array( 'Header Main', 'Header Second' ) ) ) {
	$wrap_class .= ' mobile-menu_center';
}
?>
<aside class="<?php echo esc_attr( $wrap_class ); ?>" aria-hidden="true">
	<div class="mobile-menu__wrapper">
		<?php get_template_part( 'template-parts/mobile-menu-content' ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Close Menu', 'theme' ); ?></span>
	</div>
</aside>
