<section class="keep-me-informed">
	<div class="inner">
		<?php wld_the( 'title', 'keep-me-informed__title' ); ?>
		<?php wld_the( 'text', 'keep-me-informed__text' ); ?>
		<?php wld_the( 'form' ); ?>
		<?php if ( wld_has( 'social_links' ) ) : ?>
				<?php while ( wld_loop( 'social_links' ) ) : ?>
					<h2 class="keep-me-informed__subtitle">
						<span class="keep-me-informed__subtitle-text">
							<?php wld_the( 'title' ); ?>
						</span>
					</h2>
					<?php wld_the( 'social_links' ); ?>
				<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>
