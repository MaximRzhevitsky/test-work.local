<section class="blog-post">
	<div class="inner">
		<div class="blog-post__item">
			<div class="blog-post__content">
				<h1 class="blog-post__title"><?php the_title(); ?></h1>
				<p class="blog-post__except"><?php echo wld_get( 'excerpt_content' ); ?></p>
				<?php if ( has_category() ) : ?>
					<div class="blog-categories blog-post__categories">
						<h3 class="screen-reader-text">Categories:</h3>
						<?php echo preg_replace('/<a /', '<a class="blog-categories_link"', get_the_category_list( ' ' ) ); ?>
					</div>
				<?php endif; ?>
				<div class="blog-post__posted">
					<?php
						printf( // translators: %s - posted date
							esc_html__( 'Posted - %s', 'theme' ),
							get_the_date( 'F j, Y' )
						);
					?>
				</div>
			</div>
			<div class="blog-post__thumbnail">
				<?php the_post_thumbnail( '700x0', array( 'class' => 'blog-post__image' ) ); ?>
			</div>
		</div>
	</div>
</section>
