<?php
/*
 * This template is used for the display images.
 */

get_header(); ?>

	<section class="content-container image-content image-attachment image cf">
		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<?php get_template_part( 'yoast', 'breadcrumbs' ); // Yoast Breadcrumbs ?>

		<?php get_template_part( 'loop', 'attachment-image' ); ?>

		<?php comments_template(); // Comments ?>

		<!-- Sidebar -->
		<?php get_sidebar(); ?>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>
	</section>

<?php get_footer(); ?>