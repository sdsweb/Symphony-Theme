<?php
/*
 * This template is used for the display of Jetpack & Portfolio archives.
 */

get_header(); ?>

	<section class="content-container cf">

		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<!--Post Loop -->
		<article class="content portfolio-content">

			<?php get_template_part( 'yoast', 'breadcrumbs' ); // Yoast Breadcrumbs ?>

			<?php get_template_part( 'loop', 'archive-portfolio' ); // Loop - Archive Portfolio ?>

			<?php get_template_part( 'post', 'navigation' ); // Post Navigation ?>

		</article>

		<!-- Sidebar -->
		<?php get_sidebar(); ?>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="clear"></div>
			</div>
		<?php endif; ?>

	</section>

<?php get_footer(); ?>