<?php if( has_nav_menu( 'top_nav' ) ) : // Top Navigation Area ?>
	<!-- Top Nav -->
	<nav class="top-nav cf">
		<div class="in">
			<?php
				wp_nav_menu( array(
					'theme_location' => 'top_nav',
					'container' => false,
					'menu_class' => 'top-nav menu',
					'menu_id' => 'top-nav',
				) );
			?>
		</div>
	</nav>
<?php else: // Top Sidebar ?>
	<aside class="top-sidebar cf <?php echo ( is_active_sidebar( 'top-sidebar' ) ) ? 'widgets' : 'no-widgets'; ?>">
		<div class="in">
			<?php
				// Top Sidebar
				if ( is_active_sidebar( 'top-sidebar' ) )
					dynamic_sidebar( 'top-sidebar' );
			?>
		</div>
	</aside>
<?php endif; ?>
<div class="clear"></div>