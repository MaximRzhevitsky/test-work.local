<section class="blog-content blog-content_single">
	<div class="inner">
		<div class="blog-content__wrapper">
			<div class="blog-content__content">
				<?php the_content(); ?>
				<hr>
				<?php get_template_part( 'template-parts/blog-share-post' ); ?>
			</div>
			<?php get_template_part( 'template-parts/blog-sidebar' ); ?>
		</div>
	</div>
</section>
