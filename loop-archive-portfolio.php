<?php
	// Loop through posts
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
?>
		<section id="<?php the_ID(); ?>" <?php post_class( 'medium-block portfolio-item portfolio-project' ); ?>>
			<figure class="block-thumb">
				<?php sds_featured_image( true, 'symphony-600x400', 'div' ); ?>

				<figcaption class="block-info">
					<h2 class="project-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				</figcaption>
			</figure>
		</section>
<?php
		endwhile;
	else: // No Posts
?>
	<section class="no-results no-posts no-archive-results post">
		<?php sds_no_posts(); ?>
	</section>
<?php endif; ?>