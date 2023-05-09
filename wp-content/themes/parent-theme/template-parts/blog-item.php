<?php
global $wp_query;

$is_first_post = $wp_query->is_main_query() && 0 === $wp_query->current_post;
?>
<li class="blog-items__item">
	<article class="blog-item accessibility-card<?php echo $is_first_post ? ' blog-item_two-columns' : ''; ?>">
		<div class="blog-item__text">
			<h2 class="blog-item__title title">
				<a class="blog-item__link"
				   href="<?php the_permalink(); ?>"
				   aria-describedby="read-more-<?php echo esc_attr( $wp_query->current_post ); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
			<p class="blog-item__except"><?php wld_the_excerpt( 100, false, true, '...' ); ?></p>
			<div class="blog-item__posted">
				<?php
				printf( // translators: %s - posted date
					esc_html__( 'Posted - %s', 'theme' ),
					get_the_date( 'F j, Y' )
				);
				?>
			</div>
			<div class="blog-item__more"
				 aria-hidden="true"
				 id="read-more-<?php echo esc_attr( $wp_query->current_post ); ?>">
				<?php esc_html_e( 'Learn More', 'theme' ); ?>
			</div>
			<?php if ( has_category() ) : ?>
				<aside class="blog-categories blog-item__categories">
					<h3 class="screen-reader-text"><?php esc_html_e( 'Categories:', 'theme' ); ?></h3>
					<?php
					echo str_replace(
						'<a ',
						'<a class="blog-categories_link"',
						get_the_category_list( ' ' )
					);
					?>
				</aside>
			<?php endif; ?>
		</div>
		<div class="blog-thumbnail blog-item__thumbnail">
			<?php
			the_post_thumbnail(
				$is_first_post ? '569x441' : '262x258',
				array(
					'class' => 'blog-thumbnail__image',
				),
			);
			?>
		</div>
	</article>
</li>
