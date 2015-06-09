<?php
/**
 * This template is used for the display of blog posts (archive; river).
 */

get_header(); ?>

	<section class="content-container cf">
		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<!--Post Loop -->
		<article class="content">

			<?php get_template_part( 'yoast', 'breadcrumbs' ); // Yoast Breadcrumbs ?>

			<?php get_template_part( 'loop', 'home' ); ?>

			<?php get_template_part( 'post', 'navigation' ); // Post Navigation ?>

			<?php comments_template(); ?>

		</article>

		<!-- Sidebar -->
		<?php get_sidebar(); ?>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>
	</section>

<?php get_footer(); ?>