<?php
/*
 * Template Name: Landing Page
 * This template is used for the display of landing pages.
 */

get_header( 'landing-page' ); ?>
	<section class="content-container cf">
		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<article class="content full-width-content cf">

			<?php get_template_part( 'loop', 'page-full-width' ); // Loop - Full Width ?>

			<div class="clear"></div>

			<?php comments_template(); // Comments ?>
		</article>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
			</div>
		<?php endif; ?>
	</section>

<?php get_footer( 'landing-page' ); ?>