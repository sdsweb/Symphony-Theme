<?php global $sds_theme_options; ?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html><!--<![endif]-->
	<head>
		<?php wp_head(); ?>
	</head>

	<body <?php language_attributes(); ?> <?php body_class(); ?>>
		<?php get_sidebar( 'top' ); // Top Sidebar ?>

		<div class="in">
			<!-- Header	-->
			<header id="header" class="cf">
				<?php if ( ! symphony_is_fixed_width() ) : ?>
					<div class="in">
				<?php endif; ?>

					<!-- Logo -->
					<section class="logo-box <?php echo ( has_nav_menu( 'primary_nav' ) && $sds_theme_options['hide_tagline'] ) ? 'top-gutter' : false; ?>">
						<?php sds_logo(); ?>
						<?php sds_tagline(); ?>
					</section>

					<?php get_sidebar( 'primary-nav' ); // Primary Nav Sidebar ?>

				<?php if ( ! symphony_is_fixed_width() ) : ?>
					</div>
				<?php endif; ?>
			</header>
			<div class="clear"></div>

			<!-- Secondary Header Sidebar -->
			<?php get_sidebar( 'secondary-header' ); ?>