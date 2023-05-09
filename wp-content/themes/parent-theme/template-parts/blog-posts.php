<section class="blog-content">
	<div class="inner">
		<div class="blog-content__wrapper">
			<div class="blog-content__content">
				<?php if ( have_posts() ) : ?>
					<ul class="blog-items">
						<?php while ( have_posts() ) : ?>
							<?php the_post(); ?>
							<?php get_template_part( 'template-parts/blog-item' ); ?>
						<?php endwhile; ?>
					</ul>
					<?php wld_the_pagination(); ?>
				<?php else : ?>
					<p><?php esc_html_e( 'Nothing found', 'theme' ); ?></p>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'template-parts/blog-sidebar' ); ?>
		</div>
	</div>
</section>
