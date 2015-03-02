<?php
	global $multipage;

	// Loop through posts
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
?>
		<section id="<?php the_ID(); ?>" <?php post_class( 'latest-post cf' ); ?>>
			<?php if ( has_post_thumbnail() ): ?>
				<div class="latest-post-thumb">
					<?php sds_featured_image(); ?>
				</div>
			<?php endif; ?>

			<article class="latest-post-info">
				<h2 class="latest-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

				<p class="latest-post-date">
					<?php printf( __( 'Posted on %1$s by %2$s - %3$s', 'symphony' ) , get_the_time( get_option( 'date_format' ) ), '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a>', '<a href="' . get_comments_link() . '">' . get_comments_number_text() . '</a>' ); ?>
				</p>

				<?php
					// Show the excerpt if it exists
					if ( has_excerpt() )
						the_excerpt();
					// Otherwise show the content
					else
						the_content();
				?>

				<section class="clear">
					<a href="<?php the_permalink(); ?>" class="more-link post-button"><?php echo symphony_more_link_label(); ?></a>
				</section>

				<div class="clear"></div>

				<?php edit_post_link( __( 'Edit Post', 'symphony' ) ); // Allow logged in users to edit ?>

				<div class="clear"></div>

				<?php if ( $multipage ) : ?>
					<section class="single-post-navigation single-post-pagination wp-link-pages">
						<?php wp_link_pages(); ?>
					</section>

					<div class="clear"></div>
				<?php endif; ?>

				<?php if ( $post->post_type !== 'attachment' ) : // Post Meta Data (tags, categories, etc...) ?>
					<section class="post-meta">
						<?php
							if ( symphony_show_post_meta() )
								sds_post_meta();
						?>
					</section>

					<div class="clear"></div>
				<?php endif ?>

			</article>
		</section>

		<div class="clear"></div>

		<section class="after-posts-widgets <?php echo ( is_active_sidebar( 'after-posts-sidebar' ) ) ? 'after-posts-widgets-active cf widgets' : 'no-widgets'; ?>">
			<?php sds_after_posts_sidebar(); ?>
		</section>

		<div class="clear"></div>
<?php
		endwhile;
	endif;
?>