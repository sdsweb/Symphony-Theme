<?php
	if ( have_posts() ) : // Search results
?>
	<header class="search-title">
		<h1 title="<?php esc_attr_e( sprintf( __( 'Search results for \'%s\'', 'symphony' ), get_search_query() ) ); ?>" class="page-title"><?php printf( __( 'Search results for "%s"', 'symphony' ), get_search_query() ); ?></h1>
	</header>

	<?php while ( have_posts() ) : the_post(); ?>
		<section id="<?php the_ID(); ?>" <?php post_class( 'latest-post cf' ); ?>>
			<?php if ( has_post_thumbnail() ): ?>
				<div class="latest-post-thumb">
					<?php
						$featured_image_alignment = get_theme_mod( 'symphony_featured_image_alignment' );

						if ( $featured_image_alignment === 'center' )
							sds_featured_image( true, 'symphony-1600x9999' );
						else
							sds_featured_image( true, 'medium' );
					?>
				</div>
			<?php endif; ?>

			<article class="latest-post-info">
				<h2 class="latest-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

				<?php if ( $post->post_type === 'post' ): ?>
					<p class="latest-post-date">
						<?php printf( __( 'Posted on %1$s by %2$s - %3$s', 'symphony' ) , get_the_time( get_option( 'date_format' ) ), '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a>', '<a href="' . get_comments_link() . '">' . get_comments_number_text() . '</a>' ); ?>
					</p>
				<?php endif; ?>

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
			</article>
		</section>

		<div class="clear"></div>
	<?php endwhile; ?>
<?php else : // No search results ?>
		<header class="search-title">
			<h1 title="<?php esc_attr_e( sprintf( __( 'No results for \'%s\'', 'symphony' ), get_search_query() ) ); ?>'" class="page-title"><?php printf( __( 'No results for "%s"', 'symphony' ), get_search_query() ); ?></h1>
		</header>

		<section class="no-results no-posts no-search-results latest-post">
			<?php sds_no_posts(); ?>

			<section id="search-again" class="search-again search-block no-posts no-search-results">
				<p><?php _e( 'Would you like to search again?', 'symphony' ); ?></p>
				<?php echo get_search_form(); ?>
			</section>
		</section>
<?php endif; ?>