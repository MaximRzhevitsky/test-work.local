<?php
	$terms               = get_terms(
		array(
			'taxonomy' => 'faq_category',
		)
	);
	$posts               = get_posts(
		array(
			'numberposts'      => - 1,
			'post_type'        => 'faq',
			'suppress_filters' => false,
		)
	);
	$iteration           = 2;
	$is_first            = false;
	$iteration_accordion = 1;
?>
<section class="faq">
	<div class="inner">
		<div class="faq__search-block">
			<?php while ( wld_loop( 'faq_custom_fields' ) ) {
				wld_the(
					'image',
					'1170x216',
					'',
					array(
						'class' => 'object-fit object-fit-cover'
					)
				);
				wld_the( 'title', 'faq__title' );
			}
			?>
			<form class="faq__search-form form" role="search" method="get" action="/">
				<div class="form__item">
					<label class="form__label" for="fag-search-field">Type your question</label>
					<input class="form__input" id="fag-search-field" type="search" value="" name="s">
				</div>
				<input class="form__submit btn" type="submit" value="Search">
			</form>
		</div>

		<div class="faq__tabs">
			<div class="tabs " id="tab-1">
				<?php if ( $terms ) : ?>
				<div class="tabs__tablist" role="tablist" >
					<button class="tabs__button" id='tab-1-1'
							type="button"
							role="tab"
							aria-controls='tabpanel-1-1'>
						<span class="focus"><?php esc_html_e( 'All', 'theme' ); ?></span>
					</button>
					<?php foreach ( $terms as $term ) : ?>
						<button class="tabs__button" id='tab-1-<?php echo esc_attr( $iteration ); ?>'
								type="button"
								role="tab"
								aria-controls='tabpanel-1-<?php echo esc_attr( $iteration ); ?>'>
							<span class="focus"><?php esc_html_e( $term->name, 'theme' ); ?></span>
						</button>
						<?php $iteration++; ?>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
					<div class="tabs__tabpanel"
						 id='tabpanel-1-1'
						 role="tabpanel"
						 tabindex="0"
						 aria-labelledby='tab-1-1
					'>
					<?php foreach( $posts as $post ): ?>
						<?php
							setup_postdata( $post );
						?>
						<?php if ( 1 === $iteration_accordion ) : ?>
							<div class="accordion accordion_active" id='accordion-<?php echo esc_attr( $iteration_accordion ); ?>'>
								<h2 class="accordion__header">
									<button class="accordion__trigger" id='accordion-btn-<?php echo esc_attr( $iteration_accordion ); ?>' type="button" aria-expanded="true" aria-controls='accordion-panel-<?php echo esc_attr( $iteration_accordion ); ?>'>
										<span class="accordion__title"><?php the_title(); ?></span>
									</button>
								</h2>
								<div class="accordion__panel" id='accordion-panel-<?php echo esc_attr( $iteration_accordion ); ?>' role="region" aria-labelledby='accordion-btn-<?php echo esc_attr( $iteration_accordion ); ?>'>
									<?php the_content(); ?>
								</div>
							</div>
						<?php else : ?>
							<div class="accordion" id='accordion-<?php echo esc_attr( $iteration_accordion ); ?>'>
								<h2 class="accordion__header">
									<button class="accordion__trigger" id='accordion-btn-<?php echo esc_attr( $iteration_accordion ); ?>' type="button" aria-expanded="false" aria-controls='accordion-panel-<?php echo esc_attr( $iteration_accordion ); ?>'>
										<span class="accordion__title"><?php the_title(); ?></span>
									</button>
								</h2>
								<div class="accordion__panel" id='accordion-panel-<?php echo esc_attr( $iteration_accordion ); ?>' role="region" aria-labelledby='accordion-btn-<?php echo esc_attr( $iteration_accordion ); ?>' hidden>
									<?php the_content(); ?>
								</div>
							</div>
						<?php endif; ?>
						<?php $iteration_accordion++; ?>
					<?php endforeach; ?>
				</div>
				<?php if ( $terms ) : ?>
					<?php $iteration = 2; ?>
					<?php foreach ( $terms as $term ) : ?>
						<div class="tabs__tabpanel"  is-hidden id='tabpanel-1-<?php echo esc_attr( $iteration ); ?>'
							 role="tabpanel"
							 tabindex="0"
							 aria-labelledby='tab-1-<?php echo esc_attr( $iteration ); ?>'>
							<?php
							$posts = get_posts(
								array(
									'numberposts'  => - 1,
									'post_type'    => 'faq',
									'tax_query' => array(
										array(
											'taxonomy' => 'faq_category',
											'field'    => 'slug',
											'terms'    => $term->slug
										)
									)
								)
							);
							?>
							<?php foreach( $posts as $key => $post ): ?>
								<?php setup_postdata( $post ); ?>
								<div class="accordion" id='accordion-<?php echo esc_attr( $iteration_accordion ); ?>'>
									<h2 class="accordion__header">
										<button class="accordion__trigger"
												id='accordion-btn-<?php echo esc_attr( $iteration_accordion ); ?>'
												type="button" aria-expanded="false"
												aria-controls='accordion-panel-<?php echo esc_attr( $iteration_accordion ); ?>
										'>
											<span class="accordion__title"><?php the_title(); ?></span>
										</button>
									</h2>
									<div class="accordion__panel"
										 id='accordion-panel-<?php echo esc_attr( $iteration_accordion ); ?>'
										 role="region"
										 aria-labelledby='accordion-btn-<?php echo esc_attr( $iteration_accordion ); ?>' hidden>
										<?php the_content(); ?>
									</div>
								</div>
								<?php $iteration_accordion++; ?>
							<?php endforeach; ?>
							<?php $iteration++; ?>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
