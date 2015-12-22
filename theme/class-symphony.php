<?php
/**
 * This class manages all functionality with our Symphony theme.
 */
class Symphony {
	/**
	 * @var string
	 */
	public $version = '1.0.7';

	/**
	 * @var string, Slug for Slocum Theme support
	 */
	public $theme_support_slug = 'slocum-theme';

	/**
	 * @var array, Array of Slocum Theme support
	 */
	public $theme_support = false;

	/**
	 * @var int, Keep track of footer widgets rendered
	 */
	public $footer_widgets_output_count = 0;

	private static $instance; // Keep track of the instance

	/**
	 * @var EDD_SL_Theme_Updater, Instance of the EDD Software Licensing Theme Updater class
	 */
	protected $_updater;

	/**
	 * Function used to create instance of class.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) )
			self::$instance = new self();

		return self::$instance;
	}


	/*
	 * This function sets up all of the actions and filters on instance
	 */
	function __construct() {
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 20 ); // Register image sizes
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ), 1 ); // Early
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 20 ); // Unregister sidebars
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) ); // Add Meta Boxes
		add_filter( 'dynamic_sidebar_params', array( $this, 'dynamic_sidebar_params' ) ); // Add clearing elements to footer widgets
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) ); // Used to enqueue editor styles based on post type
		add_filter( 'template_include', array( $this, 'template_include' ) ); // Filter the template on the "Portfolio" post type
		add_action( 'wp_head', array( $this, 'wp_head' ), 1 ); // Add <meta> tags to <head> section
		add_action( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 10, 2 ); // Output TinyMCE Setup function
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) ); // Enqueue all stylesheets (Main Stylesheet, Fonts, etc...)
		add_filter( 'the_content_more_link', '__return_false' ); // Remove default more link
		add_action( 'wp_footer', array( $this, 'wp_footer' ) ); // Responsive navigation functionality

		// Theme Options
		add_filter( 'sds_theme_options_defaults', array( $this, 'sds_theme_options_defaults' ) ); //Adjust Symphony Theme Options defaults
		add_filter( 'sds_featured_image_size', array( $this, 'sds_featured_image_size' ) ); // Adjust featured image size

		// Gravity Forms
		add_filter( 'gform_field_input', array( $this, 'gform_field_input' ), 10, 5 ); // Add placeholder to newsletter form
		add_filter( 'gform_confirmation', array( $this, 'gform_confirmation' ), 10, 4 ); // Change confirmation message on newsletter form

		// WooCommerce
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 ); // Remove default WooCommerce content wrapper
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 ); // Remove default WooCommerce content wrapper
		add_action( 'woocommerce_before_main_content', array( $this, 'woocommerce_before_main_content' ) ); // Add Symphony WooCommerce content wrapper
		add_action( 'woocommerce_after_main_content', array( $this, 'woocommerce_after_main_content' ) ); // Add Symphony WooCommerce content wrapper
		add_action( 'woocommerce_sidebar', array( $this, 'woocommerce_sidebar' ), 999 ); // Add Symphony WooCommerce closing content wrapper
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'woocommerce_breadcrumb_defaults' ) ); // Adjust WooComemrce default breadcrumb settings
		add_filter( 'woocommerce_product_settings', array( $this, 'woocommerce_product_settings' ) ); // Adjust default WooCommerce product settings
		add_filter( 'loop_shop_per_page', array( $this, 'loop_shop_per_page' ), 20 ); // Adjust number of items displayed on a catalog page
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 ); // Remove default WooCommerce related products
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'woocommerce_after_single_product_summary' ), 20 ); // Add WooCommerce related products (3x3)
	}


	/************************************************************************************
	 *    Functions to correspond with actions above (attempting to keep same order)    *
	 ************************************************************************************/

	/**
	 * This function adds images sizes to WordPress.
	 */
	function after_setup_theme() {
		global $content_width;

		/**
		 * Set the Content Width for embedded items.
		 */
		if ( ! isset( $content_width ) )
			$content_width = 1060;

		// WooCommerce Support
		add_theme_support( 'woocommerce' );

		// Change default core markup for search form, comment form, and comments, etc... to HTML5
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list'
		) );

		// Adds support for the Jetpack Portfolio custom post type
		add_theme_support( 'jetpack-portfolio' );

		// Custom Background (color/image)
		add_theme_support( 'custom-background', array(
			'default-color' => '#f6f6f6'
		) );

		// Theme textdomain
		load_theme_textdomain( 'symphony', get_template_directory() . '/languages' );

		add_image_size( 'symphony-600x400', 600, 400, true ); // Portfolio Archive Page Featured Image Size
		add_image_size( 'symphony-1600x9999', 1600, 9999, false ); // Single Post/Page Featured Image Size

		// Register menus which was used in Symphony
		register_nav_menus( array(
			'secondary_nav' => __( 'Secondary Navigation', 'symphony' ),
		) );

		// Unregister unused menus which are registered in SDS Core
		unregister_nav_menu( 'footer_nav' );

		// Slocum Theme Extender Support
		add_theme_support( $this->theme_support_slug, apply_filters( 'symphony_slocum_theme_support', array(
			// Fonts (adjustable font elements and properties)
			'fonts' => array(
				// Site Title
				'site_title' => array(
					// Font Size
					'font_size' => array(
						'default' => 36, // Default font size in px
						'min' => 18, // Minimum font size in px
						'max' => 96, // Maximum font size in px
						// CSS Properties
						'css' => array(
							// Other CSS properties that should be adjusted with the font size and their unit vale
							'properties' => array(
								// Property => unit (px, em, etc...)
								'line-height' => 'px'
							)
						)
					),
					// Font Family
					'font_family' => array( 'default' => 'Open Sans' )
				),
				// Tagline
				'tagline' => array(
					// Font Size
					'font_size' => array(
						'default' => 18,
						'css' => array(
							'properties' => array( 'line-height' => 'px' )
						)
					),
					// Font Family
					'font_family' => array( 'default' => 'Open Sans' ),
				),
				// Navigation
				'navigation' => array(
					// Primary Navigation
					'primary_nav' => array(
						// Font Size
						'font_size' => array(
							'default' => 16,
							'max' => 36
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' ),
						// CSS Properties
						'css' => array(
							// Ignore using the default CSS selector for this element
							'ignore_default_selector' => true,
							// CSS Selectors (array of selectors to match this element)
							'selector' => array( 'nav.primary-nav-container .primary-nav li a, .primary-nav-button' )
						)
					),
					// Top Navigation
					'top_nav' => array(
						// Font Size
						'font_size' => array(
							'default' => 16,
							'max' => 36
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' ),
						// CSS Properties
						'css' => array(
							'ignore_default_selector' => true,
							'selector' => array( 'nav.top-nav li a', '.top-sidebar .widget.widget_nav_menu ul li a' )
						)
					),
					// Secondary Navigation
					'secondary_nav' => array(
						// Font Size
						'font_size' => array(
							'default' => 16,
							'max' => 36
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' ),
						// CSS Properties
						'css' => array(
							'ignore_default_selector' => true,
							'selector' => array( 'nav.portfolio-nav-container ul li a', 'aside.secondary-header-sidebar .widget.widget_nav_menu ul li a' )
						)
					)
				),
				// Headings
				'headings' => array(
					// Heading 1
					'h1' => array(
						// Font Size
						'font_size' => array(
							'default' => 60,
							'min' => 24,
							'max' => 96,
							// CSS Properties
							'css' => array(
								'properties' => array( 'line-height' => 'px' )
							)
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' ),
						// CSS Properties
						'css' => array(
							'selector' => array( 'h1.post-title' )
						)
					),
					// Heading 2
					'h2' => array(
						// Font Size
						'font_size' => array(
							'default' => 48,
							'min' => 22,
							'max' => 72,
							// CSS Properties
							'css' => array(
								'properties' => array( 'line-height' => 'px' )
							)
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' ),
						// CSS Properties
						'css' => array(
							'selector' => array( 'h2.post-title', 'h2.latest-post-title' )
						)
					),
					// Heading 3
					'h3' => array(
						// Font Size
						'font_size' => array(
							'default' => 38,
							'min' => 18,
							'max' => 64,
							// CSS Properties
							'css' => array(
								'properties' => array( 'line-height' => 'px' )
							)
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' )
					),
					// Heading 4
					'h4' => array(
						// Font Size
						'font_size' => array(
							'default' => 30,
							'min' => 16,
							'max' => 48,
							// CSS Properties
							'css' => array(
								'properties' => array( 'line-height' => 'px' )
							)
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' )
					),
					// Heading 5
					'h5' => array(
						// Font Size
						'font_size' => array(
							'default' => 24,
							'min' => 12,
							'max' => 36,
							// CSS Properties
							'css' => array(
								'properties' => array( 'line-height' => 'px' )
							)
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' )
					),
					// Heading 6
					'h6' => array(
						// Font Size
						'font_size' => array(
							'default' => 20,
							'min' => 10,
							'max' => 32,
							// CSS Properties
							'css' => array(
								'properties' => array( 'line-height' => 'px' )
							)
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' )
					),
				),
				// Body (content)
				'body' => array(
					// Font Size
					'font_size' => array(
						'default' => 18
					),
					// Line Height
					'line_height' => array(
						'default' => 27,
						// CSS Properties
						'css' => array(
							// Ignore using the default CSS selector for this element
							'ignore_default_selector' => true,
							// CSS Selectors (array of selectors to match this element)
							'selector' => array( '.content p, .content ul, .content ol' )
						)
					),
					// Font Family
					'font_family' => array( 'default' => 'Open Sans' )
				),
				// Widget
				'widget' => array(
					// Font Size
					'font_size' => array(
						'default' => 18
					),
					// Font Family
					'font_family' => array( 'default' => 'Open Sans' ),
					// Widget Title
					'title' => array(
						// Font Size
						'font_size' => array(
							'default' => 18
						),
						// Font Family
						'font_family' => array( 'default' => 'Open Sans' )
					)
				)
			)
		) ) );

		// Store theme support in class
		$this->theme_support = get_theme_support( $this->theme_support_slug );
		$this->theme_support = $this->theme_support[0]; // Remove the 0 index
	}

	/**
	 * This function adjusts widgets and adds an admin notice upon activation
	 */
	function after_switch_theme() {
		$old_sidebars_widgets = get_theme_mod( 'sidebars_widgets' ); // Grab old sidebar widgets

		// If this is the first activation, run our own logic
		if ( ! is_array( $old_sidebars_widgets ) ) {
			remove_action( 'after_switch_theme', '_wp_sidebars_changed' );

			// Make sure widgets don't get added to the Top, Primary Nav, or Secondary Header Sidebars
			$this->wp_sidebars_changed();
		}

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * This function outputs admin notices.
	 */
	function admin_notices() {
		printf( __( '<div class="updated"><p>Welcome to Symphony! Get started by visiting the <a href="%1$s">Customizer</a>!</p></div>', 'symphony' ), esc_url( wp_customize_url() ) );
	}

	/**
	 * This function unregisters extra sidebars that are not used in this theme.
	 */
	function widgets_init() {
		global $wp_registered_sidebars;

		// Unregister unused sidebars which are registered in SDS Core
		unregister_sidebar( 'front-page-slider-sidebar' );
		unregister_sidebar( 'front-page-sidebar' );
		unregister_sidebar( 'header-call-to-action-sidebar' );

		// Top Sidebar (insert before 'primary-sidebar')
		$top_sidebar = array(
			'name'          => __( 'Top Sidebar', 'symphony' ),
			'id'            => 'top-sidebar',
			'class'         => '', // Is a $default in register_sidebar() so we include it here to prevent warnings
			'description'   => __( 'This widget area is the top widget area. It is displayed if a menu (Appearance &gt; Menus) is not chosen for the Top Navigation menu.', 'symphony' ),
			'before_widget' => '<section id="top-sidebar-%1$s" class="widget top-sidebar top-sidebar-widget %2$s">',
			'after_widget'  => '<div class="clear"></div></section>',
			'before_title'  => '<h3 class="widgettitle widget-title top-sidebar-widget-title">',
			'after_title'   => '</h3>'
		);

		$wp_registered_sidebars = $this->array_insert( 'sidebar', $top_sidebar, 'before', 'primary-sidebar' );

		do_action( 'register_sidebar', $top_sidebar );

		// Primary Nav Sidebar (insert after 'top-sidebar')
		$primary_nav_sidebar = array(
			'name'          => __( 'Primary Navigation Sidebar', 'symphony' ),
			'id'            => 'primary-nav-sidebar',
			'class'         => '', // Is a $default in register_sidebar() so we include it here to prevent warnings
			'description'   => __( 'This widget area is the primary navigation widget area. It is displayed if a menu (Appearance &gt; Menus) is not chosen for the Primary Navigation menu.', 'symphony' ),
			'before_widget' => '<section id="primary-nav-sidebar-%1$s" class="widget primary-nav-sidebar primary-nav-sidebar-widget %2$s">',
			'after_widget'  => '<div class="clear"></div></section>',
			'before_title'  => '<h3 class="widgettitle widget-title primary-nav-sidebar-widget-title">',
			'after_title'   => '</h3>'
		);

		$wp_registered_sidebars = $this->array_insert( 'sidebar', $primary_nav_sidebar, 'after', 'top-sidebar' );

		do_action( 'register_sidebar', $top_sidebar );

		// Secondary Header Sidebar (insert after 'primary-nav-sidebar')
		$secondary_header_sidebar = array(
			'name'          => __( 'Secondary Header Sidebar', 'symphony' ),
			'id'            => 'secondary-header-sidebar',
			'class'         => '', // Is a $default in register_sidebar() so we include it here to prevent warnings
			'description'   => __( '*This widget area is only shown if a "Secondary Navigation" menu is not set in Appearance > Menus.* This widget area is the secondary widget area. Specifically formatted for "Custom Menu" widgets (for use as a secondary navigation area across all pages).', 'symphony' ),
			'before_widget' => '<section id="secondary-header-sidebar-%1$s" class="widget secondary-header-sidebar secondary-header-sidebar-widget %2$s">',
			'after_widget'  => '<div class="clear"></div></section>',
			'before_title'  => '<h3 class="widgettitle widget-title secondary-header-sidebar-widget-title">',
			'after_title'   => '</h3>'
		);

		$wp_registered_sidebars = $this->array_insert( 'sidebar', $secondary_header_sidebar, 'after', 'primary-nav-sidebar' );

		do_action( 'register_sidebar', $secondary_header_sidebar );
	}

	/**
	 * This function runs when meta boxes are added.
	 */
	function add_meta_boxes() {
		// Post types
		$post_types = get_post_types(
			array(
				'public' => true,
				'_builtin' => false
			)
		);
		$post_types[] = 'post';
		$post_types[] = 'page';

		// Add the metabox for each type
		foreach ( $post_types as $type ) {
			add_meta_box(
				'symphony-us-metabox',
				__( 'Layout Settings', 'symphony' ),
				array( $this, 'symphony_us_metabox' ),
				$type,
				'side',
				'default'
			);
		}
	}

	/**
	 * This function renders a metabox.
	 */
	function symphony_us_metabox( $post ) {
		// Get the post type label
		$post_type = get_post_type_object( $post->post_type );
		$label = ( isset( $post_type->labels->singular_name ) ) ? $post_type->labels->singular_name : __( 'Post', 'symphony' );

		echo '<p class="howto">';
		printf(
			__( 'Looking to configure a unique layout for this %1$s? %2$s.', 'symphony' ),
			esc_html( strtolower( $label ) ),
			sprintf(
				'<a href="%1$s" target="_blank">Upgrade to Pro</a>',
				esc_url( sds_get_pro_link( 'metabox-layout-settings' ) )
			)
		);
		echo '</p>';
	}

	/**
	 * This function adds a clearing element to Footer Widgets after every 2nd & 4th widget.
	 */
	function dynamic_sidebar_params( $params ) {
		// Top Sidebar
		if ( $params[0]['id'] === 'top-sidebar' ) {
			$theme_mod_symphony_top_header_alignment = get_theme_mod( 'symphony_top_header_alignment' );
			// Top Header Alignment - Flipped
			if ( $theme_mod_symphony_top_header_alignment === 'flipped' && strpos( $params[0]['before_widget'], 'class="' ) !== false )
				$params[0]['before_widget'] = str_replace( 'class="', 'class="cf ', $params[0]['before_widget'] );
		}

		// Secondary Header Sidebar
		if ( $params[0]['id'] === 'secondary-header-sidebar' ) {
			$theme_mod_symphony_top_header_alignment = get_theme_mod( 'symphony_secondary_header_alignment' );
			// Secondary Header Alignment - Flipped
			if ( $theme_mod_symphony_top_header_alignment === 'flipped' && strpos( $params[0]['before_widget'], 'class="' ) !== false )
				$params[0]['before_widget'] = str_replace( 'class="', 'class="cf ', $params[0]['before_widget'] );
		}

		// Footer Sidebar
		if( $params[0]['id'] === 'footer-sidebar' ) {
			// Increase output count
			$this->footer_widgets_output_count++;

			// After every 2nd widget
			if ( ! ( $this->footer_widgets_output_count % 2 ) )
				$params[0]['after_widget'] .= '<div class="clear-768"></div>'; // Add clearing element

			// After every 4th widget
			if ( ! ( $this->footer_widgets_output_count % 4 ) )
				$params[0]['after_widget'] .= '<div class="clear"></div>'; // Add clearing element
		}

		return $params;
	}

	/**
	 * This function adds editor styles based on post type, before TinyMCE is initalized.
	 * It will also enqueue the correct color scheme stylesheet to better match front-end display.
	 */
	function pre_get_posts() {
		global $sds_theme_options, $post;

		$protocol = is_ssl() ? 'https' : 'http';

		// Admin only
		if ( is_admin() ) {
			add_editor_style( 'css/editor-style.css' );

			// Add correct color scheme if selected
			if ( function_exists( 'sds_color_schemes' ) && ! empty( $sds_theme_options['color_scheme'] ) && $sds_theme_options['color_scheme'] !== 'default' ) {
				$color_schemes = sds_color_schemes();
				add_editor_style( 'css/' . $color_schemes[$sds_theme_options['color_scheme']]['stylesheet'] );
			}

			// Open Sans Web Font (include only if a web font is not selected in Theme Options)
			if ( ! function_exists( 'sds_web_fonts' ) || empty( $sds_theme_options['web_font'] ) )
				add_editor_style( str_replace( ',', '%2C', $protocol . '://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600,700,800' ) ); // Google WebFonts (Open Sans)

			// Fetch page template if any on Pages only
			if ( ! empty( $post ) && $post->post_type === 'page' )
				$wp_page_template = get_post_meta( $post->ID,'_wp_page_template', true );
		}

		// Admin only and if we have a post using our full page or landing page templates
		if ( is_admin() && ! empty( $post ) && ( isset( $wp_page_template ) && ( $wp_page_template === 'template-full-width.php' || $wp_page_template === 'template-landing-page.php' ) ) )
			add_editor_style( 'css/editor-style-full-width.css' );
	}

	/**
	 * This function applies the Portfolio template to the "Portfolio" post type set in Symphony Theme Options.
	 */
	function template_include( $template ) {
		global $sds_theme_options;

		// If Jetpack is installed and Custom Content Types Module is activated
		if ( symphony_jetpack_portfolio_active() )
			// If we're on a Jetpack tag archive
			if ( is_tax( Jetpack_Portfolio::CUSTOM_TAXONOMY_TAG ) )
				$template = get_query_template( 'archive', array( 'archive-jetpack-portfolio.php' ) );

		// "Portfolio" Post Type
		if ( ! symphony_jetpack_portfolio_active() && isset( $sds_theme_options['portfolio_post_type'] ) && $sds_theme_options['portfolio_post_type'] && is_post_type_archive( $sds_theme_options['portfolio_post_type'] ) )
			$template = get_query_template( 'archive', array( 'archive-jetpack-portfolio.php' ) );

		return $template;
	}

	/**
	 * This function adds <meta> tags to the <head> element.
	 */
	function wp_head() {
	?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<?php
	}

	/**
	 * This function prints scripts after TinyMCE has been initialized for dynamic CSS in the
	 * content editor based on page template dropdown selection.
	 */
	function tiny_mce_before_init( $mceInit, $editor_id ) {
		$max_width = 1600;

		// Only on the admin 'content' editor
		if ( is_admin() && ! isset( $mceInit['setup'] ) && $editor_id === 'content' ) {
			$mceInit['setup'] = 'function( editor ) {
				// Editor init
 				editor.on( "init", function( e ) {
 					// Only on the "content" editor (other editors can inherit the setup function on init)
 					if( editor.id === "content" ) {
						var $page_template = jQuery( "#page_template" ),
							full_width_templates = ["template-full-width.php", "template-landing-page.php"],
							$content_editor_head = jQuery( editor.getDoc() ).find( "head" );

						// If the page template dropdown exists
						if ( $page_template.length ) {
							// When the page template dropdown changes
							$page_template.on( "change", function() {
								// Is this a full width template?
								if ( full_width_templates.indexOf( $page_template.val() ) !== -1 ) {
									// Add dynamic CSS
									if( $content_editor_head.find( "#' . get_template() . '-editor-css" ).length === 0 ) {
										$content_editor_head.append( "<style type=\'text/css\' id=\'' . get_template() . '-editor-css\'> body, body.wp-autoresize { max-width: ' . $max_width . 'px; } </style>" );
									}
								}
								else {
									// Add dynamic CSS
									$content_editor_head.find( "#' . get_template() . '-editor-css" ).remove();

									// If the full width style was added on TinyMCE Init, remove it
									$content_editor_head.find( "link[href=\'' . get_template_directory_uri() . '/css/editor-style-full-width.css\']" ).remove();
								}
							} );
						}
					}
				} );
			}';
		}

		return $mceInit;
	}

	/**
	 * This function enqueues all styles and scripts (Main Stylesheet, Fonts, etc...). Stylesheets can be conditionally included if needed.
	 */
	function wp_enqueue_scripts() {
		global $sds_theme_options, $is_IE;

		$protocol = is_ssl() ? 'https' : 'http'; // Determine current protocol

		// symphony (main stylesheet)
		wp_enqueue_style( 'symphony', get_template_directory_uri() . '/style.css', false, $this->version );

		// Enqueue the child theme stylesheet only if a child theme is active
		if ( is_child_theme() )
			wp_enqueue_style( 'symphony-child', get_stylesheet_uri(), array( 'symphony' ), $this->version );

		// Open Sans (include only if a web font is not selected in Theme Options)
		if ( ! function_exists( 'sds_web_fonts' ) || empty( $sds_theme_options['web_font'] ) )
			wp_enqueue_style( 'open-sans-web-font', $protocol . '://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600,700,800', false, $this->version ); // Google WebFonts (Open Sans)

		// Ensure jQuery is loaded on the front end for our footer script (@see wp_footer() below)
		wp_enqueue_script( 'jquery' );

		// Fitvids
		wp_enqueue_script( 'fitvids', get_template_directory_uri() . '/js/fitvids.js', array( 'jquery' ), $this->version );

		// HTML5 Shiv (IE only, conditionally for less than IE9)
		if ( $is_IE )
			wp_enqueue_script( 'html5-shim', get_template_directory_uri() . '/js/html5.js', false, $this->version );
	}

	/**
	 * This function outputs the necessary javascript for the responsive menus.
	 */
	function wp_footer() {
	?>
		<script type="text/javascript">
			// <![CDATA[
			jQuery( function( $ ) {
				// Primary Nav
				$( '.primary-nav-button' ).on( 'click', function ( e ) {
					var $primary_nav_and_button = $( '.primary-nav-button, .primary-nav' );

					// Prevent Propagation (bubbling) to other elements and default
					e.stopPropagation();
					e.preventDefault();

					// Open
					if ( ! $primary_nav_and_button.hasClass( 'open' ) ) {
						$primary_nav_and_button.addClass( 'open' );

						// 500ms delay to account for CSS transition
						setTimeout( function() {
							$primary_nav_and_button.addClass( 'opened' );
						}, 500 );
					}
					// Close
					else {
						$primary_nav_and_button.removeClass( 'open opened' );
					}
				} );

				$( document ).on( 'click touch', function() {
					var $primary_nav_and_button = $( '.primary-nav-button, .primary-nav' );

					// Close
					$primary_nav_and_button.removeClass( 'open opened' );
				} );

				// Fitvids
				$( 'article.content, .widget' ).fitVids();
			} );
			// ]]>
		</script>
	<?php
	}


	/*****************
	 * Theme Options *
	 *****************/

	/**
	 * This function adjusts the Symphony Theme Option defaults.
	 */
	function sds_theme_options_defaults( $defaults ) {
		// Portfolio Post Type
		if ( ! isset( $defaults['portfolio_post_type'] ) )
			$defaults['portfolio_post_type'] = false;

		// Hide Archive Titles
		if ( ! isset( $defaults['hide_archive_titles'] ) )
			$defaults['hide_archive_titles'] = false;

		// Hide Post Meta
		if ( ! isset( $defaults['hide_post_meta'] ) )
			$defaults['hide_post_meta'] = false;

		// Hide Author Meta
		if ( ! isset( $defaults['hide_author_meta'] ) )
			$defaults['hide_author_meta'] = false;

		// Featured Image Size
		if ( ! isset( $defaults['featured_image_size'] ) )
			$defaults['featured_image_size'] = apply_filters( 'sds_theme_options_default_featured_image_size', '' );

		return $defaults;
	}

	/**
	 * This function adjusts the featured image size on single posts and pages.
	 */
	function sds_featured_image_size( $size ) {
		global $sds_theme_options;

		// Single Post or Page
		if ( is_singular( 'post' ) || is_page() )
			$size = ( isset( $sds_theme_options['featured_image_size'] ) && ! empty( $sds_theme_options['featured_image_size'] ) ) ? $sds_theme_options['featured_image_size'] : $size;

		return $size;
	}


	/*****************
	 * Gravity Forms *
	 *****************/

	/**
	 * This function adds the HTML5 placeholder attribute to forms with a CSS class of the following:
	 * .mc-gravity, .mc_gravity, .mc-newsletter, .mc_newsletter classes
	 */
	function gform_field_input( $input, $field, $value, $lead_id, $form_id ) {
		$form_meta = RGFormsModel::get_form_meta( $form_id ); // Get form meta

		// Ensure we have at least one CSS class
		if ( isset( $form_meta['cssClass'] ) ) {
			$form_css_classes = explode( ' ', $form_meta['cssClass'] );

			// Ensure the current form has one of our supported classes and alter the field accordingly if we're not on admin
			if ( ! is_admin() && array_intersect( $form_css_classes, array( 'mc-gravity', 'mc_gravity', 'mc-newsletter', 'mc_newsletter' ) ) )
				$input = '<div class="ginput_container"><input name="input_' . $field['id'] . '" id="input_' . $form_id . '_' . $field['id'] . '" type="text" value="" class="large" placeholder="' . $field['label'] . '" /></div>';
		}

		return $input;
	}

	/**
	 * This function alters the confirmation message on forms with a CSS class of the following:
	 * .mc-gravity, .mc_gravity, .mc-newsletter, .mc_newsletter classes
	 */
	function gform_confirmation( $confirmation, $form, $lead, $ajax ) {
		// Ensure we have at least one CSS class
		if ( isset( $form['cssClass'] ) ) {
			$form_css_classes = explode( ' ', $form['cssClass'] );

			// Confirmation message is set and form has one of our supported classes (alter the confirmation accordingly)
			if ( $form['confirmation']['type'] === 'message' && array_intersect( $form_css_classes, array( 'mc-gravity', 'mc_gravity', 'mc-newsletter', 'mc_newsletter' ) ) )
				$confirmation = '<div class="mc-gravity-confirmation mc_gravity-confirmation mc-newsletter-confirmation mc_newsletter-confirmation">' . $confirmation . '</div>';
		}

		return $confirmation;
	}


	/***************
	 * WooCommerce *
	 ***************/

	/**
	 * This function alters the default WooCommerce content wrapper starting element.
	 */
	function woocommerce_before_main_content(){
	?>
		<section class="woocommerce woo-commerce content-wrapper page-content content-container cf">
			<?php if ( ! symphony_is_fixed_width() ) : ?>
				<div class="in">
			<?php endif; ?>

			<article class="content cf">
	<?php
	}

	/**
	 * This function alters the default WooCommerce content wrapper ending element.
	 */
	function woocommerce_after_main_content(){
	?>
			</article>
	<?php
	}

	/**
	 * This function adds to the default WooCommerce content wrapper ending element.
	 */
	function woocommerce_sidebar(){
	?>
			<?php if ( ! symphony_is_fixed_width() ) : ?>
				</div>
			<?php endif; ?>
		</section>
	<?php
	}

	/**
	 * This function modifies WooCommerce default breadcrumb settings.
	 */
	function woocommerce_breadcrumb_defaults( $defaults ) {
		// If Yoast exists and breadcrumbs enabled, grab the delimiter
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			$wpseo_internallinks = get_option( 'wpseo_internallinks' );

			// If we have Yoast options for breadcrumbs
			if ( ! empty( $wpseo_internallinks ) && isset( $wpseo_internallinks['breadcrumbs-sep'] ) ) {
				$defaults['delimiter'] = ''; // Reset delimiter

				// Add a space (' ') to the beginning if necessary
				if ( strpos( $wpseo_internallinks['breadcrumbs-sep'], ' ' ) !== 0 )
					$defaults['delimiter'] .= ' ';

				// Yoast breadcrumb separator
				$defaults['delimiter'] .= $wpseo_internallinks['breadcrumbs-sep'];

				// Add a space (' ') to the end if necessary
				if ( strrpos( $wpseo_internallinks['breadcrumbs-sep'], ' ' ) !== strlen( $wpseo_internallinks['breadcrumbs-sep'] ) )
					$defaults['delimiter'] .= ' ';
			}

		}

		return $defaults;
	}

	/**
	 * This function adjusts the default WooCommerce Product settings.
	 */
	function woocommerce_product_settings( $settings ) {
		if ( is_array( $settings ) )
			foreach( $settings as &$setting )
				// Adjust the default value of the Catalog image size
				if( $setting['id'] === 'shop_catalog_image_size' )
					$setting['default']['width'] = $setting['default']['height'] = 300;

		return $settings;
	}

	/**
	 * This function changes the number of products output on the Catalog page.
	 */
	function loop_shop_per_page( $num_items ) {
		return 12;
	}

	/**
	 * This function changes the number of related products displayed on a single product page.
	 */
	function woocommerce_after_single_product_summary() {
		woocommerce_related_products( array(
		  'posts_per_page' => 3,
		  'columns' => 3
		) );
	}


	/**
	 * Internal Functions (functions used internally throughout this class)
	 */

	/**
	 * This function inserts a value into an array before or after a specified key.
	 */
	public function array_insert( $type, $value, $action, $key, $original = array() ) {
		// Switch based on type
		switch ( $type ) {
			// Sidebar
			case 'sidebar':
				global $wp_registered_sidebars;

				// Where should we look (in global or passed original data)
				$where = ( ! empty( $original ) ) ? $original: $wp_registered_sidebars;

				// Check to see if the array key exists in the current array
				if ( array_key_exists( $key, $where ) ) {
					$new = array();

					foreach ( $where as $k => $v ) {
						// Before
						if ( $k === $key && $action == 'before' )
							$new[$value['id']] = $value;

						// Current
						$new[$k] = $v;

						// After
						if ( $k === $key && $action == 'after' )
							$new[$value['id']] = $value;
					}

					return $new;
				}

				// No key found, return the original array
				return $where;
			break;
			// Settings Section
			case 'settings-section':
				global $wp_settings_sections;

				// Where should we look (in global or passed original data)
				$where = ( ! empty( $original ) ) ? $original: $wp_settings_sections;

				// Check to see if the array key exists in the current array
				if ( array_key_exists( $key, $where ) ) {
					$new = array();
					$settings_section = $value;
					unset( $settings_section['page'] );

					foreach ( $where as $k => $v ) {
						// Before
						if ( $k === $key && $action == 'before' )
							$new[$value['id']] = $settings_section;

						// Current
						$new[$k] = $v;

						// After
						if ( $k === $key && $action == 'after' )
							$new[$value['id']] = $settings_section;
					}

					return $new;
				}

				// No key found, return the original array
				return $where;
			break;
		}

		return array();
	}

	/**
	 * This function is called upon the first ever activation of Symphony.
	 */
	function wp_sidebars_changed( $ignore_sidebars = array( 'top-sidebar', 'primary-nav-sidebar', 'secondary-header-sidebar' ) ) {
		global $wp_registered_sidebars, $sidebars_widgets, $wp_registered_widgets;

		$registered_sidebar_keys = array_keys( $wp_registered_sidebars );

		if ( empty( $sidebars_widgets ) )
			return;

		unset( $sidebars_widgets['array_version'] );

		$old = array_keys( $sidebars_widgets );
		sort( $old );
		sort( $registered_sidebar_keys );

		if ( $old == $registered_sidebar_keys )
			return;

		$orphaned = 0;
		$_sidebars_widgets = array(
			'wp_inactive_widgets' => !empty( $sidebars_widgets['wp_inactive_widgets'] ) ? $sidebars_widgets['wp_inactive_widgets'] : array()
		);

		unset( $sidebars_widgets['wp_inactive_widgets'] );

		// Remove empties from $sidebars_widgets
		$sidebars_widgets = array_filter( $sidebars_widgets );

		foreach ( $wp_registered_sidebars as $id => $settings )
			// Ignore sidebars
			if ( ! in_array( $id, $ignore_sidebars ) )
				$_sidebars_widgets[$id] = array_shift( $sidebars_widgets );

		foreach ( $sidebars_widgets as $val ) {
			if ( is_array( $val ) && ! empty( $val ) )
				$_sidebars_widgets['orphaned_widgets_' . ++$orphaned] = $val;
		}

		// discard invalid, theme-specific widgets from sidebars
		$shown_widgets = array();

		foreach ( $_sidebars_widgets as $sidebar => $widgets ) {
			if ( ! is_array( $widgets ) )
				continue;

			$_widgets = array();

			foreach ( $widgets as $widget )
				if ( isset( $wp_registered_widgets[$widget] ) )
					$_widgets[] = $widget;

			$_sidebars_widgets[$sidebar] = $_widgets;
			$shown_widgets = array_merge( $shown_widgets, $_widgets );
		}

		$sidebars_widgets = $_sidebars_widgets;
		unset( $_sidebars_widgets, $_widgets );

		// find hidden/lost multi-widget instances
		$lost_widgets = array();

		foreach ( $wp_registered_widgets as $key => $val ) {
			if ( in_array( $key, $shown_widgets, true ) )
				continue;

			$number = preg_replace( '/.+?-([0-9]+)$/', '$1', $key );

			if ( 2 > (int) $number )
				continue;

			$lost_widgets[] = $key;
		}

		$sidebars_widgets['wp_inactive_widgets'] = array_merge( $lost_widgets, ( array ) $sidebars_widgets['wp_inactive_widgets'] );

		wp_set_sidebars_widgets( $sidebars_widgets );

		return $sidebars_widgets;
	}
}


function Symphony_Instance() {
	return Symphony::instance();
}

// Starts Symphony
Symphony_Instance();