<?php
/*
 * This template is used for the display of single pages.
 */
get_header();
?>
	<!-- Home Content -->
	<section class="content-container cf">
		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<article class="content">

			<?php get_template_part( 'yoast', 'breadcrumbs' ); // Yoast Breadcrumbs ?>

			<?php get_template_part( 'loop', 'page' ); ?>

			<?php comments_template(); ?>

		</article>

		<?php get_sidebar(); ?>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>
	</section>
<?php get_footer(); ?>