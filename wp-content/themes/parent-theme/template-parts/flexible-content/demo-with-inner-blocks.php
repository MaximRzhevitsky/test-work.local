<div class="content-with-sidebar">
	<div class="inner">
		<div class="content-with-sidebar__content">
			<?php
			if ( have_rows( 'inner_content' ) ) {
				while ( have_rows( 'inner_content' ) ) {
					the_row();
					WLD_ACF_Flex_Content::the_content();
				}
			}
			?>
		</div>
		<div class="content-with-sidebar__sidebar">
			<?php
			if ( have_rows( 'sidebar' ) ) {
				while ( have_rows( 'sidebar' ) ) {
					the_row();
					WLD_ACF_Flex_Content::the_content( 'template-parts/flexible-content/inner-blocks/' );
				}
			}
			?>
		</div>
	</div>
</div>
