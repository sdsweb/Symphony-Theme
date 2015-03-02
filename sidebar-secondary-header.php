<?php
	// Jetpack Portfolio Archive/Types/Tags
	if ( symphony_jetpack_portfolio_active() && ( is_post_type_archive( Jetpack_Portfolio::CUSTOM_POST_TYPE ) || is_tax( Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE ) || is_tax( Jetpack_Portfolio::CUSTOM_TAXONOMY_TAG ) ) && ( $terms = get_terms( Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE ) ) ) :
?>
	<nav class="portfolio-nav-container jetpack-nav-container">
		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<ul class="jetpack-portfolio-terms">
			<?php
			// Output Jetpack Portfolio taxonomy terms
			wp_list_categories( array(
				'title_li' => '',
				'show_option_none' => '',
				'taxonomy' => Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE,
			) );
			?>
		</ul>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
			</div>
		<?php endif; ?>
	</nav>
<?php elseif ( has_nav_menu( 'secondary_nav' ) ) : // Secondary Navigation Menu ?>
	<nav class="portfolio-nav-container">
		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<?php
			wp_nav_menu( array(
				'theme_location' => 'secondary_nav',
				'container' => false,
				'menu_class' => 'secondary-nav menu',
				'menu_id' => 'secondary-nav',
			) );
		?>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
			</div>
		<?php endif; ?>
	</nav>
<?php else: // Secondary Sidebar ?>
	<aside class="secondary-header-sidebar <?php echo ( is_active_sidebar( 'secondary-header-sidebar' ) ) ? 'widgets' : 'no-widgets'; ?>">
		<?php if ( ! symphony_is_fixed_width() ) : ?>
			<div class="in">
		<?php endif; ?>

		<?php
			// Secondary Header Sidebar
			if ( is_active_sidebar( 'secondary-header-sidebar' ) )
				dynamic_sidebar( 'secondary-header-sidebar' );
		?>

		<?php if ( ! symphony_is_fixed_width() ) : ?>
			</div>
		<?php endif; ?>
	</aside>
<?php endif; ?>
<div class="clear"></div>