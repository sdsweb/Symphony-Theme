<?php
/**
 * This class manages all Customizer functionality with our Symphony theme.
 */
class Symphony_Customizer {
	/**
	 * @var string
	 */
	public $version = '1.0.1';

	/**
	 * @var string, Transient name
	 */
	public $transient_name = 'symphony_customizer_';

	/**
	 * @var array, Transient data
	 */
	public $transient_data = array();


	private static $instance; // Keep track of the instance

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
		// Includes
		$this->includes();

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 9999 ); // After Setup Theme (late; load assets based on theme support)

		// Customizer
		add_action( 'customize_register', array( $this, 'customize_register' ), 25 ); // Add settings/sections/controls to Customizer
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ), 20 );
		add_action( 'customize_save_after', array( $this, 'reset_transient' ) ); // Customize Save (reset transients)

		// Color Scheme
		add_filter( 'theme_mod_primary_color', array( $this, 'theme_mod_primary_color' ) ); // Set the default primary color
		add_filter( 'theme_mod_symphony_post_title_color', array( $this, 'theme_mod_symphony_post_title_color' ) ); // Set the default post title color
		add_filter( 'theme_mod_content_color', array( $this, 'theme_mod_content_color' ) ); // Set the default content color
		add_filter( 'theme_mod_link_color', array( $this, 'theme_mod_link_color' ) ); // Set the default link color
		add_filter( 'theme_mod_symphony_archive_title_color', array( $this, 'theme_mod_symphony_archive_title_color' ) ); // Set the default archive title color
		add_filter( 'theme_mod_symphony_button_text_color', array( $this, 'theme_mod_symphony_button_text_color' ) ); // Set the default more link text color
		add_filter( 'theme_mod_symphony_button_background_color', array( $this, 'theme_mod_symphony_button_background_color' ) ); // Set the default more link background color

		// Widget Design
		add_filter( 'theme_mod_symphony_widget_title_color', array( $this, 'theme_mod_symphony_widget_title_color' ) ); // Set the default widget title color
		add_filter( 'theme_mod_symphony_widget_color', array( $this, 'theme_mod_symphony_widget_color' ) ); // Set the default widget title color
		add_filter( 'theme_mod_symphony_widget_link_color', array( $this, 'theme_mod_symphony_widget_link_color' ) ); // Set the default widget title color

		// Fixed Width Background Color
		add_filter( 'theme_mod_symphony_fixed_width_background_color', array( $this, 'theme_mod_symphony_fixed_width_background_color' ) ); // Set the default fixed width background color

		// Fluid Width Background Color
		add_filter( 'theme_mod_symphony_fluid_width_background_color', array( $this, 'theme_mod_symphony_fluid_width_background_color' ) ); // Set the default fluid width background color

		// Top Header
		add_filter( 'theme_mod_symphony_top_header_color', array( $this, 'theme_mod_symphony_top_header_color' ) ); // Set the default top color
		add_filter( 'theme_mod_symphony_top_header_text_color', array( $this, 'theme_mod_symphony_top_header_text_color' ) ); // Set the default top header text color
		add_filter( 'theme_mod_symphony_top_header_sub_menu_color', array( $this, 'theme_mod_symphony_top_header_sub_menu_color' ) ); // Set the default top header navigation sub menu color
		add_filter( 'theme_mod_symphony_top_header_sub_menu_background_color', array( $this, 'theme_mod_symphony_top_header_sub_menu_background_color' ) ); // Set the default top header navigation sub menu background color
		add_filter( 'theme_mod_symphony_top_header_background_color', array( $this, 'theme_mod_symphony_top_header_background_color' ) ); // Set the default top header background color

		// Header
		//add_filter( 'theme_mod_header_textcolor', array( $this, 'theme_mod_header_textcolor' ) ); // Set the default header text color'
		add_filter( 'theme_mod_symphony_site_title_color', array( $this, 'theme_mod_symphony_site_title_color' ) ); // Set the default site title color
		add_filter( 'theme_mod_symphony_tagline_color', array( $this, 'theme_mod_symphony_tagline_color' ) ); // Set the default tagline color
		add_filter( 'theme_mod_symphony_primary_sub_menu_color', array( $this, 'theme_mod_symphony_primary_sub_menu_color' ) ); // Set the default primary navigation sub menu color
		add_filter( 'theme_mod_symphony_primary_sub_menu_background_color', array( $this, 'theme_mod_symphony_primary_sub_menu_background_color' ) ); // Set the default primary navigation sub menu background color
		add_filter( 'theme_mod_symphony_header_background_color', array( $this, 'theme_mod_symphony_header_background_color' ) ); // Set the default header background color

		// Secondary Header
		add_filter( 'theme_mod_secondary_color', array( $this, 'theme_mod_secondary_color' ) ); // Set the default secondary color
		add_filter( 'theme_mod_symphony_secondary_header_text_color', array( $this, 'theme_mod_symphony_secondary_header_text_color' ) ); // Set the default secondary header text color
		add_filter( 'theme_mod_symphony_secondary_header_sub_menu_color', array( $this, 'theme_mod_symphony_secondary_header_sub_menu_color' ) ); // Set the default secondary header navigation sub menu color
		add_filter( 'theme_mod_symphony_secondary_header_sub_menu_background_color', array( $this, 'theme_mod_symphony_secondary_header_sub_menu_background_color' ) ); // Set the default secondary header navigation sub menu background color
		add_filter( 'theme_mod_symphony_secondary_header_background_color', array( $this, 'theme_mod_symphony_secondary_header_background_color' ) ); // Set the default secondary header background color

		// Footer
		add_filter( 'theme_mod_symphony_footer_text_color', array( $this, 'theme_mod_symphony_footer_text_color' ) ); // Set the default footer text color
		add_filter( 'theme_mod_symphony_footer_link_color', array( $this, 'theme_mod_symphony_footer_link_color' ) ); // Set the default footer link color
		add_filter( 'theme_mod_symphony_footer_background_color', array( $this, 'theme_mod_symphony_footer_background_color' ) ); // Set the default footer background color

		// More Link
		add_filter( 'theme_mod_symphony_more_link_label', array( $this, 'theme_mod_symphony_more_link_label' ) ); // Set the default more link button label

		// Front End
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// All
		include_once( 'class-symphony-customizer-theme-helper.php' ); // Customizer Theme Helper Class
		include_once( 'class-symphony-customizer-typography.php' ); // Customizer Typography Class

		// Admin Only
		if ( is_admin() ) { }

		// Front-End Only
		if ( ! is_admin() ) { }
	}


	/************************************************************************************
	 *    Functions to correspond with actions above (attempting to keep same order)    *
	 ************************************************************************************/

	/**
	 * This function runs after the theme has been setup and determines which assets to load based on theme support.
	 */
	function after_setup_theme() {
		// Load required assets
		$this->includes();

		$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance

		// Setup transient data
		$this->transient_name .= $symphony_theme_helper->theme->get_template(); // Append theme name to transient name
		$this->transient_data = $this->get_transient();

		// If the theme has updated, let's update the transient data
		if ( ! isset( $this->transient_data['version'] ) || $this->transient_data['version'] !== $symphony_theme_helper->theme->get( 'Version' ) )
			$this->reset_transient();
	}

	/**************
	 * Customizer *
	 **************/

	/**
	 * This function registers various Customizer options for this theme.
	 */
	function customize_register( $wp_customize ) {
		// Load custom Customizer API assets
		include_once get_template_directory() . '/customizer/class-symphony-customizer-checkbox-control.php'; // Checkbox Controller
		include_once get_template_directory() . '/customizer/class-symphony-customizer-font-size-control.php'; // Symphony Customizer Font Size Control
		include_once get_template_directory() . '/customizer/class-symphony-customizer-jetpack-portfolio-control.php'; // Symphony Customizer Jetpack Portfolio Control

		$sds_theme_options_instance = SDS_Theme_Options_Instance();
		$sds_theme_options_defaults = $sds_theme_options_instance->get_sds_theme_option_defaults();

		/**
		 * General Settings
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/*
			 * General Settings Panel
			 */
			$wp_customize->add_panel( 'symphony_general_design', array(
				'priority' => 10, // Top
				'title' => __( 'General Settings', 'symphony' )
			) );

			/**
			 * Logo/Site Title & Tagline Section
			 */
			$title_tagline_section = $wp_customize->get_section( 'title_tagline' ); // Get Section
			$title_tagline_section->panel = 'symphony_general_design'; // Add panel
			$title_tagline_section->priority = 10; // Adjust Priority


			/**
			 * Static Front Page Section
			 */
			$static_front_page_section = $wp_customize->get_section( 'static_front_page' ); // Get Section
			$static_front_page_section->panel = 'symphony_general_design'; // Add panel
			$static_front_page_section->priority = 20; // Adjust Priority


			/**
			 * Nav Section
			 */
			$static_front_page_section = $wp_customize->get_section( 'nav' ); // Get Section
			$static_front_page_section->panel = 'symphony_general_design'; // Add panel
			$static_front_page_section->priority = 30; // Adjust Priority


			/**
			 * Site Layout Section
			 */
			$wp_customize->add_section( 'symphony_design_site_layout', array(
				'priority' => 40, // Top
				'title' => __( 'Site Layout', 'symphony' ),
				'panel' => 'symphony_general_design'
			) );

			/**
			 * Fixed Width (checkbox design is set for turning things off when checked, so we're using symphony_fluid_width for the theme mod name)
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fluid_width',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fluid_width', true ),
					'sanitize_callback' => 'symphony_boolval'
				)
			);

			// Control
			$wp_customize->add_control(
				new Symphony_Customizer_Checkbox_Control(
					$wp_customize,
					'symphony_fluid_width',
					array(
						'label' => __( 'Fixed Width', 'symphony' ),
						'description' => __( 'When set to "Yes", the left and right side of the site will be bordered by a background. The color or image of the background can be set separately from the main body of the site. <br /><br /> When set to "No", the content of the body will extend to the side of the page. This is recommended for heavily mobile sites. <br /><br /> To customize these backgrounds, go to "Background Colors & Images".', 'symphony' ),
						'section' => 'symphony_design_site_layout',
						'settings' => 'symphony_fluid_width',
						'type' => 'checkbox', // Used in js controller
						'css_class' => 'theme-mod-symphony-fluid-width',
						'css_id' => 'theme_mod_symphony_fluid_width',
						'checked_label' => __('No', 'symphony' ),
						'unchecked_label' => __( 'Yes', 'symphony' ),
						'style' => array(
							'before' => 'width: 38%; text-align: center;',
							'after' => 'right: 0; width: 38%; padding: 0 6px; text-align: center;'
						)
					)
				)
			);

			/**
			 * Max Width
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_max_width',
				array(
					'default' => apply_filters( 'symphony_max_width', 1600, 1600 ), // Pass the default value as second parameter
					'sanitize_callback' => 'absint',
					'sanitize_js_callback' => 'absint'
				)
			);

			// Control
			$wp_customize->add_control(
				new Symphony_Customizer_Font_Size_Control(
					$wp_customize,
					'symphony_max_width',
					array(
						'label' => __( 'Maximum Width', 'symphony' ),
						//'description' => __( 'Define a maximum width in pixels for your site.', 'symphony' ),
						'section' => 'symphony_design_site_layout',
						'settings' => 'symphony_max_width',
						'priority' => 20, // After Fixed Width
						'type' => 'number',
						'input_attrs' => array(
							'min' => apply_filters( 'theme_mod_symphony_max_width_min', 800, 800 ), // Pass the default value as second parameter
							'max' => apply_filters( 'theme_mod_symphony_max_width_max', 1600, 1600 ), // Pass the default value as second parameter
							'placeholder' => apply_filters( 'theme_mod_symphony_max_width', 1600, 1600 ), // Pass the default value as second parameter
							'style' => 'width: 70px',
							'step' => '10'
						),
						'units' => array(
							'title' => _x( 'pixels', 'title attribute for this Customizer control', 'symphony' )
						)
					)
				)
			);


			/**
			 * Featured Images Section
			 */
			$wp_customize->add_section( 'symphony_design_featured_images', array(
				'priority' => 50, // After Footer
				'title' => __( 'Featured Images', 'symphony' ),
				'panel' => 'symphony_general_design'
			) );

			/**
			 * Featured Image Size
			 */
			// Setting
			$wp_customize->add_setting(
				'sds_theme_options[featured_image_size]', // IDs can have nested array keys
				array(
					'default' => $sds_theme_options_defaults['featured_image_size'],
					'type' => 'option',
					// Data is also sanitized upon update_option() call using the sanitize function in $sds_theme_options_instance
					'sanitize_callback' => 'sanitize_text_field'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'sds_theme_options[featured_image_size]',
					array(
						'label' => __( 'Featured Image Size', 'symphony' ),
						'description' => __( 'Adjust the size of featured images on single posts and pages.', 'symphony' ),
						'section' => 'symphony_design_featured_images',
						'settings' => 'sds_theme_options[featured_image_size]',
						'type' => 'select',
						'choices' => $this->get_featured_image_size_choices()
					)
				)
			);

			/**
			 * Featured Image Alignment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_featured_image_alignment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_featured_image_alignment', 'left' ),
					'sanitize_callback' => 'sanitize_text_field'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_featured_image_alignment',
					array(
						'label' => __( 'Blog/Archive Featured Image Alignment', 'symphony' ),
						'section' => 'symphony_design_featured_images',
						'settings' => 'symphony_featured_image_alignment',
						'priority' => 20,
						'type' => 'radio',
						'choices' => array(
							'left' => __( 'Left', 'symphony' ),
							'center' => __( 'Center', 'symphony' ),
							'right' => __( 'Right', 'symphony' )
						)
					)
				)
			);


			/**
			 * Show/Hide Elements Section
			 */
			$wp_customize->add_section( 'symphony_design_show_hide', array(
				'priority' => 60, // After Background Colors & Image
				'title' => __( 'Show or Hide Elements', 'symphony' ),
				'panel' => 'symphony_general_design'
			) );

			/**
			 * Show/Hide Tagline
			 */
			// Setting
			$wp_customize->add_setting(
				'sds_theme_options[hide_tagline]', // IDs can have nested array keys
				array(
					'default' => $sds_theme_options_defaults['hide_tagline'],
					'type' => 'option',
					// Data is also sanitized upon update_option() call using the sanitize function in $sds_theme_options_instance
					'sanitize_callback' => 'symphony_boolval'
				)
			);

			// Control
			$wp_customize->add_control(
				new Symphony_Customizer_Checkbox_Control(
					$wp_customize,
					'sds_theme_options[hide_tagline]', // IDs can have nested array keys
					array(
						'label' => __( 'Tagline', 'symphony' ),
						//'description' => __( 'When "show" is displayed, the tagline will be displayed on your site and vise-versa.', 'symphony' ),
						'section'  => 'symphony_design_show_hide',
						'settings' => 'sds_theme_options[hide_tagline]',
						'priority' => 10,
						'type' => 'checkbox', // Used in js controller
						'css_class' => 'sds-theme-options-show-hide-tagline',
						'css_id' => 'sds_theme_options_hide_tagline'
					)
				)
			);

			/**
			 * Show/Hide Archive Titles
			 */
			// Setting
			$wp_customize->add_setting(
				'sds_theme_options[hide_archive_titles]', // IDs can have nested array keys
				array(
					'default' => $sds_theme_options_defaults['hide_archive_titles'],
					'type' => 'option',
					// Data is also sanitized upon update_option() call using the sanitize function in $sds_theme_options_instance
					'sanitize_callback' => 'symphony_boolval'
				)
			);

			// Control
			$wp_customize->add_control(
				new Symphony_Customizer_Checkbox_Control(
					$wp_customize,
					'sds_theme_options[hide_archive_titles]', // IDs can have nested array keys
					array(
						'label' => __( 'Archive Titles', 'symphony' ),
						//'description' => __( 'When "show" is displayed, the archive titles will be displayed on your site and vise-versa.', 'symphony' ),
						'section'  => 'symphony_design_show_hide',
						'settings' => 'sds_theme_options[hide_archive_titles]',
						'priority' => 20,
						'type' => 'checkbox', // Used in js controller
						'css_class' => 'sds-theme-options-show-hide-archive-titles',
						'css_id' => 'sds_theme_options_hide_archive_titles'
					)
				)
			);

			/**
			 * Show/Hide Post Meta
			 */
			// Setting
			$wp_customize->add_setting(
				'sds_theme_options[hide_post_meta]', // IDs can have nested array keys
				array(
					'default' => $sds_theme_options_defaults['hide_post_meta'],
					'type' => 'option',
					// Data is also sanitized upon update_option() call using the sanitize function in $sds_theme_options_instance
					'sanitize_callback' => 'symphony_boolval'
				)
			);

			// Control
			$wp_customize->add_control(
				new Symphony_Customizer_Checkbox_Control(
					$wp_customize,
					'sds_theme_options[hide_post_meta]', // IDs can have nested array keys
					array(
						'label' => __( 'Post Meta', 'symphony' ),
						//'description' => __( 'When "show" is displayed, the post meta will be displayed on your site and vise-versa.', 'symphony' ),
						'section'  => 'symphony_design_show_hide',
						'settings' => 'sds_theme_options[hide_post_meta]',
						'priority' => 30,
						'type' => 'checkbox', // Used in js controller
						'css_class' => 'sds-theme-options-show-hide-post-meta',
						'css_id' => 'sds_theme_options_hide_post_meta'
					)
				)
			);

			/**
			 * Show/Hide Author Details
			 */
			// Setting
			$wp_customize->add_setting(
				'sds_theme_options[hide_author_meta]', // IDs can have nested array keys
				array(
					'default' => $sds_theme_options_defaults['hide_author_meta'],
					'type' => 'option',
					// Data is also sanitized upon update_option() call using the sanitize function in $sds_theme_options_instance
					'sanitize_callback' => 'symphony_boolval'
				)
			);

			// Control
			$wp_customize->add_control(
				new Symphony_Customizer_Checkbox_Control(
					$wp_customize,
					'sds_theme_options[hide_author_meta]', // IDs can have nested array keys
					array(
						'label' => __( 'Author Meta', 'symphony' ),
						//'description' => __( 'When "show" is displayed, the author details will be displayed on your site and vise-versa.', 'symphony' ),
						'section'  => 'symphony_design_show_hide',
						'settings' => 'sds_theme_options[hide_author_meta]',
						'priority' => 40,
						'type' => 'checkbox', // Used in js controller
						'css_class' => 'sds-theme-options-show-hide-author-meta',
						'css_id' => 'sds_theme_options_hide_author_meta'
					)
				)
			);

			/**
			 * Symphony Jetpack Portfolio
			 */
			// Section
			$wp_customize->add_section( 'symphony_jetpack_portfolio', array(
				'priority' => 70, // After Show or Hide Elements
				'title' => __( 'Symphony: Jetpack Portfolio', 'symphony' ),
				'panel' => 'symphony_general_design'
			) );

			// Setting
			$wp_customize->add_setting(
				'sds_theme_options[portfolio_post_type]', // IDs can have nested array keys
				array(
					'default' => $sds_theme_options_defaults['portfolio_post_type'],
					'type' => 'option',
					// Data is also sanitized upon update_option() call using the sanitize function in $sds_theme_options_instance
					'sanitize_callback' => array( $this, 'sanitize_symphony_jetpack_portfolio' )
				)
			);

			// Control
			$wp_customize->add_control(
				new Symphony_Customizer_Jetpack_Portfolio_Control(
					$wp_customize,
					'sds_theme_options[portfolio_post_type]', // IDs can have nested array keys
					array(
						'label' => __( 'Portfolio Post Type:', 'symphony' ),
						'description' => sprintf( __( 'Symphony has built-in support for the <a href="%1$s" target="_blank">Jetpack Portfolio Custom Post Type</a>. If you choose not to use Jetpack, you can select your "Portfolio" post type. Portfolio posts will inherit select styling on the front-end display of your website.', 'symphony' ), esc_url( 'http://jetpack.me/2014/07/31/jetpack-3-1-portfolio-custom-post-types-a-new-logo-and-much-more/' ) ),
						'section'  => 'symphony_jetpack_portfolio',
						'settings' => 'sds_theme_options[portfolio_post_type]',
						'priority' => 10,
						'type' => 'select' // Used in js controller
					)
				)
			);
		}

		/**
		 * Background Color & Image Panel
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/*
			 * Background Color & Image Panel
			 */
			$wp_customize->add_panel( 'symphony_background_color_image', array(
				'priority' => 20, // After General Settings Section
				'title' => __( 'Background Colors &amp; Images', 'symphony' ),
				'description' => __( 'Adjust the background color and image on both fluid and fixed width site layouts (General Settings &gt; Site Layout).', 'symphony' ),
				//'active_callback' => 'symphony_is_fixed_width'
			) );

			/**
			 * Fluid Width Background Section
			 */
			$wp_customize->add_section( 'symphony_background_body', array(
				'priority' => 10, // Top
				'title' => __( 'Body', 'symphony' ),
				//'description' => __( 'Use the settings within this section to specify a background color and image when Genera Design &gt; Site Layout &gt; Fixed Width is set to "no".', 'symphony' ),
				'panel' => 'symphony_background_color_image',
				'active_callback' => 'symphony_is_fixed_width'
			) );

			/**
			 * Background Color
			 */
			$background_color_control = $wp_customize->get_control( 'background_color' ); // Get Control
			$background_color_control->section = 'symphony_background_body'; // Adjust Section
			$background_color_control->priority = 10; // Adjust Priority
			$background_color_control->active_callback = 'symphony_is_fixed_width'; // Adjust active callback

			/**
			 * Background Image
			 */
			$background_image_control = $wp_customize->get_control( 'background_image' ); // Get Control
			$background_image_control->section = 'symphony_background_body'; // Adjust Section
			$background_image_control->priority = 20; // Adjust Priority
			$background_image_control->active_callback = 'symphony_is_fixed_width'; // Adjust active callback
			$wp_customize->remove_section( 'background_image' ); // Remove Section

			/**
			 * Background Repeat
			 */
			$background_repeat_control = $wp_customize->get_control( 'background_repeat' ); // Get Control
			$background_repeat_control->section = 'symphony_background_body'; // Adjust Section
			$background_repeat_control->priority = 30; // Adjust Priority
			$background_repeat_control->active_callback = array( $this, 'symphony_is_symphony_fixed_width_background_image' ); // Adjust active callback

			/**
			 * Background Position X
			 */
			$background_position_x_control = $wp_customize->get_control( 'background_position_x' ); // Get Control
			$background_position_x_control->section = 'symphony_background_body'; // Adjust Section
			$background_position_x_control->priority = 40; // Adjust Priority
			$background_position_x_control->active_callback = array( $this, 'symphony_is_symphony_fixed_width_background_image' ); // Adjust active callback

			/**
			 * Background Attachment
			 */
			$background_attachment_control = $wp_customize->get_control( 'background_attachment' ); // Get Control
			$background_attachment_control->section = 'symphony_background_body'; // Adjust Section
			$background_attachment_control->priority = 50; // Adjust Priority
			$background_attachment_control->active_callback = array( $this, 'symphony_is_symphony_fixed_width_background_image' ); // Adjust active callback


			/**
			 * Fixed Width Background Section
			 */
			$wp_customize->add_section( 'symphony_background_fixed_width', array(
				'priority' => 20, // After Body
				'title' => __( 'Content', 'symphony' ),
				//'description' => __( '', 'symphony' ),
				'panel' => 'symphony_background_color_image',
				'active_callback' => 'symphony_is_fixed_width'
			) );

			/**
			 * Fixed Width Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fixed_width_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fixed_width_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_fixed_width_background_color',
					array(
						'label' => __( 'Content Background Color', 'symphony' ),
						'section' => 'symphony_background_fixed_width',
						'settings' => 'symphony_fixed_width_background_color',
						'priority' => 10,
						'active_callback' => 'symphony_is_fixed_width'
					)
				)
			);

			/**
			 * Fixed Width Background Image
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fixed_width_background_image',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fixed_width_background_image', '' ),
					'sanitize_callback' => 'wp_unslash',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'symphony_fixed_width_background_image',
					array(
						'label' => __( 'Content Background Image', 'symphony' ),
						'section' => 'symphony_background_fixed_width',
						'settings' => 'symphony_fixed_width_background_image',
						'priority' => 20,
						'active_callback' => 'symphony_is_fixed_width'
					)
				)
			);

			/**
			 * Fixed Width Background Image Repeat
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fixed_width_background_image_repeat',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fixed_width_background_image_repeat', 'repeat' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_fixed_width_background_image_repeat',
					array(
						'label' => __( 'Content Background Repeat', 'symphony' ),
						'section' => 'symphony_background_fixed_width',
						'settings' => 'symphony_fixed_width_background_image_repeat',
						'priority' => 30,
						'type' => 'radio',
						'choices' => array(
							'no-repeat' => __( 'No Repeat', 'symphony' ),
							'repeat' => __( 'Tile', 'symphony' ),
							'repeat-x' => __( 'Tile Horizontally', 'symphony' ),
							'repeat-y' => __( 'Tile Vertically', 'symphony' )
						),
						'active_callback' => array( $this, 'symphony_is_symphony_fixed_width_background_image' )
					)
				)
			);

			/**
			 * Fixed Width Background Image Position X
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fixed_width_background_image_position_x',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fixed_width_background_image_position_x', 'left' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_fixed_width_background_image_position_x',
					array(
						'label' => __( 'Content Background Position', 'symphony' ),
						'section' => 'symphony_background_fixed_width',
						'settings' => 'symphony_fixed_width_background_image_position_x',
						'priority' => 40,
						'type' => 'radio',
						'choices' => array(
							'left' => __( 'Left', 'symphony' ),
							'center' => __( 'Center', 'symphony' ),
							'right' => __( 'Right', 'symphony' )
						),
						'active_callback' => array( $this, 'symphony_is_symphony_fixed_width_background_image' )
					)
				)
			);

			/**
			 * Fluid Width Background Image Attachment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fixed_width_background_image_attachment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fixed_width_background_image_attachment', 'scroll' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_fixed_width_background_image_attachment',
					array(
						'label' => __( 'Content Background Attachment', 'symphony' ),
						'section' => 'symphony_background_fixed_width',
						'settings' => 'symphony_fixed_width_background_image_attachment',
						'priority' => 50,
						'type' => 'radio',
						'choices' => array(
							'scroll' => __( 'Scroll', 'symphony' ),
							'fixed' => __( 'Fixed', 'symphony' )
						),
						'active_callback' => array( $this, 'symphony_is_symphony_fixed_width_background_image' )
					)
				)
			);


			/**
			 * Fluid Width Background Section
			 */
			$wp_customize->add_section( 'symphony_background_fluid_width', array(
				'priority' => 30, // After Body
				'title' => __( 'Content', 'symphony' ),
				'panel' => 'symphony_background_color_image',
				'active_callback' => 'symphony_is_fluid_width'
			) );

			/**
			 * Fluid Width Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fluid_width_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fluid_width_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_fluid_width_background_color',
					array(
						'label' => __( 'Content Background Color', 'symphony' ),
						'section' => 'symphony_background_fluid_width',
						'settings' => 'symphony_fluid_width_background_color',
						'priority' => 10,
						'active_callback' => 'symphony_is_fluid_width'
					)
				)
			);

			/**
			 * Fluid Width Background Image
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fluid_width_background_image',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fluid_width_background_image', '' ),
					'sanitize_callback' => 'wp_unslash',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'symphony_fluid_width_background_image',
					array(
						'label' => __( 'Content Background Image', 'symphony' ),
						'section' => 'symphony_background_fluid_width',
						'settings' => 'symphony_fluid_width_background_image',
						'priority' => 20,
						'active_callback' => 'symphony_is_fluid_width'
					)
				)
			);

			/**
			 * Fluid Width Background Image Repeat
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fluid_width_background_image_repeat',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fluid_width_background_image_repeat', 'repeat' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_fluid_width_background_image_repeat',
					array(
						'label' => __( 'Content Background Repeat', 'symphony' ),
						'section' => 'symphony_background_fluid_width',
						'settings' => 'symphony_fluid_width_background_image_repeat',
						'priority' => 30,
						'type' => 'radio',
						'choices' => array(
							'no-repeat' => __( 'No Repeat', 'symphony' ),
							'repeat' => __( 'Tile', 'symphony' ),
							'repeat-x' => __( 'Tile Horizontally', 'symphony' ),
							'repeat-y' => __( 'Tile Vertically', 'symphony' )
						),
						'active_callback' => array( $this, 'symphony_is_symphony_fluid_width_background_image' )
					)
				)
			);

			/**
			 * Fluid Width Background Image Position X
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fluid_width_background_image_position_x',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fluid_width_background_image_position_x', 'left' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_fluid_width_background_image_position_x',
					array(
						'label' => __( 'Content Background Position', 'symphony' ),
						'section' => 'symphony_background_fluid_width',
						'settings' => 'symphony_fluid_width_background_image_position_x',
						'priority' => 40,
						'type' => 'radio',
						'choices' => array(
							'left' => __( 'Left', 'symphony' ),
							'center' => __( 'Center', 'symphony' ),
							'right' => __( 'Right', 'symphony' )
						),
						'active_callback' => array( $this, 'symphony_is_symphony_fluid_width_background_image' )
					)
				)
			);

			/**
			 * Fluid Width Background Image Attachment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_fluid_width_background_image_attachment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_fluid_width_background_image_attachment', 'scroll' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_fluid_width_background_image_attachment',
					array(
						'label' => __( 'Content Background Attachment', 'symphony' ),
						'section' => 'symphony_background_fluid_width',
						'settings' => 'symphony_fluid_width_background_image_attachment',
						'priority' => 50,
						'type' => 'radio',
						'choices' => array(
							'scroll' => __( 'Scroll', 'symphony' ),
							'fixed' => __( 'Fixed', 'symphony' )
						),
						'active_callback' => array( $this, 'symphony_is_symphony_fluid_width_background_image' )
					)
				)
			);
		}


		/**
		 * Top Header Panel
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/**
			 * Top Header Panel
			 */
			$wp_customize->add_panel( 'symphony_top_header', array(
				'priority' => 30, // After Background Colors & Images Section
				'title' => __( 'Top Header', 'symphony' ),
				'description' => __( 'This section is displayed on the front-end when a "Top Navigation" menu is set under Appearance &gt; Menus or a widget is placed in the "Top Sidebar" under Appearance &gt; Widgets.', 'symphony' ),
			) );

			/**
			 * Top Header Alignment Section
			 */
			$wp_customize->add_section( 'symphony_top_header_alignment', array(
				'priority' => 10, // Top
				'title' => __( 'Alignment', 'symphony' ),
				'panel' => 'symphony_top_header'
			) );

			/**
			 * Top Header Alignment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_alignment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_alignment', 'traditional' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_top_header_alignment',
					array(
						'label' => __( 'Alignment', 'symphony' ),
						'section' => 'symphony_top_header_alignment',
						'settings' => 'symphony_top_header_alignment',
						'priority' => 10,
						'type' => 'select',
						'choices' => array(
							'' => __( '&mdash; Select &mdash;', 'symphony' ),
							'traditional' => __( 'Traditional', 'symphony' ),
							'centered' => __( 'Centered', 'symphony' ),
							'flipped' => __( 'Flipped', 'symphony' )
						)
					)
				)
			);


			/**
			 * Top Header Navigation Section
			 */
			$wp_customize->add_section( 'symphony_top_header_navigation', array(
				'priority' => 20, // After Alignment
				'title' => __( 'Navigation', 'symphony' ),
				'panel' => 'symphony_top_header'
			) );

			/**
			 * Top Header Navigation Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_top_header_color',
					array(
						'label' => __( 'Color', 'symphony' ),
						'section' => 'symphony_top_header_navigation',
						'settings' => 'symphony_top_header_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Top Header Navigation Sub Menu Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_sub_menu_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_sub_menu_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_top_header_sub_menu_color',
					array(
						'label' => __( 'Sub Menu Color', 'symphony' ),
						'section' => 'symphony_top_header_navigation',
						'settings' => 'symphony_top_header_sub_menu_color',
						'priority' => 20
					)
				)
			);

			/**
			 * Top Header Navigation Sub Menu Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_sub_menu_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_sub_menu_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_top_header_sub_menu_background_color',
					array(
						'label' => __( 'Sub Menu Background Color', 'symphony' ),
						'section' => 'symphony_top_header_navigation',
						'settings' => 'symphony_top_header_sub_menu_background_color',
						'priority' => 30
					)
				)
			);

			/**
			 * Top Header Navigation Font Size
			 */
			$top_nav_font_size_control = $wp_customize->get_control( 'symphony_navigation_top_nav_font_size' ); // Get Control
			$top_nav_font_size_control->section = 'symphony_top_header_navigation'; // Adjust Section
			$top_nav_font_size_control->label = __( 'Font Size', 'symphony' ); // Adjust Label
			$top_nav_font_size_control->priority = 40; // Adjust Priority

			/**
			 * Top Header Navigation Font Family
			 */
			$top_nav_font_family_control = $wp_customize->get_control( 'symphony_navigation_top_nav_font_family' ); // Get Control
			$top_nav_font_family_control->section = 'symphony_top_header_navigation'; // Adjust Section
			$top_nav_font_family_control->label = __( 'Font Family', 'symphony' ); // Adjust Label
			$top_nav_font_family_control->priority = 50; // Adjust Priority


			/**
			 * Top Header Color Section
			 */
			/*$wp_customize->add_section( 'symphony_top_header_color', array(
				'priority' => 20, // After Top Header Navigation
				'title' => __( 'Color', 'symphony' ),
				'panel' => 'symphony_top_header'
			) );*/

			/**
			 * Top Header Text Color
			 */
			// Setting
			/*$wp_customize->add_setting(
				'symphony_top_header_text_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_text_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_top_header_text_color',
					array(
						'label' => __( 'Text Color', 'symphony' ),
						'description' => __( 'Adjust the text color for widgets placed in the Top Sidebar. This option will not adjust the text color of a menu placed in the Top Navigation area. See <strong>Top Header &gt; Navigation</strong> to adjust menu colors.', 'symphony' ),
						'section' => 'symphony_top_header_color',
						'settings' => 'symphony_top_header_text_color',
						'priority' => 10
					)
				)
			);*/


			/**
			 * Top Header Background Color Section
			 */
			$wp_customize->add_section( 'symphony_top_header_background', array(
				'priority' => 30, // After Navigation
				'title' => __( 'Background', 'symphony' ),
				'panel' => 'symphony_top_header'
			) );

			/**
			 * Top Header Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_top_header_background_color',
					array(
						'label' => __( 'Background Color', 'symphony' ),
						'section' => 'symphony_top_header_background',
						'settings' => 'symphony_top_header_background_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Top Header Background Image
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_background_image',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_background_image', '' ),
					'sanitize_callback' => 'wp_unslash',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'symphony_top_header_background_image',
					array(
						'label' => __( 'Background Image', 'symphony' ),
						'section' => 'symphony_top_header_background',
						'settings' => 'symphony_top_header_background_image',
						'priority' => 20,
					)
				)
			);

			/**
			 * Top Header Background Image Repeat
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_background_image_repeat',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_background_image_repeat', 'repeat' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_top_header_background_image_repeat',
					array(
						'label' => __( 'Background Repeat', 'symphony' ),
						'section' => 'symphony_top_header_background',
						'settings' => 'symphony_top_header_background_image_repeat',
						'priority' => 30,
						'type' => 'radio',
						'choices' => array(
							'no-repeat' => __( 'No Repeat', 'symphony' ),
							'repeat' => __( 'Tile', 'symphony' ),
							'repeat-x' => __( 'Tile Horizontally', 'symphony' ),
							'repeat-y' => __( 'Tile Vertically', 'symphony' )
						)
					)
				)
			);

			/**
			 * Top Header Background Image Position X
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_background_image_position_x',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_background_image_position_x', 'left' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_top_header_background_image_position_x',
					array(
						'label' => __( 'Background Position', 'symphony' ),
						'section' => 'symphony_top_header_background',
						'settings' => 'symphony_top_header_background_image_position_x',
						'priority' => 40,
						'type' => 'radio',
						'choices' => array(
							'left' => __( 'Left', 'symphony' ),
							'center' => __( 'Center', 'symphony' ),
							'right' => __( 'Right', 'symphony' )
						)
					)
				)
			);

			/**
			 * Top Header Background Image Attachment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_top_header_background_image_attachment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_top_header_background_image_attachment', 'scroll' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_top_header_background_image_attachment',
					array(
						'label' => __( 'Background Attachment', 'symphony' ),
						'section' => 'symphony_top_header_background',
						'settings' => 'symphony_top_header_background_image_attachment',
						'priority' => 50,
						'type' => 'radio',
						'choices' => array(
							'scroll' => __( 'Scroll', 'symphony' ),
							'fixed' => __( 'Fixed', 'symphony' )
						)
					)
				)
			);
		}


		/**
		 * Main Header Panel
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/*
			 * Main Header Panel
			 */
			$wp_customize->add_panel( 'symphony_main_header', array(
				'priority' => 40, // After Top Header
				'title' => __( 'Main Header', 'symphony' )
			) );

			/**
			 * Main Header Alignment Section
			 */
			$wp_customize->add_section( 'symphony_main_header_alignment', array(
				'priority' => 10, // Top
				'title' => __( 'Alignment', 'symphony' ),
				'panel' => 'symphony_main_header'
			) );

			/**
			 * Main Header Alignment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_main_header_alignment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_main_header_alignment', 'traditional' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_main_header_alignment',
					array(
						'label' => __( 'Alignment', 'symphony' ),
						'section' => 'symphony_main_header_alignment',
						'settings' => 'symphony_main_header_alignment',
						'priority' => 10,
						'type' => 'select',
						'choices' => array(
							'' => __( '&mdash; Select &mdash;', 'symphony' ),
							'traditional' => __( 'Traditional', 'symphony' ),
							'centered' => __( 'Centered', 'symphony' ),
							'flipped' => __( 'Flipped', 'symphony' ),
							'nav-below' => __( 'Navigation Below', 'symphony' )
						)
					)
				)
			);


			/**
			 * Site Title Section
			 */
			$wp_customize->add_section( 'symphony_main_header_site_title', array(
				'priority' => 20, // After Main Header Alignment
				'title' => __( 'Site Title', 'symphony' ),
				'panel' => 'symphony_main_header'
			) );

			/**
			 * Header Text Color
			 */
			// Setting
			/* $header_text_color_setting = $wp_customize->get_setting( 'header_textcolor' ); // Get Setting
			$header_text_color_setting->default = apply_filters( 'theme_mod_header_textcolor', '' ); // Adjust Default
			$header_text_color_setting->theme_supports = ''; // Adjust Theme Support */

			// Control
			/* $header_text_color_control = $wp_customize->get_control( 'header_textcolor' ); // Get Setting
			$header_text_color_control->section = 'symphony_design_header'; // Adjust Section
			$header_text_color_control->label = __( 'Text Color', 'symphony' ); // Adjust Label
			$header_text_color_control->priority = 10; // Adjust Priority
			$wp_customize->remove_control( 'display_header_text' ); // Remove Control */

			/**
			 * Site Title Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_site_title_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_site_title_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_site_title_color',
					array(
						'label' => __( 'Site Title Color', 'symphony' ),
						'section' => 'symphony_main_header_site_title',
						'settings' => 'symphony_site_title_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Site Title Font Size
			 */
			$site_title_font_size_control = $wp_customize->get_control( 'symphony_site_title_font_size' ); // Get Control
			$site_title_font_size_control->section = 'symphony_main_header_site_title'; // Adjust Section
			$site_title_font_size_control->priority = 20; // Adjust Priority

			/**
			 * Site Title Font Family
			 */
			$site_title_font_family_control = $wp_customize->get_control( 'symphony_site_title_font_family' ); // Get Control
			$site_title_font_family_control->section = 'symphony_main_header_site_title'; // Adjust Section
			$site_title_font_family_control->priority = 30; // Adjust Priority


			/**
			 * Tagline Section
			 */
			$wp_customize->add_section( 'symphony_main_header_tagline', array(
				'priority' => 30, // After Site Title
				'title' => __( 'Tagline', 'symphony' ),
				'panel' => 'symphony_main_header'
			) );

			/**
			 * Tagline Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_tagline_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_tagline_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_tagline_color',
					array(
						'label' => __( 'Tagline Color', 'symphony' ),
						'section' => 'symphony_main_header_tagline',
						'settings' => 'symphony_tagline_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Tagline Font Size
			 */
			$tagline_font_size_control = $wp_customize->get_control( 'symphony_tagline_font_size' ); // Get Control
			$tagline_font_size_control->section = 'symphony_main_header_tagline'; // Adjust Section
			$tagline_font_size_control->priority = 20; // Adjust Priority

			/**
			 * Tagline Font Family
			 */
			$tagline_font_family_control = $wp_customize->get_control( 'symphony_tagline_font_family' ); // Get Control
			$tagline_font_family_control->section = 'symphony_main_header_tagline'; // Adjust Section
			$tagline_font_family_control->priority = 30; // Adjust Priority


			/**
			 * Main Header Navigation Section
			 */
			$wp_customize->add_section( 'symphony_main_header_navigation', array(
				'priority' => 40, // After Tagline
				'title' => __( 'Navigation', 'symphony' ),
				'panel' => 'symphony_main_header'
			) );

			/**
			 * Primary Color
			 */
			// Setting
			$wp_customize->add_setting(
				'primary_color',
				array(
					'default' => apply_filters( 'theme_mod_primary_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'primary_color',
					array(
						'label' => __( 'Color', 'symphony' ),
						'section' => 'symphony_main_header_navigation',
						'settings' => 'primary_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Primary Navigation Sub Menu Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_primary_sub_menu_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_primary_sub_menu_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_primary_sub_menu_color',
					array(
						'label' => __( 'Sub Menu Color', 'symphony' ),
						'section' => 'symphony_main_header_navigation',
						'settings' => 'symphony_primary_sub_menu_color',
						'priority' => 20
					)
				)
			);

			/**
			 * Primary Navigation Sub Menu background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_primary_sub_menu_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_primary_sub_menu_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_primary_sub_menu_background_color',
					array(
						'label' => __( 'Sub Menu Background Color', 'symphony' ),
						'section' => 'symphony_main_header_navigation',
						'settings' => 'symphony_primary_sub_menu_background_color',
						'priority' => 30
					)
				)
			);

			/**
			 * Primary Navigation Font Size
			 */
			$primary_nav_font_size_control = $wp_customize->get_control( 'symphony_navigation_primary_nav_font_size' ); // Get Control
			$primary_nav_font_size_control->section = 'symphony_main_header_navigation'; // Adjust Section
			$primary_nav_font_size_control->label = __( 'Font Size', 'symphony' ); // Adjust Label
			$primary_nav_font_size_control->priority = 40; // Adjust Priority

			/**
			 * Primary Navigation Font Family
			 */
			$primary_nav_font_family_control = $wp_customize->get_control( 'symphony_navigation_primary_nav_font_family' ); // Get Control
			$primary_nav_font_family_control->section = 'symphony_main_header_navigation'; // Adjust Section
			$primary_nav_font_family_control->label = __( 'Font Family', 'symphony' ); // Adjust Label
			$primary_nav_font_family_control->priority = 50; // Adjust Priority


			/**
			 * Main Header Background Section
			 */
			$wp_customize->add_section( 'symphony_main_header_background', array(
				'priority' => 50, // After Navigation
				'title' => __( 'Background', 'symphony' ),
				'panel' => 'symphony_main_header'
			) );

			/**
			 * Header Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_header_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_header_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_header_background_color',
					array(
						'label' => __( 'Background Color', 'symphony' ),
						'section' => 'symphony_main_header_background',
						'settings' => 'symphony_header_background_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Header Background Image
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_header_background_image',
				array(
					'default' => apply_filters( 'theme_mod_symphony_header_background_image', '' ),
					'sanitize_callback' => 'wp_unslash',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'symphony_header_background_image',
					array(
						'label' => __( 'Background Image', 'symphony' ),
						'section' => 'symphony_main_header_background',
						'settings' => 'symphony_header_background_image',
						'priority' => 20,
					)
				)
			);

			/**
			 * Header Background Image Repeat
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_header_background_image_repeat',
				array(
					'default' => apply_filters( 'theme_mod_symphony_header_background_image_repeat', 'repeat' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_header_background_image_repeat',
					array(
						'label' => __( 'Header Background Repeat', 'symphony' ),
						'section' => 'symphony_main_header_background',
						'settings' => 'symphony_header_background_image_repeat',
						'priority' => 30,
						'type' => 'radio',
						'choices' => array(
							'no-repeat' => __( 'No Repeat', 'symphony' ),
							'repeat' => __( 'Tile', 'symphony' ),
							'repeat-x' => __( 'Tile Horizontally', 'symphony' ),
							'repeat-y' => __( 'Tile Vertically', 'symphony' )
						)
					)
				)
			);

			/**
			 * Header Background Image Position X
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_header_background_image_position_x',
				array(
					'default' => apply_filters( 'theme_mod_symphony_header_background_image_position_x', 'left' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_header_background_image_position_x',
					array(
						'label' => __( 'Header Background Position', 'symphony' ),
						'section' => 'symphony_main_header_background',
						'settings' => 'symphony_header_background_image_position_x',
						'priority' => 40,
						'type' => 'radio',
						'choices' => array(
							'left' => __( 'Left', 'symphony' ),
							'center' => __( 'Center', 'symphony' ),
							'right' => __( 'Right', 'symphony' )
						)
					)
				)
			);

			/**
			 * Header Background Image Attachment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_header_background_image_attachment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_header_background_image_attachment', 'scroll' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_header_background_image_attachment',
					array(
						'label' => __( 'Header Background Attachment', 'symphony' ),
						'section' => 'symphony_main_header_background',
						'settings' => 'symphony_header_background_image_attachment',
						'priority' => 50,
						'type' => 'radio',
						'choices' => array(
							'scroll' => __( 'Scroll', 'symphony' ),
							'fixed' => __( 'Fixed', 'symphony' )
						)
					)
				)
			);
		}

		/**
		 * Secondary Header Panel
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/*
			 * Secondary Header Panel
			 */
			$wp_customize->add_panel( 'symphony_secondary_header', array(
				'priority' => 50, // After Main Header
				'title' => __( 'Secondary Header', 'symphony' )
			) );

			/**
			 * Secondary Header Alignment Section
			 */
			$wp_customize->add_section( 'symphony_secondary_header_alignment', array(
				'priority' => 10, // Top
				'title' => __( 'Alignment', 'symphony' ),
				'panel' => 'symphony_secondary_header'
			) );

			/**
			 * Secondary Header Alignment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_alignment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_alignment', 'traditional' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_secondary_header_alignment',
					array(
						'label' => __( 'Alignment', 'symphony' ),
						'section' => 'symphony_secondary_header_alignment',
						'settings' => 'symphony_secondary_header_alignment',
						'priority' => 10,
						'type' => 'select',
						'choices' => array(
							'' => __( '&mdash; Select &mdash;', 'symphony' ),
							'traditional' => __( 'Traditional', 'symphony' ),
							'centered' => __( 'Centered', 'symphony' ),
							'flipped' => __( 'Flipped', 'symphony' )
						)
					)
				)
			);


			/**
			 * Secondary Header Navigation Section
			 */
			$wp_customize->add_section( 'symphony_secondary_header_navigation', array(
				'priority' => 20, // After Secondary Header Alignment
				'title' => __( 'Navigation', 'symphony' ),
				'description' => __( 'This section is displayed on the front-end when a "Secondary Navigation" menu is set under Appearance &gt; Menus or a widget is placed in the "Secondary Sidebar" under Appearance &gt; Widgets.', 'symphony' ),
				'panel' => 'symphony_secondary_header'
			) );

			/**
			 * Secondary Color
			 */

			// Setting
			$wp_customize->add_setting(
				'secondary_color',
				array(
					'default' => apply_filters( 'theme_mod_secondary_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'secondary_color',
					array(
						'label' => __( 'Secondary Theme Color', 'symphony' ),
						'section' => 'symphony_secondary_header_navigation',
						'settings' => 'secondary_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Secondary Header Navigation Sub Menu Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_sub_menu_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_sub_menu_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_secondary_header_sub_menu_color',
					array(
						'label' => __( 'Sub Menu Color', 'symphony' ),
						'section' => 'symphony_secondary_header_navigation',
						'settings' => 'symphony_secondary_header_sub_menu_color',
						'priority' => 20
					)
				)
			);

			/**
			 * Secondary Header Navigation Sub Menu Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_sub_menu_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_sub_menu_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_secondary_header_sub_menu_background_color',
					array(
						'label' => __( 'Sub Menu Background Color', 'symphony' ),
						'section' => 'symphony_secondary_header_navigation',
						'settings' => 'symphony_secondary_header_sub_menu_background_color',
						'priority' => 30
					)
				)
			);

			/**
			 * Secondary Navigation Font Size
			 */
			$secondary_nav_font_size_control = $wp_customize->get_control( 'symphony_navigation_secondary_nav_font_size' ); // Get Control
			$secondary_nav_font_size_control->section = 'symphony_secondary_header_navigation'; // Adjust Section
			$secondary_nav_font_size_control->label = __( 'Font Family', 'symphony' ); // Adjust Label
			$secondary_nav_font_size_control->priority = 40; // Adjust Priority

			/**
			 * Secondary Navigation Font Family
			 */
			$secondary_nav_font_family_control = $wp_customize->get_control( 'symphony_navigation_secondary_nav_font_family' ); // Get Control
			$secondary_nav_font_family_control->section = 'symphony_secondary_header_navigation'; // Adjust Section
			$secondary_nav_font_family_control->label = __( 'Font Family', 'symphony' ); // Adjust Label
			$secondary_nav_font_family_control->priority = 50; // Adjust Priority


			/**
			 * Secondary Header Color Section
			 */
			/*$wp_customize->add_section( 'symphony_secondary_header_color', array(
				'priority' => 20, // After Secondary Header Navigation
				'title' => __( 'Color', 'symphony' ),
				'panel' => 'symphony_secondary_header'
			) );*/

			/**
			 * Secondary Header Text Color
			 */
			// Setting
			/*$wp_customize->add_setting(
				'symphony_secondary_header_text_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_text_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_secondary_header_text_color',
					array(
						'label' => __( 'Text Color', 'symphony' ),
						'description' => __( 'Adjust the text color for widgets placed in the Secondary Sidebar. This option will not adjust the text color of a menu placed in the Secondary Navigation area. See <strong>Secondary Header &gt; Navigation</strong> to adjust menu colors.', 'symphony' ),
						'section' => 'symphony_secondary_header_color',
						'settings' => 'symphony_secondary_header_text_color',
						'priority' => 10
					)
				)
			);*/


			/**
			 * Secondary Header Background Section
			 */
			$wp_customize->add_section( 'symphony_secondary_header_background', array(
				'priority' => 30, // After Secondary Header Color
				'title' => __( 'Background', 'symphony' ),
				'panel' => 'symphony_secondary_header'
			) );

			/**
			 * Secondary Header Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_secondary_header_background_color',
					array(
						'label' => __( 'Background Color', 'symphony' ),
						'section' => 'symphony_secondary_header_background',
						'settings' => 'symphony_secondary_header_background_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Secondary Header Background Image
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_background_image',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_background_image', '' ),
					'sanitize_callback' => 'wp_unslash',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'symphony_secondary_header_background_image',
					array(
						'label' => __( 'Background Image', 'symphony' ),
						'section' => 'symphony_secondary_header_background',
						'settings' => 'symphony_secondary_header_background_image',
						'priority' => 20,
					)
				)
			);

			/**
			 * Secondary Header Background Image Repeat
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_background_image_repeat',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_background_image_repeat', 'repeat' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_secondary_header_background_image_repeat',
					array(
						'label' => __( 'Background Repeat', 'symphony' ),
						'section' => 'symphony_secondary_header_background',
						'settings' => 'symphony_secondary_header_background_image_repeat',
						'priority' => 30,
						'type' => 'radio',
						'choices' => array(
							'no-repeat' => __( 'No Repeat', 'symphony' ),
							'repeat' => __( 'Tile', 'symphony' ),
							'repeat-x' => __( 'Tile Horizontally', 'symphony' ),
							'repeat-y' => __( 'Tile Vertically', 'symphony' )
						)
					)
				)
			);

			/**
			 * Secondary Header Background Image Position X
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_background_image_position_x',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_background_image_position_x', 'left' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_secondary_header_background_image_position_x',
					array(
						'label' => __( 'Background Position', 'symphony' ),
						'section' => 'symphony_secondary_header_background',
						'settings' => 'symphony_secondary_header_background_image_position_x',
						'priority' => 40,
						'type' => 'radio',
						'choices' => array(
							'left' => __( 'Left', 'symphony' ),
							'center' => __( 'Center', 'symphony' ),
							'right' => __( 'Right', 'symphony' )
						)
					)
				)
			);

			/**
			 * Secondary Header Background Image Attachment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_secondary_header_background_image_attachment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_secondary_header_background_image_attachment', 'scroll' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_secondary_header_background_image_attachment',
					array(
						'label' => __( 'Background Attachment', 'symphony' ),
						'section' => 'symphony_secondary_header_background',
						'settings' => 'symphony_secondary_header_background_image_attachment',
						'priority' => 50,
						'type' => 'radio',
						'choices' => array(
							'scroll' => __( 'Scroll', 'symphony' ),
							'fixed' => __( 'Fixed', 'symphony' )
						)
					)
				)
			);
		}

		/**
		 * Content Panel
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/*
			 * Content Panel
			 */
			$wp_customize->add_panel( 'symphony_content', array(
				'priority' => 60, // After Secondary Header
				'title' => __( 'Content', 'symphony' )
			) );

			/**
			 * Color Section
			 */
			$wp_customize->add_section( 'symphony_content_colors', array(
				'priority' => 10, // Top
				'title' => __( 'Colors', 'symphony' ),
				'panel' => 'symphony_content'
			) );

			/**
			 * Archive Title Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_archive_title_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_archive_title_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_archive_title_color',
					array(
						'label' => __( 'Archive Title', 'symphony' ),
						'section' => 'symphony_content_colors',
						'settings' => 'symphony_archive_title_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Post Title Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_post_title_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_post_title_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_post_title_color',
					array(
						'label' => __( 'Post Title', 'symphony' ),
						'section' => 'symphony_content_colors',
						'settings' => 'symphony_post_title_color',
						'priority' => 20
					)
				)
			);

			/**
			 * Content Color (registered in SDS Core)
			 */
			$content_color_control = $wp_customize->get_control( 'content_color' ); // Get Control
			$content_color_control->section = 'symphony_content_colors'; // Adjust Section
			$content_color_control->label = __( 'Content', 'symphony' ); // Adjust Label
			$content_color_control->priority = 30; // Adjust Priority

			/**
			 * Link Color
			 */
			// Setting
			$wp_customize->add_setting(
				'link_color',
				array(
					'default' => apply_filters( 'theme_mod_link_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'link_color',
					array(
						'label' => __( 'Link', 'symphony' ),
						'section' => 'symphony_content_colors',
						'settings' => 'link_color',
						'priority' => 40
					)
				)
			);

			/**
			 * Button Text Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_button_text_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_button_text_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_button_text_color',
					array(
						'label' => __( 'Button Text', 'symphony' ),
						'section' => 'symphony_content_colors',
						'settings' => 'symphony_button_text_color',
						'priority' => 50
					)
				)
			);

			/**
			 * Button Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_button_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_button_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_button_background_color',
					array(
						'label' => __( 'Button Background', 'symphony' ),
						'section' => 'symphony_content_colors',
						'settings' => 'symphony_button_background_color',
						'priority' => 60
					)
				)
			);


			/**
			 * Headings Section
			 */
			$wp_customize->add_section( 'symphony_content_headings', array(
				'priority' => 20, // After Colors
				'title' => __( 'Headings', 'symphony' ),
				'panel' => 'symphony_content'
			) );

			/**
			 * Heading 1 Font Size
			 */
			$heading_1_font_size_control = $wp_customize->get_control( 'symphony_h1_font_size' ); // Get Control
			$heading_1_font_size_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 1 Font Family
			 */
			$heading_1_font_family_control = $wp_customize->get_control( 'symphony_h1_font_family' ); // Get Control
			$heading_1_font_family_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 2 Font Size
			 */
			$heading_2_font_size_control = $wp_customize->get_control( 'symphony_h2_font_size' ); // Get Control
			$heading_2_font_size_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 2 Font Family
			 */
			$heading_2_font_family_control = $wp_customize->get_control( 'symphony_h2_font_family' ); // Get Control
			$heading_2_font_family_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 3 Font Size
			 */
			$heading_3_font_size_control = $wp_customize->get_control( 'symphony_h3_font_size' ); // Get Control
			$heading_3_font_size_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 3 Font Family
			 */
			$heading_3_font_family_control = $wp_customize->get_control( 'symphony_h3_font_family' ); // Get Control
			$heading_3_font_family_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 4 Font Size
			 */
			$heading_4_font_size_control = $wp_customize->get_control( 'symphony_h4_font_size' ); // Get Control
			$heading_4_font_size_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 4 Font Family
			 */
			$heading_4_font_family_control = $wp_customize->get_control( 'symphony_h4_font_family' ); // Get Control
			$heading_4_font_family_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 5 Font Size
			 */
			$heading_5_font_size_control = $wp_customize->get_control( 'symphony_h5_font_size' ); // Get Control
			$heading_5_font_size_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 5 Font Family
			 */
			$heading_5_font_family_control = $wp_customize->get_control( 'symphony_h5_font_family' ); // Get Control
			$heading_5_font_family_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 6 Font Size
			 */
			$heading_6_font_size_control = $wp_customize->get_control( 'symphony_h6_font_size' ); // Get Control
			$heading_6_font_size_control->section = 'symphony_content_headings'; // Adjust Section

			/**
			 * Heading 6 Font Family
			 */
			$heading_6_font_family_control = $wp_customize->get_control( 'symphony_h6_font_family' ); // Get Control
			$heading_6_font_family_control->section = 'symphony_content_headings'; // Adjust Section


			/**
			 * Body (Content) Section
			 */
			$wp_customize->add_section( 'symphony_content_body', array(
				'priority' => 30, // After Headings
				'title' => __( 'Body', 'symphony' ),
				'panel' => 'symphony_content'
			) );

			/**
			 * Body (Content) Font Size
			 */
			$body_font_size_control = $wp_customize->get_control( 'symphony_body_font_size' ); // Get Control
			$body_font_size_control->section = 'symphony_content_body'; // Adjust Section

			/**
			 * Body (Content) Font Size
			 */
			$body_line_height_control = $wp_customize->get_control( 'symphony_body_line_height' ); // Get Control
			$body_line_height_control->section = 'symphony_content_body'; // Adjust Section

			/**
			 * Body (Content) Font Family
			 */
			$body_font_family_control = $wp_customize->get_control( 'symphony_body_font_family' ); // Get Control
			$body_font_family_control->section = 'symphony_content_body'; // Adjust Section


			/**
			 * More Link Section
			 */
			$wp_customize->add_section( 'symphony_content_more_link', array(
				'priority' => 40, // After Body (Content)
				'title' => __( 'More Link', 'symphony' ),
				'panel' => 'symphony_content'
			) );

			/**
			 * More Link Button Label
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_more_link_label',
				array(
					'default' => apply_filters( 'theme_mod_symphony_more_link_label', '' ),
					'sanitize_callback' => 'sanitize_text_field'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_more_link_label',
					array(
						'label' => __( 'Button Label', 'symphony' ),
						'section' => 'symphony_content_more_link',
						'settings' => 'symphony_more_link_label',
						'priority' => 10
					)
				)
			);
		}


		/**
		 * Widget Design Panel
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/*
			 * Widget Design Panel
			 */
			$wp_customize->add_panel( 'symphony_widget_design', array(
				'priority' => 70, // After Content
				'title' => __( 'Widget Design', 'symphony' )
			) );

			/**
			 * Color Section
			 */
			$wp_customize->add_section( 'symphony_widget_colors', array(
				'priority' => 10, // Top
				'title' => __( 'Colors', 'symphony' ),
				'panel' => 'symphony_widget_design'
			) );

			/**
			 * Widget Title Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_widget_title_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_widget_title_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_widget_title_color',
					array(
						'label' => __( 'Widget Title Color', 'symphony' ),
						'section' => 'symphony_widget_colors',
						'settings' => 'symphony_widget_title_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Widget Text Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_widget_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_widget_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_widget_color',
					array(
						'label' => __( 'Text Color', 'symphony' ),
						'section' => 'symphony_widget_colors',
						'settings' => 'symphony_widget_color',
						'priority' => 20
					)
				)
			);

			/**
			 * Widget Link Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_widget_link_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_widget_link_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_widget_link_color',
					array(
						'label' => __( 'Link Color', 'symphony' ),
						'section' => 'symphony_widget_colors',
						'settings' => 'symphony_widget_link_color',
						'priority' => 30
					)
				)
			);
		}


		/**
		 * Footer Panel
		 */
		if ( $this->version_compare( '4.0' ) ) {
			/*
			 * Design Panel
			 */
			$wp_customize->add_panel( 'symphony_footer', array(
				'priority' => 80, // After Content
				'title' => __( 'Footer', 'symphony' )
			) );

			/**
			 * Colors Section
			 */
			$wp_customize->add_section( 'symphony_footer_colors', array(
				'priority' => 10, // Top
				'title' => __( 'Colors', 'symphony' ),
				'panel' => 'symphony_footer'
			) );

			/**
			 * Footer Text Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_footer_text_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_footer_text_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_footer_text_color',
					array(
						'label' => __( 'Text Color', 'symphony' ),
						'section' => 'symphony_footer_colors',
						'settings' => 'symphony_footer_text_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Footer Link Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_footer_link_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_footer_link_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_footer_link_color',
					array(
						'label' => __( 'Link', 'symphony' ),
						'section' => 'symphony_footer_colors',
						'settings' => 'symphony_footer_link_color',
						'priority' => 20
					)
				)
			);


			/**
			 * Background Section
			 */
			$wp_customize->add_section( 'symphony_footer_background', array(
				'priority' => 30, // After Link Color
				'title' => __( 'Background', 'symphony' ),
				'panel' => 'symphony_footer'
			) );

			/**
			 * Footer Background Color
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_footer_background_color',
				array(
					'default' => apply_filters( 'theme_mod_symphony_footer_background_color', '' ),
					'sanitize_callback' => 'sanitize_hex_color',
					'sanitize_js_callback' => 'maybe_hash_hex_color'
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'symphony_footer_background_color',
					array(
						'label' => __( 'Background Color', 'symphony' ),
						'section' => 'symphony_footer_background',
						'settings' => 'symphony_footer_background_color',
						'priority' => 10
					)
				)
			);

			/**
			 * Footer Background Image
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_footer_background_image',
				array(
					'default' => apply_filters( 'theme_mod_symphony_footer_background_image', '' ),
					'sanitize_callback' => 'wp_unslash',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'symphony_footer_background_image',
					array(
						'label' => __( 'Background Image', 'symphony' ),
						'section' => 'symphony_footer_background',
						'settings' => 'symphony_footer_background_image',
						'priority' => 20,
					)
				)
			);

			/**
			 * Footer Background Image Repeat
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_footer_background_image_repeat',
				array(
					'default' => apply_filters( 'theme_mod_symphony_footer_background_image_repeat', 'repeat' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_footer_background_image_repeat',
					array(
						'label' => __( 'Footer Background Repeat', 'symphony' ),
						'section' => 'symphony_footer_background',
						'settings' => 'symphony_footer_background_image_repeat',
						'priority' => 30,
						'type' => 'radio',
						'choices' => array(
							'no-repeat' => __( 'No Repeat', 'symphony' ),
							'repeat' => __( 'Tile', 'symphony' ),
							'repeat-x' => __( 'Tile Horizontally', 'symphony' ),
							'repeat-y' => __( 'Tile Vertically', 'symphony' )
						)
					)
				)
			);

			/**
			 * Footer Background Image Position X
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_footer_background_image_position_x',
				array(
					'default' => apply_filters( 'theme_mod_symphony_footer_background_image_position_x', 'left' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_footer_background_image_position_x',
					array(
						'label' => __( 'Footer Background Position', 'symphony' ),
						'section' => 'symphony_footer_background',
						'settings' => 'symphony_footer_background_image_position_x',
						'priority' => 40,
						'type' => 'radio',
						'choices' => array(
							'left' => __( 'Left', 'symphony' ),
							'center' => __( 'Center', 'symphony' ),
							'right' => __( 'Right', 'symphony' )
						)
					)
				)
			);

			/**
			 * Footer Background Image Attachment
			 */
			// Setting
			$wp_customize->add_setting(
				'symphony_footer_background_image_attachment',
				array(
					'default' => apply_filters( 'theme_mod_symphony_footer_background_image_attachment', 'scroll' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// Control
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'symphony_footer_background_image_attachment',
					array(
						'label' => __( 'Footer Background Attachment', 'symphony' ),
						'section' => 'symphony_footer_background',
						'settings' => 'symphony_footer_background_image_attachment',
						'priority' => 50,
						'type' => 'radio',
						'choices' => array(
							'scroll' => __( 'Scroll', 'symphony' ),
							'fixed' => __( 'Fixed', 'symphony' )
						)
					)
				)
			);
		}


		/*
		 * Navigation Menus
		 */
		if ( $this->version_compare( '4.0' ) ) {
			$locations = get_registered_nav_menus();
			$menus = wp_get_nav_menus();

			// If we have menus
			if ( $menus ) {
				foreach ( $locations as $location => $description ) {
					$nav_location_control = $wp_customize->get_control( 'nav_menu_locations[' . $location . ']' ); // Get Control

					switch( $location ) {
						// Top Navigation
						case 'top_nav':
							$nav_location_control->section = 'symphony_top_header_navigation'; // Adjust Section
						break;
						// Primary Navigation
						case 'primary_nav':
							$nav_location_control->section = 'symphony_main_header_navigation'; // Adjust Section
						break;
						// Secondary Navigation
						case 'secondary_nav':
							$nav_location_control->section = 'symphony_secondary_header_navigation'; // Adjust Section
						break;
					}

					$nav_location_control->priority = 5; // Adjust Priority (top)
				}

				// Navigation Section
				$wp_customize->remove_section( 'nav' ); // Remove Section
			}
		}

		// TODO: below 4.0 support
	}

	/**
	 * This function enqueues scripts and styles on the Customizer.
	 */
	function customize_controls_enqueue_scripts() {
		$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance

		// Symphony Customize Controls
		wp_enqueue_script( 'symphony-customize-controls', get_template_directory_uri() . '/customizer/js/symphony-customize-controls.js', array( 'customize-controls' ), $this->version );

		$localization_data = apply_filters( 'symphony_customize_controls_localization', array(
			// TODO: Add other support features here (if necessary)
			'theme_support' => array(
				'fonts' => $symphony_theme_helper->current_theme_supports( 'fonts' )
			),
			// WordPress 4.0
			'is_wp_4_0' => $this->version_compare( '4.1', '<' )
		) );

		// Symphony Font Customizer Localization
		if ( $symphony_theme_helper->current_theme_supports( 'fonts' ) ) {
			// Load Symphony Customizer Fonts if necessary
			if ( ! function_exists( 'Symphony_Customizer_Fonts' ) )
				include_once( 'class-symphony-customizer-fonts.php' ); // Customizer Font Settings/Controls

			// Grab the Symphony_Customizer_Fonts instance
			$symphony_customizer_fonts = Symphony_Customizer_Fonts();

			$localization_data['google_font_families'] = $symphony_customizer_fonts->get_google_fonts_choices( false, true );
			$localization_data['symphony_ff_control_regex'] = '^symphony_.+_font_family$';
		}

		wp_localize_script( 'symphony-customize-controls', 'symphony_customize_controls', $localization_data );

		// Symphony Customizer CSS
		wp_enqueue_style( 'symphony-customizer', get_template_directory_uri() . '/customizer/css/symphony-customizer.css', array( 'sds-theme-options' ) );

		// Select2
		wp_enqueue_script( 'select2', get_template_directory_uri() . '/customizer/js/select2/select2.min.js', array( 'jquery' ), $this->version );
		wp_enqueue_style( 'select2', get_template_directory_uri() . '/customizer/js/select2/select2.css' );
	}

	/**
	 * This function sets the default primary color in the Customizer.
	 */
	function theme_mod_primary_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default post title color in the Customizer.
	 */
	function theme_mod_symphony_post_title_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default content color in the Customizer.
	 */
	function theme_mod_content_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default content link color in the Customizer.
	 */
	function theme_mod_link_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default archive title color in the Customizer.
	 */
	function theme_mod_symphony_archive_title_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default more link button text color in the Customizer.
	 */
	function theme_mod_symphony_button_text_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default more link button background color in the Customizer.
	 */
	function theme_mod_symphony_button_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#ffffff';
	}

	/**
	 * This function sets the default widget title color in the Customizer.
	 */
	function theme_mod_symphony_widget_title_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default widget color in the Customizer.
	 */
	function theme_mod_symphony_widget_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default widget link color in the Customizer.
	 */
	function theme_mod_symphony_widget_link_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default color for the content area in the Customizer.
	 */
	function theme_mod_symphony_fixed_width_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#ffffff';
	}

	/**
	 * This function sets the default color for the fluid width area in the Customizer.
	 */
	function theme_mod_symphony_fluid_width_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#ffffff';
	}

	/**
	 * This function sets the default top color in the Customizer.
	 */
	function theme_mod_symphony_top_header_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default top header text color in the Customizer.
	 */
	function theme_mod_symphony_top_header_text_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default top navigation sub menu color in the Customizer.
	 */
	function theme_mod_symphony_top_header_sub_menu_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default top navigation sub menu background color in the Customizer.
	 */
	function theme_mod_symphony_top_header_sub_menu_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#fdfdfd';
	}

	/**
	 * This function sets the default top header background color in the Customizer.
	 */
	function theme_mod_symphony_top_header_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#ffffff';
	}

	/**
	 * This function sets the default header text color in the Customizer.
	 */
	function theme_mod_symphony_header_textcolor( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default site title color in the Customizer.
	 */
	function theme_mod_symphony_site_title_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default tagline color in the Customizer.
	 */
	function theme_mod_symphony_tagline_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#acacac';
	}

	/**
	 * This function sets the default primary navigation sub menu color in the Customizer.
	 */
	function theme_mod_symphony_primary_sub_menu_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default primary navigation sub menu background color in the Customizer.
	 */
	function theme_mod_symphony_primary_sub_menu_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#fdfdfd';
	}

	/**
	 * This function sets the default header background color in the Customizer.
	 */
	function theme_mod_symphony_header_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#ffffff';
	}

	/**
	 * This function sets the default secondary color in the Customizer.
	 */
	function theme_mod_secondary_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default secondary header text color in the Customizer.
	 */
	function theme_mod_symphony_secondary_header_text_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#363636';
	}

	/**
	 * This function sets the default secondary navigation sub menu color in the Customizer.
	 */
	function theme_mod_symphony_secondary_header_sub_menu_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default secondary navigation sub menu background color in the Customizer.
	 */
	function theme_mod_symphony_secondary_header_sub_menu_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#fdfdfd';
	}

	/**
	 * This function sets the default secondary header background color in the Customizer.
	 */
	function theme_mod_symphony_secondary_header_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#ffffff';
	}

	/**
	 * This function sets the default footer text color in the Customizer.
	 */
	function theme_mod_symphony_footer_text_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#acacac';
	}

	/**
	 * This function sets the default footer link color in the Customizer.
	 */
	function theme_mod_symphony_footer_link_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#1769ff';
	}

	/**
	 * This function sets the default footer background color in the Customizer.
	 */
	function theme_mod_symphony_footer_background_color( $color = false ) {
		// Return the current color if set
		if ( $color )
			return $color;

		// Return the default
		return '#ffffff';
	}

	/**
	 * This function sets the default more link label in the Customizer.
	 */
	function theme_mod_symphony_more_link_label( $label = false ) {
		// Return the current color if set
		if ( $label )
			return $label;

		// Return the default
		return symphony_more_link_label( true ) ;
	}

	/**
	 * This function adjusts the body classes based on theme mods.
	 */
	function body_class( $classes ) {
		$alignment_classes = '';

		// Fixed or fluid width
		$classes['symphony_fluid_width'] = ( symphony_is_fixed_width() ) ? 'fixed-width': 'fluid-width';

		// Featured Image Alignment
		if ( ! is_singular() && ( $featured_image_alignment = get_theme_mod( 'symphony_featured_image_alignment' ) ) )
			$classes['symphony_featured_image_alignment'] = 'featured-image-' . $featured_image_alignment;

		// Max Width
		if ( ( $theme_mod_symphony_max_width = $this->get_theme_mod( 'symphony_max_width', 1600 ) ) )
			$classes['symphony_max_width'] = 'custom-max-width custom-max-width-' . $theme_mod_symphony_max_width . ' max-width-' . $theme_mod_symphony_max_width;

		// Top Header Alignment (ignore default value in $this->get_theme_mod() check)
		if ( ( $theme_mod_symphony_top_header_alignment = $this->get_theme_mod( 'symphony_top_header_alignment' ) ) )
			$classes['symphony_top_header_alignment'] = 'custom-top-header-alignment top-header-' . $theme_mod_symphony_top_header_alignment . ' top-header-alignment-' . $theme_mod_symphony_top_header_alignment;
		else
			$classes['symphony_top_header_alignment'] = 'top-header-traditional top-header-alignment-traditional';

		// Main Header Alignment (ignore default value in $this->get_theme_mod() check)
		if ( ( $theme_mod_symphony_main_header_alignment = $this->get_theme_mod( 'symphony_main_header_alignment' ) ) )
			$classes['symphony_main_header_alignment'] = 'custom-header-alignment header-' . $theme_mod_symphony_main_header_alignment . ' main-header-' . $theme_mod_symphony_main_header_alignment . ' header-alignment-' . $theme_mod_symphony_main_header_alignment . ' main-header-alignment-' . $theme_mod_symphony_main_header_alignment;
		else
			$classes['symphony_main_header_alignment'] = 'header-traditional main-header-traditional header-alignment-traditional main-header-alignment-traditional';

		// Secondary Header Alignment (ignore default value in $this->get_theme_mod() check)
		if ( ( $theme_mod_symphony_secondary_header_alignment = $this->get_theme_mod( 'symphony_secondary_header_alignment' ) ) )
			$classes['symphony_secondary_header_alignment'] = 'custom-secondary-header-alignment secondary-header-' . $theme_mod_symphony_secondary_header_alignment . ' secondary-header-alignment-' . $theme_mod_symphony_secondary_header_alignment;
		else
			$classes['symphony_secondary_header_alignment'] = 'secondary-header-traditional top-header-alignment-traditional';

		return $classes;
	}

	/**
	 * This function returns a CSS <style> block for Customizer theme mods.
	 */
	// TODO: Variable names might be too long
	function get_customizer_css() {
		// Check transient first (not in the Customizer)
		if ( ! $this->is_customize_preview() && ! empty( $this->transient_data ) && isset( $this->transient_data['customizer_css' ] ) )
			return $this->transient_data['customizer_css'];
		// Otherwise return data
		else {
			$sds_theme_options_instance = SDS_Theme_Options_Instance();

			// Open <style>
			$r = '<style type="text/css" id="' . $sds_theme_options_instance->get_parent_theme()->get_template() . '-customizer">';

			// If we have a primary color selected by the user
			if ( ( $theme_mod_symphony_max_width = $this->get_theme_mod( 'symphony_max_width', 1600 ) ) ) {
				$r .= '/* Maximum Width */' . "\n";
				$r .= '.fluid-width header#header div.in,' . "\n";
				$r .= '.fluid-width aside.secondary-header-sidebar div.in,' . "\n";
				$r .= '.fluid-width footer#footer div.in,' . "\n";
				$r .= '.fluid-width .content-container div.in,' . "\n";
				$r .= '.fluid-width .portfolio-nav-container div.in,' . "\n";
				$r .= '.fluid-width .top-nav div.in, .fluid-width .top-sidebar div.in,' . "\n";
				$r .= '.fixed-width > div.in {' . "\n";
					$r .= 'max-width: ' . $theme_mod_symphony_max_width . 'px;' . "\n";
				$r .= '}' . "\n\n";

				// Top Navigation/Sidebar
				$r .= '.fixed-width .top-nav div.in, .fixed-width .top-sidebar div.in {' . "\n";
					$r .= 'max-width: ' . ( $theme_mod_symphony_max_width - 40 ). 'px;' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a primary color selected by the user
			if ( ( $theme_mod_primary_color = $this->get_theme_mod( 'primary_color', $this->theme_mod_primary_color() ) ) ) {
				$r .= '/* Primary Color */' . "\n";
				$r .= 'nav.primary-nav-container .primary-nav li a, nav.primary-nav-container .primary-nav-button {' . "\n";
					$r .= 'color: ' . $theme_mod_primary_color . ';' . "\n";
				$r .= '}' . "\n\n";


				$r .= 'nav.primary-nav-container {' . "\n";
					$r .= 'border-color: ' . $theme_mod_primary_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a content color selected by the user
			if ( ( $theme_mod_content_color = $this->get_theme_mod( 'content_color', $this->theme_mod_content_color() ) ) ) {
				$r .= '/* Content Color */' . "\n";
				$r .= 'article.content, ul.page-numbers li span.current {' . "\n";
					$r .= 'color: ' . $theme_mod_content_color . ';' . "\n";
				$r .= '}' . "\n\n";

				$r .= '.woocommerce nav.woocommerce-pagination ul li span.current,' . "\n";
				$r .= '.woocommerce #content nav.woocommerce-pagination ul li span.current,' . "\n";
				$r .= '.woocommerce-page nav.woocommerce-pagination ul li span.current,' . "\n";
				$r .= '.woocommerce-page #content nav.woocommerce-pagination ul li span.current {' . "\n";
					$r .= 'color: ' . $theme_mod_content_color . ' !important;' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a content link color selected by the user
			if ( ( $theme_mod_link_color = $this->get_theme_mod( 'link_color', $this->theme_mod_link_color() ) ) ) {
				$r .= '/* Content Link Color */' . "\n";
				$r .= '.content a, article.content a {' . "\n";
					$r .= 'color: ' . $theme_mod_link_color . ';' . "\n";
				$r .= '}' . "\n\n";

				$r .= '.woocommerce nav.woocommerce-pagination ul li a:hover,' . "\n";
				$r .= '.woocommerce nav.woocommerce-pagination ul li a:focus,' . "\n";
				$r .= '.woocommerce #content nav.woocommerce-pagination ul li a:hover,' . "\n";
				$r .= '.woocommerce #content nav.woocommerce-pagination ul li a:focus,' . "\n";
				$r .= '.woocommerce-page nav.woocommerce-pagination ul li a:hover,' . "\n";
				$r .= '.woocommerce-page nav.woocommerce-pagination ul li a:focus,' . "\n";
				$r .= '.woocommerce-page #content nav.woocommerce-pagination ul li a:hover,' . "\n";
				$r .= '.woocommerce-page #content nav.woocommerce-pagination ul li a:focus,' . "\n";

				$r .= '.woocommerce nav.woocommerce-pagination ul li a:active,' . "\n";
				$r .= '.woocommerce nav.woocommerce-pagination ul li a:active,' . "\n";
				$r .= '.woocommerce #content nav.woocommerce-pagination ul li a:active,' . "\n";
				$r .= '.woocommerce #content nav.woocommerce-pagination ul li a:active,' . "\n";
				$r .= '.woocommerce-page nav.woocommerce-pagination ul li a:active,' . "\n";
				$r .= '.woocommerce-page nav.woocommerce-pagination ul li a:active,' . "\n";
				$r .= '.woocommerce-page #content nav.woocommerce-pagination ul li a:active,' . "\n";
				$r .= '.woocommerce-page #content nav.woocommerce-pagination ul li a:active {' . "\n";
					$r .= 'color: ' . $theme_mod_link_color . ' !important;' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a fixed width background color or image selected by the user
			if ( symphony_is_fixed_width() && ( $symphony_fixed_width_background_css = $this->get_background_image_css( 'fixed_width' ) ) ) {
				$r .= '/* Fixed Width Background Image & Color */' . "\n";
				$r .= '.fixed-width .content-container {' . "\n";
					$r .= $symphony_fixed_width_background_css . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a fluid width background color or image selected by the user
			if ( symphony_is_fluid_width() && ( $symphony_fluid_width_background_css = $this->get_background_image_css( 'fluid_width' ) ) ) {
				$r .= '/* Fluid Width Background Image & Color */' . "\n";
				$r .= 'body.fluid-width, body.custom-background.fluid-width,' . "\n";
				$r .= 'body.fluid-width .content-container {' . "\n";
					$r .= $symphony_fluid_width_background_css . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a post title color selected by the user
			if ( ( $theme_mod_symphony_post_title_color = $this->get_theme_mod( 'symphony_post_title_color', $this->theme_mod_symphony_post_title_color() ) ) ) {
				$r .= '/* Post Title Color */' . "\n";
				$r .= '.post-title, h2.latest-post-title a, article.content h2.latest-post-title a {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_post_title_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have an archive title color selected by the user
			if ( ( $theme_mod_symphony_archive_title_color = $this->get_theme_mod( 'symphony_archive_title_color', $this->theme_mod_symphony_archive_title_color() ) ) ) {
				$r .= '/* Archive Title Color */' . "\n";
				$r .= '.archive-title {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_archive_title_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a button text color selected by the user
			if ( ( $theme_mod_symphony_button_text_color = $this->get_theme_mod( 'symphony_button_text_color', $this->theme_mod_symphony_button_text_color() ) ) ) {
				$r .= '/* Button Text Color */' . "\n";
				$r .= '.more-link, article.content .more-link, .post-edit-link, input[type=submit],' . "\n";
				$r .= '#searchform input[type="submit"], #respond input[type="submit"],' . "\n";
				$r .= '.mc-gravity .gform_footer input.button, .mc_gravity .gform_footer input.button,' . "\n";
				$r .= '.mc-newsletter .gform_footer input.button, .mc_newsletter .gform_footer input.button,' . "\n";
				$r .= '.mc-gravity_wrapper .gform_footer input.button, .mc_gravity_wrapper .gform_footer input.button,' . "\n";
				$r .= '.mc-newsletter_wrapper .gform_footer input.button, .mc_newsletter_wrapper .gform_footer input.button,' . "\n";
				$r .= '#bbp_topic_submit, .bbp-submit-wrapper .submit {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_button_text_color . ';' . "\n";
					$r .= 'border-color: ' . $theme_mod_symphony_button_text_color . ';' . "\n";
					if ( ! $this->get_theme_mod( 'symphony_button_background_color', $this->theme_mod_symphony_button_background_color() ) )
						$r .= 'background-color: ' . $this->theme_mod_symphony_button_background_color() . ';' . "\n";
				$r .= '}' . "\n\n";

				$r .= '/* Button Text Color Hover */' . "\n";
				$r .= '.more-link:hover, article.content .more-link:hover, .post-edit-link:hover, input[type=submit]:hover,' . "\n";
				$r .= '#searchform input[type="submit"]:hover, #respond input[type="submit"]:hover,' . "\n";
				$r .= '.mc-gravity .gform_footer input.button:hover, .mc_gravity .gform_footer input.button:hover,' . "\n";
				$r .= '.mc-newsletter .gform_footer input.button:hover, .mc_newsletter .gform_footer input.button:hover,' . "\n";
				$r .= '.mc-gravity_wrapper .gform_footer input.button:hover, .mc_gravity_wrapper .gform_footer input.button:hover,' . "\n";
				$r .= '.mc-newsletter_wrapper .gform_footer input.button:hover, .mc_newsletter_wrapper .gform_footer input.button:hover,' . "\n";
				$r .= '#bbp_topic_submit:hover, .bbp-submit-wrapper .submit:hover {' . "\n";
					if ( ! $this->get_theme_mod( 'symphony_button_background_color', $this->theme_mod_symphony_button_background_color() ) ) {
						$r .= 'color: ' . $this->theme_mod_symphony_button_background_color() . ';' . "\n";
						$r .= 'border-color: ' . $this->theme_mod_symphony_button_background_color() . ';' . "\n";
					}
					$r .= 'background-color: ' . $theme_mod_symphony_button_text_color . ';' . "\n";
				$r .= '}' . "\n\n";

				$r .= '/* MailChimp Gravity Background Color */' . "\n";
				$r .= '.mc-gravity .gform_heading, .mc_gravity .gform_heading, .mc-newsletter .gform_heading, .mc_newsletter .gform_heading,' . "\n";
				$r .= '.mc-gravity_wrapper .gform_heading, .mc_gravity_wrapper .gform_heading, .mc-newsletter_wrapper .gform_heading, .mc_newsletter_wrapper .gform_heading {' . "\n";
					$r .= 'background-color: ' . $theme_mod_symphony_button_text_color . ';' . "\n";
				$r .= '}' . "\n\n";

			}

			// If we have a more link button background color selected by the user
			if ( ( $theme_mod_symphony_button_background_color = $this->get_theme_mod( 'symphony_button_background_color', $this->theme_mod_symphony_button_background_color() ) ) ) {
				$r .= '/* Button Background Color */' . "\n";
				$r .= '.more-link, article.content .more-link, .post-edit-link, input[type=submit],' . "\n";
				$r .= '#searchform input[type="submit"], #respond input[type="submit"],' . "\n";
				$r .= '.mc-gravity .gform_footer input.button, .mc_gravity .gform_footer input.button,' . "\n";
				$r .= '.mc-newsletter .gform_footer input.button, .mc_newsletter .gform_footer input.button,' . "\n";
				$r .= '.mc-gravity_wrapper .gform_footer input.button, .mc_gravity_wrapper .gform_footer input.button,' . "\n";
				$r .= '.mc-newsletter_wrapper .gform_footer input.button, .mc_newsletter_wrapper .gform_footer input.button,' . "\n";
				$r .= '#bbp_topic_submit, .bbp-submit-wrapper .submit {' . "\n";
					$r .= 'background-color: ' . $theme_mod_symphony_button_background_color . ';' . "\n";
				$r .= '}' . "\n\n";

				$r .= '/* Button Background Color Hover */' . "\n";
				$r .= '.more-link:hover, article.content .more-link:hover, .post-edit-link:hover, input[type=submit]:hover,' . "\n";
				$r .= '#searchform input[type="submit"]:hover, #respond input[type="submit"]:hover,' . "\n";
				$r .= '.mc-gravity .gform_footer input.button:hover, .mc_gravity .gform_footer input.button:hover,' . "\n";
				$r .= '.mc-newsletter .gform_footer input.button:hover, .mc_newsletter .gform_footer input.button:hover,' . "\n";
				$r .= '.mc-gravity_wrapper .gform_footer input.button:hover, .mc_gravity_wrapper .gform_footer input.button:hover,' . "\n";
				$r .= '.mc-newsletter_wrapper .gform_footer input.button:hover, .mc_newsletter_wrapper .gform_footer input.button:hover,' . "\n";
				$r .= '#bbp_topic_submit:hover, .bbp-submit-wrapper .submit:hover {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_button_background_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a widget title color selected by the user
			if ( ( $theme_mod_symphony_widget_title_color = $this->get_theme_mod( 'symphony_widget_title_color', $this->theme_mod_symphony_widget_title_color() ) ) ) {
				$r .= '/* Widget Title Color */' . "\n";
				$r .= 'h3.widget-title {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_widget_title_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a widget text color selected by the user
			if ( ( $theme_mod_symphony_widget_color = $this->get_theme_mod( 'symphony_widget_color', $this->theme_mod_symphony_widget_color() ) ) ) {
				$r .= '/* Widget Text Color */' . "\n";
				$r .= '.widget {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_widget_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a widget link color selected by the user
			if ( ( $theme_mod_symphony_widget_link_color = $this->get_theme_mod( 'symphony_widget_link_color', $this->theme_mod_symphony_widget_link_color() ) ) ) {
				$r .= '/* Widget Link Color */' . "\n";
				$r .= '.widget a, footer#footer .widget a {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_widget_link_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a top header color selected by the user
			if ( ( $theme_mod_symphony_top_header_color = $this->get_theme_mod( 'symphony_top_header_color', $this->theme_mod_symphony_top_header_color() ) ) ) {
				$r .= '/* Top Color */' . "\n";
				$r .= 'nav.top-nav li a, .top-sidebar .widget.widget_nav_menu li a {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_top_header_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a top header text color selected by the user
			if ( ( $theme_mod_symphony_top_header_text_color = $this->get_theme_mod( 'symphony_top_header_text_color', $this->theme_mod_symphony_top_header_text_color() ) ) ) {
				$r .= '/* Top Header Text Color */' . "\n";
				$r .= 'nav.top-nav, aside.top-sidebar, aside.top-sidebar .widget {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_top_header_text_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a top navigation sub menu color selected by the user
			if ( ( $theme_mod_symphony_top_header_sub_menu_color = $this->get_theme_mod( 'symphony_top_header_sub_menu_color', $this->theme_mod_symphony_top_header_sub_menu_color() ) ) ) {
				$r .= '/* Top Navigation Sub Menu Color */' . "\n";
				$r .= 'nav.top-nav ul .sub-menu li a,' . "\n";
				$r .= '.top-sidebar .widget.widget_nav_menu ul .sub-menu li a {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_top_header_sub_menu_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a top navigation sub menu background color selected by the user
			if ( ( $theme_mod_symphony_top_header_sub_menu_background_color = $this->get_theme_mod( 'symphony_top_header_sub_menu_background_color', $this->theme_mod_symphony_top_header_sub_menu_background_color() ) ) ) {
				$r .= '/* Top Navigation Sub Menu Background Color */' . "\n";
				$r .= 'nav.top-nav ul .sub-menu,' . "\n";
				$r .= '.top-sidebar .widget.widget_nav_menu ul .sub-menu {' . "\n";
					$r .= 'background-color: ' . $theme_mod_symphony_top_header_sub_menu_background_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a top header background color or image selected by the user
			if ( ( $symphony_top_header_background_css = $this->get_background_image_css( 'top_header' ) ) ) {
				$r .= '/* Top Header Background Image & Color */' . "\n";
				$r .= 'nav.top-nav, aside.top-sidebar {' . "\n";
					$r .= $symphony_top_header_background_css . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a site title selected by the user
			if ( ( $theme_mod_symphony_site_title_color = $this->get_theme_mod( 'symphony_site_title_color', $this->theme_mod_symphony_site_title_color() ) ) ) {
				$r .= '/* Site Title Color */' . "\n";
				$r .= '.site-title a {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_site_title_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a tagline color selected by the user
			if ( ( $theme_mod_symphony_tagline_color = $this->get_theme_mod( 'symphony_tagline_color', $this->theme_mod_symphony_tagline_color() ) ) ) {
				$r .= '/* Tagline Color */' . "\n";
				$r .= '.slogan {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_tagline_color .';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a primary navigation sub menu color selected by the user
			if ( ( $theme_mod_symphony_primary_sub_menu_color = $this->get_theme_mod( 'symphony_primary_sub_menu_color', $this->theme_mod_symphony_primary_sub_menu_color() ) ) ) {
				$r .= '/* Primary Navigation Sub Menu Color */' . "\n";
				$r .= 'nav.primary-nav-container .primary-nav .sub-menu li a,' . "\n";
				$r .= '.primary-nav-container ul .children li a {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_primary_sub_menu_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a primary navigation sub menu background color selected by the user
			if ( ( $theme_mod_symphony_primary_sub_menu_background_color = $this->get_theme_mod( 'symphony_primary_sub_menu_background_color', $this->theme_mod_symphony_primary_sub_menu_background_color() ) ) ) {
				$r .= '/* Primary Navigation Sub Menu Background Color */' . "\n";
				$r .= 'nav.primary-nav-container .primary-nav .sub-menu,' . "\n";
				$r .= '.primary-nav-container ul .children {' . "\n";
					$r .= 'background-color: ' . $theme_mod_symphony_primary_sub_menu_background_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a header background color or image selected by the user
			if ( ( $header_background_css = $this->get_background_image_css( 'header' ) ) ) {
				$r .= '/* Header Background Image & Color */' . "\n";
				$r .= 'header#header {' . "\n";
					$r .= $header_background_css . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a secondary color selected by the user
			if ( ( $theme_mod_secondary_color = $this->get_theme_mod( 'secondary_color', $this->theme_mod_secondary_color() ) ) ) {
				$r .= '/* Secondary Color */' . "\n";
				$r .= 'nav.portfolio-nav-container li a,' . "\n";
				$r .= '.secondary-header-sidebar .widget.widget_nav_menu li a {' . "\n";
					$r .= 'color: ' .$theme_mod_secondary_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a secondary header text color selected by the user
			if ( ( $theme_mod_symphony_secondary_header_text_color = $this->get_theme_mod( 'symphony_secondary_header_text_color', $this->theme_mod_symphony_secondary_header_text_color() ) ) ) {
				$r .= '/* Secondary Header Text Color */' . "\n";
				$r .= 'nav.portfolio-nav-container, aside.secondary-header-sidebar, aside.secondary-header-sidebar .widget {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_secondary_header_text_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a secondary navigation sub menu color selected by the user
			if ( ( $theme_mod_symphony_secondary_header_sub_menu_color = $this->get_theme_mod( 'symphony_secondary_header_sub_menu_color', $this->theme_mod_symphony_secondary_header_sub_menu_color() ) ) ) {
				$r .= '/* Secondary Navigation Sub Menu Color */' . "\n";
				$r .= 'nav.portfolio-nav-container ul .sub-menu li a,' . "\n";
				$r .= 'nav.portfolio-nav-container ul .children li a,' . "\n";
				$r .= '.secondary-header-sidebar .widget.widget_nav_menu ul .sub-menu li a {' . "\n";
					$r .= 'color: ' . $theme_mod_symphony_secondary_header_sub_menu_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a secondary navigation sub menu background color selected by the user
			if ( ( $theme_mod_symphony_secondary_header_sub_menu_background_color = $this->get_theme_mod( 'symphony_secondary_header_sub_menu_background_color', $this->theme_mod_symphony_secondary_header_sub_menu_background_color() ) ) ) {
				$r .= '/* SecondaryNavigation Sub Menu Background Color */' . "\n";
				$r .= 'nav.portfolio-nav-container ul .sub-menu,' . "\n";
				$r .= 'nav.portfolio-nav-container ul .children,' . "\n";
				$r .= '.secondary-header-sidebar .widget.widget_nav_menu ul .sub-menu {' . "\n";
					$r .= 'background-color: ' . $theme_mod_symphony_secondary_header_sub_menu_background_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a secondary header background color or image selected by the user
			if ( ( $symphony_secondary_header_background_css = $this->get_background_image_css( 'secondary_header' ) ) ) {
				$r .= '/* Secondary Header Background Image & Color */' . "\n";
				$r .= 'nav.portfolio-nav-container, aside.secondary-header-sidebar {' . "\n";
				$r .= $symphony_secondary_header_background_css . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a footer text color selected by the user
			if ( ( $theme_mod_symphony_footer_text_color = $this->get_theme_mod( 'symphony_footer_text_color', $this->theme_mod_symphony_footer_text_color() ) ) ) {
				$r .= '/* Footer Text Color */' . "\n";
				$r .= 'footer#footer, p.copyright-message {' . "\n";
				$r .= 'color: ' . $theme_mod_symphony_footer_text_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a footer link color selected by the user
			if ( ( $theme_mod_symphony_footer_link_color = $this->get_theme_mod( 'symphony_footer_link_color', $this->theme_mod_symphony_footer_link_color() ) ) ) {
				$r .= '/* Footer Link Color */' . "\n";
				$r .= 'footer#footer a, .copyright-message a {' . "\n";
				$r .= 'color: ' . $theme_mod_symphony_footer_link_color . ';' . "\n";
				$r .= '}' . "\n\n";
			}

			// If we have a header background color or image selected by the user
			if ( ( $footer_background_css = $this->get_background_image_css( 'footer' ) ) ) {
				$r .= '/* Footer Background Image & Color */' . "\n";
				$r .= 'footer#footer {' . "\n";
					$r .= $footer_background_css . "\n";
				$r .= '}' . "\n\n";
			}

			// Close </style>
			$r .= '</style>';

			return $r;
		}
	}

	/**
	 * This function outputs CSS for Customizer settings.
	 */
	function wp_head() {
		// Get Customizer CSS
		echo $this->get_customizer_css();
	}


	/**********************
	 * Internal Functions *
	 **********************/

	/**
	 * This function returns a boolean result comparing WordPress versions.
	 *
	 * @return Boolean
	 */
	public function version_compare( $version, $operator = '>=' ) {
		global $wp_version;

		return version_compare( $wp_version, $version, $operator );
	}

	/**
	 * This function returns a list of featured image size choices formatted for a Customizer
	 * "select" option.
	 */
	function get_featured_image_size_choices() {
		$r = array(); // Return values

		// Get all available image sizes and their dimensions
		$avail_image_sizes = $this->get_available_image_sizes();
		$default_featured_image_size = apply_filters( 'sds_theme_options_default_featured_image_size', '' );

		foreach ( $avail_image_sizes as $size => $atts )
			$r[$size] = ( $size === $default_featured_image_size ) ? $size . ' ' . implode( 'x', $atts ) .' (Default)' : $size . ' ' . implode( 'x', $atts );

		return $r;
	}

	/**
	 * This function returns a theme mod but first checks to see if it is the default, and if so
	 * no value is returned. This is to prevent unnecessary CSS output in wp_head().
	 */
	function get_theme_mod( $theme_mod_name, $default = false ) {
		$theme_mod = get_theme_mod( $theme_mod_name );

		// Check this theme mod against the default
		if ( $theme_mod === $default )
			$theme_mod = false;

		return $theme_mod;
	}

	/**
	 * This function returns background image CSS properties based on the theme mod parameter.
	 *
	 * Copyright: WordPress Core (3.0), http://wordpress.org/
	 *
	 * We've used WordPress' function as a base and modified it to suit our needs.
	 */
	function get_background_image_css( $theme_mod_area = 'default' ) {
		// Just get the default background CSS
		if ( $theme_mod_area === 'default' ) {
			// $background is the saved custom image, or the default image.
			$background = set_url_scheme( get_background_image() );

			// $color is the saved custom color.
			// A default has to be specified in style.css. It will not be printed here.
			$color = '#' . get_background_color();

			if ( $color === get_theme_support( 'custom-background', 'default-color' ) )
				$color = false;

			if ( ! $background && ! $color )
				return;

			$style = $color ? "background-color: #$color;" : '';

			if ( $background ) {
				$image = " background-image: url('$background');";

				$repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
				if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
					$repeat = 'repeat';
				$repeat = " background-repeat: $repeat;";

				$position = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
				if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
					$position = 'left';
				$position = " background-position: top $position;";

				$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
				if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
					$attachment = 'scroll';
				$attachment = " background-attachment: $attachment;";

				$style .= $image . $repeat . $position . $attachment;
			}

			return $style;
		}
		// Otherwise get the theme mod area background CSS
		else {
			// $background is the saved custom image, or the default image.
			$background = set_url_scheme( get_theme_mod( 'symphony_' . $theme_mod_area . '_background_image' ) );

			// $color is the saved custom color.
			$theme_mod_filter_function = 'theme_mod_symphony_' . $theme_mod_area . '_background_color';
			$color = $this->get_theme_mod( 'symphony_' . $theme_mod_area . '_background_color', $this->$theme_mod_filter_function() );

			if ( ! $background && ! $color )
				return;

			$style = $color ? "background-color: $color;" : '';

			if ( $background ) {
				$image = " background-image: url('$background');";

				$repeat = get_theme_mod( 'symphony_' . $theme_mod_area . '_background_image_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
				if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
					$repeat = 'repeat';
				$repeat = " background-repeat: $repeat;";

				$position = get_theme_mod( 'symphony_' . $theme_mod_area . '_background_image_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
				if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
					$position = 'left';
				$position = " background-position: top $position;";

				$attachment = get_theme_mod( 'symphony_' . $theme_mod_area . '_background_image_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
				if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
					$attachment = 'scroll';
				$attachment = " background-attachment: $attachment;";

				$style .= $image . $repeat . $position . $attachment;
			}

			return $style;
		}
	}

	/**
	 * This function determines if Symphony is fixed width and has a fixed width background image. It is
	 * used as an 'active_callback' to prevent certain controls from displaying on the "ready"
	 * event from the Previewer to the Customizer.
	 */
	function symphony_is_symphony_fixed_width_background_image( $control ) {
		// Check for fixed width and background image
		return symphony_is_fixed_width() && get_theme_mod( 'symphony_fixed_width_background_image', false );
	}

	/**
	 * This function determines if Symphony is fluid width and has a fluid width background image. It is
	 * used as an 'active_callback' to prevent certain controls from displaying on the "ready"
	 * event from the Previewer to the Customizer.
	 */
	function symphony_is_symphony_fluid_width_background_image( $control ) {
		// Check for fixed width and background image
		return symphony_is_fluid_width() && get_theme_mod( 'symphony_fluid_width_background_image', false );
	}

	/**
	 * This function resets transient data to ensure front-end matches Customizer preview.
	 */
	function reset_transient() {
		// Reset transient data on this class
		$this->transient_data = array();

		// Delete the transient data
		$this->delete_transient();

		// Set the transient data
		$this->set_transient();
	}



	/**
	 * This function gets our transient data. Additionally it calls the set_transient()
	 * method on this class to set and return transient data if the transient data doesn't
	 * currently exist.
	 */
	function get_transient() {
		// Check for transient data first
		if ( ! $transient_data = get_transient( $this->transient_name ) )
			// Create and return the transient data if it doesn't exist
			$transient_data = $this->set_transient();

		return $transient_data;
	}

	/**
	 * This function stores data in our transient and returns the data.
	 */
	function set_transient() {
		$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance

		$data = array(); // Default

		// Always add the Customizer CSS
		$data['customizer_css'] = $this->get_customizer_css();

		// Always add the theme's version
		$data['version'] = $symphony_theme_helper->theme->get( 'Version' );

		// Set the transient
		set_transient( $this->transient_name, $data );

		return $data;
	}

	/**
	 * This function deletes our transient data.
	 */
	function delete_transient() {
		// Delete the transient
		delete_transient( $this->transient_name );
	}

	/**
	 * This function determines if the site is currently being previewed in the Customizer.
	 */
	public function is_customize_preview() {
		$symphony_customizer = Symphony_Customizer_Instance();
		$is_wp_4 = $symphony_customizer->version_compare( '4.0' );

		// Less than 4.0
		if ( ! $is_wp_4 ) {
			global $wp_customize;

			return is_a( $wp_customize, 'WP_Customize_Manager' ) && $wp_customize->is_preview();
		}
		// 4.0 or greater
		else
			return is_customize_preview();
	}

	/**
	 * This function returns an array of available image sizes with attributes.
	 */
	public function get_available_image_sizes() {
		global $_wp_additional_image_sizes;

		$avail_image_sizes = array();
		foreach( get_intermediate_image_sizes() as $size ) {
			$avail_image_sizes[ $size ] = array( 0, 0 );

			if( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$avail_image_sizes[ $size ][0] = get_option( $size . '_size_w' );
				$avail_image_sizes[ $size ][1] = get_option( $size . '_size_h' );
			}
			else if ( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $size ] ) )
				$avail_image_sizes[ $size ] = array( $_wp_additional_image_sizes[ $size ]['width'], $_wp_additional_image_sizes[ $size ]['height'] );
		}

		return $avail_image_sizes;
	}

	/**
	 * This function sanitizes the Symphony Jetpack Portfolio Customizer setting.
	 */
	public function sanitize_symphony_jetpack_portfolio( $post_type ) {
		// Portfolio Post Type
		$post_type = sanitize_text_field( $post_type );

		// Pubic post types with archives
		$public_post_types = get_post_types( array(
			'public' => true,
			'has_archive' => true
		) );

		// Verify that this is a valid post type
		if ( ! in_array( $post_type, $public_post_types ) )
			$post_type = false;

		return $post_type;
	}
}


function Symphony_Customizer_Instance() {
	return Symphony_Customizer::instance();
}

// Starts Symphony
Symphony_Customizer_Instance();