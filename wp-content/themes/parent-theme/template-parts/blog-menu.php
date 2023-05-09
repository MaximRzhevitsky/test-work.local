<section class="blog-nav">
	<div class="inner">
		<h2 class="blog-nav__title">
			<span class="blog-nav__text"><?php echo strip_tags( wld_get( 'title' ) ); ?></span>
		</h2>
		<?php
		wld_the(
			'menu',
			array( 'bam_block_name' => 'menu-blog' )
		);
		?>
	</div>
</section>
