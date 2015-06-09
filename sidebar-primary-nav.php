<?php if( has_nav_menu( 'primary_nav' ) ) : // Primary Navigation Area ?>
	<!-- Primary Navigation -->
	<nav class="primary-nav-container cf-768">
		<a href="#" class="primary-nav-button" title="<?php esc_attr_e( 'Toggle Navigation', 'symphony' ); ?>">
			<?php
				// Primary Navigation label
				if ( ( $nav_menu_locations = get_nav_menu_locations() ) && isset( $nav_menu_locations['primary_nav'] ) ) {
					// Get primary nav menu object
					$primary_nav_menu_object = wp_get_nav_menu_object( $nav_menu_locations['primary_nav'] );

					// Output the navigation name
					echo $primary_nav_menu_object->name;
				}
				// Fallback
				else
					_e( 'Navigation', 'symphony' );
			?>
		</a>
		<?php
			wp_nav_menu( array(
				'theme_location' => 'primary_nav',
				'container' => false,
				'menu_class' => 'primary-nav menu',
				'menu_id' => 'primary-nav',
				'fallback_cb' => 'sds_primary_menu_fallback'
			) );
		?>
	</nav>
<?php else: // Primary Navigation Sidebar ?>
	<aside class="primary-nav-sidebar primary-navigation-sidebar cf <?php echo ( is_active_sidebar( 'primary-sidebar' ) ) ? 'widgets' : 'no-widgets'; ?>">
		<?php
			// Primary Navigation Sidebar
			if ( is_active_sidebar( 'primary-nav-sidebar' ) )
				dynamic_sidebar( 'primary-nav-sidebar' );
		?>
	</aside>
<?php endif; ?>
<div class="clear"></div>