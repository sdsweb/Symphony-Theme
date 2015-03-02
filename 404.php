<?php
/*
 * This template is used for the display of 404 (Not Found) errors.
 */

get_header(); ?>

<section class="content-container cf">
	<?php if ( ! symphony_is_fixed_width() ) : ?>
		<div class="in">
	<?php endif; ?>

	<!--Post Loop -->
	<article class="content">

		<section class="404-error no-posts post">
			<article class="post-content">
				<h1 title="<?php _e( '404 Error', 'symphony' ); ?>" class="page-title"><?php _e( '404 Error', 'symphony' ); ?></h1>
				<p><?php _e( 'We apologize but something when wrong while trying to find what you were looking for. Please use the navigation below to navigate to your destination.', 'symphony' ); ?></p>

				<section id="search-again" class="search-again search-block no-posts no-search-results">
					<p><?php _e( 'Search:', 'symphony' ); ?></p>
					<?php echo get_search_form(); ?>
				</section>

				<?php sds_sitemap(); ?>
			</article>
		</section>

	</article>

	<!-- Sidebar -->
	<?php get_sidebar(); ?>

	<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="clear"></div>
		</div>
	<?php endif; ?>
</section>

<?php get_footer(); ?>