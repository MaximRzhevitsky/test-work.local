<footer class="page-footer">
	<div class="inner">
		<div class="page-footer__top">
			<?php wld_the_nav( 'Footer Main' ); ?>
			<div class="page-footer__content">
				<?php wld_the( 'wld_footer_text', '<div class="page-footer__contacts">' ); ?>
				<?php wld_the( 'wld_footer_social_links', 'size_30' ); ?>
			</div>
		</div>
		<div class="page-footer__bottom">
			<div class="page-footer__wrapper">
				<?php wld_the( 'wld_footer_copyright', '<p class="page-footer__copyright">' ); ?>
				<?php wld_the_nav( 'Footer Links' ); ?>
			</div>
			<div class="page-footer__by">
				<?php wld_the_by(); ?>
			</div>
		</div>
	</div>
</footer>
