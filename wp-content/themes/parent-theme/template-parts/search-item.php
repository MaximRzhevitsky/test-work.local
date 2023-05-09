<article class="search-result__item accessibility-card">
	<div class="search-result__content">
		<h2 class="search-result__title title">
			<a class="search-result__link" href="<?php the_permalink(); ?>" aria-describedby="read-more-1"><?php the_title(); ?></a>
		</h2>
		<p class="search-result__text"><?php wld_the_excerpt( 375 ); ?></p>
		<div class="search-result__more" aria-hidden="true" id="read-more-1">Read more</div>
	</div>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="search-result__thumbnail">
			<?php the_post_thumbnail( '200x200', array(
				'class' => 'search-result__image'
			) ); ?>
		</div>
	<?php endif; ?>
</article>
