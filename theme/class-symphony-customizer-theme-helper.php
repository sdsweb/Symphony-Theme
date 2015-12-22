<?php
/**
* Symphony Theme Helper (A helper class to determine information about the current theme)
*/

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;


final class Symphony_Theme_Helper {
	/**
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * @var Symphony_Theme_Helper, Instance of the class
	 */
	protected static $_instance;

	/**
	 * @var WP_Theme
	 */
	public $theme = false;

	/**
	 * @var WP_Theme
	 */
	public $child_theme = false;

	/**
	 * @var string, Slug for Slocum Theme support
	 */
	public $theme_support_slug = 'slocum-theme';

	/**
	 * @var array, Array of Slocum Theme support
	 */
	public $theme_support = false;

	/**
	 * Function used to create instance of class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();

		return self::$_instance;
	}


	/**
	 * This function sets up all of the actions and filters on instance. It also loads (includes)
	 * the required files and assets.
	 */
	function __construct( ) {
		// Hooks
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ), 9999 ); // After Switch Theme (late)
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 999 ); // After Setup Theme (late; but before Symphony_Customizer_Typography class)
		add_filter( 'current_theme_supports-slocum-theme', array( $this, 'current_theme_supports_slocum_theme' ), 10, 3 ); // Modify checks for theme support
	}

	/**
	 * This function adds theme mods from the Symphony parent theme to a child theme if
	 * one is active.
	 */
	function after_switch_theme() {
		// If a child theme is active
		if ( $this->child_theme )
			// Get Symphony theme mods
			if ( $symphony_theme_mods = $this->get_theme_mods( $this->theme->get_template() ) )
				// Store theme mods on the child theme
				$this->set_theme_mods( $symphony_theme_mods );
	}

	/**
	 * This function initiates the calls to get the details of the current theme.
	 */
	function after_setup_theme() {
		$this->theme = $this->get_parent_theme(); // Get the [parent] theme
		$this->child_theme = $this->get_child_theme(); // Get the child theme
		$this->theme_support = get_theme_support( $this->theme_support_slug ); // Get Slocum Theme theme support

		// If theme support is an array, remove the 0 index
		if ( is_array( $this->theme_support ) )
			$this->theme_support = $this->theme_support[0];

		/*
		 * Filters for theme mods
		 */

		// Site Title Font Size
		add_filter( 'theme_mod_symphony_site_title_font_size', array( $this, 'theme_mod_symphony_site_title_font_size' ), 10, 2 ); // Set the default font size
		add_filter( 'theme_mod_symphony_site_title_font_size_min', array( $this, 'theme_mod_symphony_site_title_font_size_min' ), 10, 2 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_site_title_font_size_max', array( $this, 'theme_mod_symphony_site_title_font_size_max' ), 10, 2 ); // Set the default font size max attribute value

		// Site Title Font Family
		add_filter( 'theme_mod_symphony_site_title_font_family', array( $this, 'theme_mod_symphony_site_title_font_family' ), 10, 2 ); // Set the default font family

		// Site Tagline Font Size
		add_filter( 'theme_mod_symphony_tagline_font_size', array( $this, 'theme_mod_symphony_tagline_font_size' ), 10, 2 ); // Set the default font size
		add_filter( 'theme_mod_symphony_tagline_font_size_min', array( $this, 'theme_mod_symphony_tagline_font_size_min' ), 10, 2 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_tagline_font_size_max', array( $this, 'theme_mod_symphony_tagline_font_size_max' ), 10, 2 ); // Set the default font size max attribute value

		// Tagline Font Family
		add_filter( 'theme_mod_symphony_tagline_font_family', array( $this, 'theme_mod_symphony_tagline_font_family' ), 10, 2 ); // Set the default font family

		// Navigation Font Size
		add_filter( 'theme_mod_symphony_navigation_font_size', array( $this, 'theme_mod_symphony_navigation_font_size' ), 10, 3 ); // Set the default font size
		add_filter( 'theme_mod_symphony_navigation_font_size_min', array( $this, 'theme_mod_symphony_navigation_font_size_min' ), 10, 3 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_navigation_font_size_max', array( $this, 'theme_mod_symphony_navigation_font_size_max' ), 10, 3 ); // Set the default font size max attribute value

		// Navigation Font Family
		add_filter( 'theme_mod_symphony_navigation_font_family', array( $this, 'theme_mod_symphony_navigation_font_family' ), 10, 3 ); // Set the default font family

		// Heading Font Sizes
		add_filter( 'theme_mod_symphony_headings_font_size', array( $this, 'theme_mod_symphony_headings_font_size' ), 10, 2 ); // Set the default font size
		add_filter( 'theme_mod_symphony_headings_font_size_min', array( $this, 'theme_mod_symphony_headings_font_size_min' ), 10, 2 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_headings_font_size_max', array( $this, 'theme_mod_symphony_headings_font_size_max' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h1_font_size', array( $this, 'theme_mod_symphony_h1_font_size' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h1_font_size_min', array( $this, 'theme_mod_symphony_h1_font_size_min' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h1_font_size_max', array( $this, 'theme_mod_symphony_h1_font_size_max' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h2_font_size', array( $this, 'theme_mod_symphony_h2_font_size' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h2_font_size_min', array( $this, 'theme_mod_symphony_h2_font_size_min' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h2_font_size_max', array( $this, 'theme_mod_symphony_h2_font_size_max' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h3_font_size', array( $this, 'theme_mod_symphony_h3_font_size' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h3_font_size_min', array( $this, 'theme_mod_symphony_h3_font_size_min' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h3_font_size_max', array( $this, 'theme_mod_symphony_h3_font_size_max' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h4_font_size', array( $this, 'theme_mod_symphony_h4_font_size' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h4_font_size_min', array( $this, 'theme_mod_symphony_h4_font_size_min' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h4_font_size_max', array( $this, 'theme_mod_symphony_h4_font_size_max' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h5_font_size', array( $this, 'theme_mod_symphony_h5_font_size' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h5_font_size_min', array( $this, 'theme_mod_symphony_h5_font_size_min' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h5_font_size_max', array( $this, 'theme_mod_symphony_h5_font_size_max' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h6_font_size', array( $this, 'theme_mod_symphony_h6_font_size' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h6_font_size_min', array( $this, 'theme_mod_symphony_h6_font_size_min' ), 10, 2 ); // Set the default font size max attribute value
		add_filter( 'theme_mod_symphony_h6_font_size_max', array( $this, 'theme_mod_symphony_h6_font_size_max' ), 10, 2 ); // Set the default font size max attribute value

		// Heading Font Families
		add_filter( 'theme_mod_symphony_headings_font_family', array( $this, 'theme_mod_symphony_headings_font_family' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h1_font_family', array( $this, 'theme_mod_symphony_h1_font_family' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h2_font_family', array( $this, 'theme_mod_symphony_h2_font_family' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h3_font_family', array( $this, 'theme_mod_symphony_h3_font_family' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h4_font_family', array( $this, 'theme_mod_symphony_h4_font_family' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h5_font_family', array( $this, 'theme_mod_symphony_h5_font_family' ), 10, 2 ); // Set the default font family
		add_filter( 'theme_mod_symphony_h6_font_family', array( $this, 'theme_mod_symphony_h6_font_family' ), 10, 2 ); // Set the default font family

		// Body (content) Font Size
		add_filter( 'theme_mod_symphony_body_font_size', array( $this, 'theme_mod_symphony_body_font_size' ), 10, 2 ); // Set the default font size
		add_filter( 'theme_mod_symphony_body_font_size_min', array( $this, 'theme_mod_symphony_body_font_size_min' ), 10, 2 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_body_font_size_max', array( $this, 'theme_mod_symphony_body_font_size_max' ), 10, 2 ); // Set the default font size max attribute value

		// Body (content) Line Height
		add_filter( 'theme_mod_symphony_body_line_height', array( $this, 'theme_mod_symphony_body_line_height' ), 10, 2 ); // Set the default font size
		add_filter( 'theme_mod_symphony_body_line_height_min', array( $this, 'theme_mod_symphony_body_line_height_min' ), 10, 2 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_body_line_height_max', array( $this, 'theme_mod_symphony_body_line_height_max' ), 10, 2 ); // Set the default font size max attribute value

		// Body (content) Font Family
		add_filter( 'theme_mod_symphony_body_font_family', array( $this, 'theme_mod_symphony_body_font_family' ), 10, 2 ); // Set the default font family

		// Widget Title Font Size
		add_filter( 'theme_mod_symphony_widget_title_font_size', array( $this, 'theme_mod_symphony_widget_title_font_size' ), 10, 2 ); // Set the default font size
		add_filter( 'theme_mod_symphony_widget_title_font_size_min', array( $this, 'theme_mod_symphony_widget_title_font_size_min' ), 10, 2 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_widget_title_font_size_max', array( $this, 'theme_mod_symphony_widget_title_font_size_max' ), 10, 2 ); // Set the default font size max attribute value

		// Widget Title Font Family
		add_filter( 'theme_mod_symphony_widget_title_font_family', array( $this, 'theme_mod_symphony_widget_title_font_family' ), 10, 2 ); // Set the default font family

		// Widget Font Size
		add_filter( 'theme_mod_symphony_widget_font_size', array( $this, 'theme_mod_symphony_widget_font_size' ), 10, 2 ); // Set the default font size
		add_filter( 'theme_mod_symphony_widget_font_size_min', array( $this, 'theme_mod_symphony_widget_font_size_min' ), 10, 2 ); // Set the default font size min attribute value
		add_filter( 'theme_mod_symphony_widget_font_size_max', array( $this, 'theme_mod_symphony_widget_font_size_max' ), 10, 2 ); // Set the default font size max attribute value

		// Widget Font Family
		add_filter( 'theme_mod_symphony_widget_font_family', array( $this, 'theme_mod_symphony_widget_font_family' ), 10, 2 ); // Set the default font family
	}

	/**
	 * This function adds checks to current_theme_supports() for Slocum Theme support.
	 */
	function current_theme_supports_slocum_theme( $supports, $args, $theme_support ) {
		$theme_support = $theme_support[0];
		$feature = $args[0];
		$name = $args[1];
		$property = $args[2]; // Can be passed as an array of keys to check
		$key = $args[3]; // Can be passed as an array of keys to check

		// First determine if we have a valid $key
		if ( $key ) {
			if ( is_array( $key ) ) {
				if ( isset( $theme_support[$feature] ) && isset( $theme_support[$feature][$name] ) && isset( $theme_support[$feature][$name][$property] ) )
					// Loop through keys in this property
					foreach( $theme_support[$feature][$name][$property] as $the_key => $value )
						// If one exists, support exists
						if ( in_array( $the_key, $key ) )
							return true;
			}
			else
				if ( isset( $theme_support[$feature] ) && isset( $theme_support[$feature][$name] ) && isset( $theme_support[$feature][$name][$property] ) && isset( $theme_support[$feature][$name][$property][$key] ) )
					return true;
		}

		// Then determine if we have a $property
		if ( ! $key && $property ) {
			if ( is_array( $property ) ) {
				if ( isset( $theme_support[$feature] ) && isset( $theme_support[$feature][$name] ) )
					// Loop through keys in this name
					foreach( $theme_support[$feature][$name] as $the_key => $value )
						// If one exists, support exists
						if ( in_array( $the_key, $property ) )
							return true;
				}
				else
					if ( isset( $theme_support[$feature] ) && isset( $theme_support[$feature][$name] ) && isset( $theme_support[$feature][$name][$property] ) )
						return true;
		}

		// Next determine if we have a $name
		if ( ! $key && ! $property && $name && isset( $theme_support[$feature] ) && isset( $theme_support[$feature][$name] ) )
			return true;

		// Finally we have to at least have a $feature
		if ( ! $key && ! $property && ! $name && isset( $theme_support[$feature] ) )
			return true;

		// TODO: Check for specific theme support and return data accordingly

		// No support (default)
		return false;
	}

	/**
	 * This function returns the details of the current [parent] theme.
	 */
	public function get_parent_theme() {
		// Return the cached version of the theme
		if ( is_a( $this->theme, 'WP_Theme' ) )
			return $this->theme;

		$wp_get_theme = wp_get_theme();

		return ( is_child_theme() ) ? $wp_get_theme->parent() : $wp_get_theme ;
	}

	/**
	 * This function returns the details of the current child theme (if any).
	 */
	public function get_child_theme() {
		// Return the cached version of the theme or null (if not a child theme)
		if ( is_a( $this->child_theme, 'WP_Theme' ) || $this->child_theme === null )
			return $this->child_theme;

		return ( is_child_theme() ) ? wp_get_theme() : null;
	}

	/**
	 * This function returns support for certain Slocum Theme features within the current theme.
	 */
	public function current_theme_supports( $feature, $name = '', $property = '', $key = '' ) {
		return current_theme_supports( $this->theme_support_slug, $feature, $name, $property, $key );
	}

	/**
	 * This function looks through theme mods to determine if a value exists based on parameters.
	 */
	function get_theme_mod_value( $value, $default, $feature = '', $name = '', $property = '', $key = '' ) {
		// Return the current font size if no default parameter is set or if set by another source
		if ( ! isset( $default ) || ( $value && $value !== $default ) || empty( $this->theme_support ) )
			return $value;

		// Determine if we have a $key
		if ( $key && ( $value = $this->get_theme_support_value( $feature, $name, $property, $key ) ) )
			return $value;

		// Next determine if we have a $property
		if ( ! $key && $property && ( $value = $this->get_theme_support_value( $feature, $name, $property ) ) )
			return $value;

		// Next determine if we have a $name
		if ( ! $key && ! $property && $name && ( $value = $this->get_theme_support_value( $feature, $name ) ) )
			return $value;

		// Finally we have to at least have a $feature
		if ( ! $key && ! $property && ! $name && ( $value = $this->get_theme_support_value( $feature ) ) )
			return $value;

		// Return default if we don't have another value specified at this point
		return $default;
	}

	/**
	 * This function looks through theme mod support and fetches to determine if a value exists based on parameters.
	 */
	// TODO: Might need a way to get the default value from the customizer settings if possible (this function only returns data currently if the theme specifies support)
	function get_theme_support_value( $feature, $name = '', $property = '', $key = '' ) {
		// Determine if we have a $key
		if ( $key && $this->current_theme_supports( $feature, $name, $property ) && isset( $this->theme_support[$feature][$name][$property][$key] ) )
			return $this->theme_support[$feature][$name][$property][$key];

		// Next determine if we have a $property
		if ( ! $key && $property && $this->current_theme_supports( $feature, $name, $property ) )
			return $this->theme_support[$feature][$name][$property];

		// Next determine if we have a $name
		if ( ! $key && ! $property && $name && $this->current_theme_supports( $feature, $name ) )
			return $this->theme_support[$feature][$name];

		// Finally we have to at least have a $feature
		if ( ! $key && ! $property && ! $name && $this->current_theme_supports( $feature ) )
			return $this->theme_support[$feature];

		// Return false if we don't have another value specified at this point
		return false;
	}

	/**
	 * This function determines if the current theme supports any Google Web Fonts
	 */
	public function has_google_web_font_support() {
		$r = false; // Default

		// Individual Navigation Support
		$individual_navigation_support = false;
		$navigation_support = ( $this->has_individual_navigation_support() ) ? $this->get_individual_navigation_support() : array();

		if ( ! empty( $navigation_support ) )
			foreach( $navigation_support as $support )
				if ( isset( $support['support']['font_family'] ) ) {
					$individual_navigation_support = true;
					break;
				}

		// Individual heading Support
		$individual_heading_support = false;
		$heading_support = ( $this->current_theme_supports( 'fonts', 'headings' ) ) ? $this->get_theme_support_value( 'fonts', 'headings' ) : array();

		if ( ! empty( $heading_support ) )
			foreach( $heading_support as $support )
				if ( isset( $support['font_family'] ) ) {
					$individual_heading_support = true;
					break;
				}

		// Checking Site Title, Tagline, Navigation, Headings, Body (content)
		$r = $this->current_theme_supports( 'fonts', 'site_title', 'font_family' ) ||
			$this->current_theme_supports( 'fonts', 'tagline', 'font_family' ) ||
			$this->current_theme_supports( 'fonts', 'navigation', 'font_family' ) ||
			$individual_navigation_support ||
			$this->current_theme_supports( 'fonts', 'headings', 'font_family' ) ||
			$individual_heading_support ||
			$this->current_theme_supports( 'fonts', 'body', 'font_family' );

		$r = apply_filters( 'symphony_has_google_web_font_support', $r );

		return $r;
	}

	/**
	 * This function determines if the current theme supports individual navigation menus.
	 */
	public function has_individual_navigation_support() {
		$navigation_support = $this->get_theme_support_value( 'fonts', 'navigation' );

		// No navigation theme support
		if ( ! $navigation_support )
			return false;

		// If the font size or font family keys do not exist, we'll mark that we have support
		if ( ! array_key_exists( 'font_size', $navigation_support ) || ! array_key_exists( 'font_family', $navigation_support ) )
			return true;
		// Otherwise this theme doesn't support individual navigation menus
		else
			return false;
	}

	/**
	 * This function gets the individual navigation menu support from a theme and returns
	 * an array of data.
	 */
	function get_individual_navigation_support() {
		$r = array();
		$navigation_support = $this->get_theme_support_value( 'fonts', 'navigation' );
		$registered_nav_menus = get_registered_nav_menus();

		// If we have registered nav menus
		if ( ! empty( $registered_nav_menus ) )
			// Loop through them
			foreach ( $registered_nav_menus as $nav_menu_id => $nav_menu_label )
				// Theme Support for this navigation menu exists
				if ( array_key_exists( $nav_menu_id, $navigation_support ) )
					$r[$nav_menu_id] = array(
						'label' => $nav_menu_label,
						'support' => $navigation_support[$nav_menu_id]
					) ;

		return $r;
	}


	/**************
	 * Theme Mods *
	 **************/

	/**
	 * Site Title
	 */

	/**
	 * This function sets the default font size for the site title in the Customizer.
	 */
	function theme_mod_symphony_site_title_font_size( $size, $default = false ) {
		return $this->get_theme_mod_value( $size, $default, 'fonts', 'site_title', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute value for the site title in the Customizer.
	 */
	function theme_mod_symphony_site_title_font_size_min( $min, $default = false ) {
		return $this->get_theme_mod_value( $min, $default, 'fonts', 'site_title', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute value for the site title in the Customizer.
	 */
	function theme_mod_symphony_site_title_font_size_max( $max, $default = false ) {
		return $this->get_theme_mod_value( $max, $default, 'fonts', 'site_title', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font family for the site title in the Customizer.
	 */
	function theme_mod_symphony_site_title_font_family( $family, $default = false ) {
		return $this->get_theme_mod_value( $family, $default, 'fonts', 'site_title', 'font_family', 'default' );
	}

	/**
	 * Site Tagline
	 */

	/**
	 * This function sets the default font size for the tagline in the Customizer.
	 */
	function theme_mod_symphony_tagline_font_size( $size, $default = false ) {
		return $this->get_theme_mod_value( $size, $default, 'fonts', 'tagline', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute value for the tagline in the Customizer.
	 */
	function theme_mod_symphony_tagline_font_size_min( $min, $default = false ) {
		return $this->get_theme_mod_value( $min, $default, 'fonts', 'tagline', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute value for the tagline in the Customizer.
	 */
	function theme_mod_symphony_tagline_font_size_max( $max, $default = false ) {
		return $this->get_theme_mod_value( $max, $default, 'fonts', 'tagline', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font family for the tagline in the Customizer.
	 */
	function theme_mod_symphony_tagline_font_family( $family, $default = false ) {
		return $this->get_theme_mod_value( $family, $default, 'fonts', 'tagline', 'font_family', 'default' );
	}

	/**
	 * Navigation
	 */

	/**
	 * This function sets the default font size for the navigation in the Customizer.
	 */
	function theme_mod_symphony_navigation_font_size( $size, $default = false, $nav_menu_id = false ) {
		// Global
		if ( ! isset( $nav_menu_id ) || ! $nav_menu_id )
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'navigation', 'font_size', 'default' );
		// Individual
		else {
			// Get theme support
			$navigation_support = $this->get_theme_mod_value( $size, $default, 'fonts', 'navigation', $nav_menu_id, 'font_size' );

			// Return default value
			return ( isset( $navigation_support['default'] ) ) ? $navigation_support['default'] : $size;
		}
	}

	/**
	 * This function sets the default font size min attribute value for the navigation in the Customizer.
	 */
	function theme_mod_symphony_navigation_font_size_min( $min, $default = false, $nav_menu_id = false ) {
		// Global
		if ( ! isset( $nav_menu_id ) || ! $nav_menu_id )
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'navigation', 'font_size', 'min' );
		// Individual
		else {
			// Get theme support
			$navigation_support = $this->get_theme_mod_value( $min, $default, 'fonts', 'navigation', $nav_menu_id, 'font_size' );

			// Return min value
			return ( isset( $navigation_support['min'] ) ) ? $navigation_support['min'] : $min;
		}
	}

	/**
	 * This function sets the default font size max attribute value for the navigation in the Customizer.
	 */
	function theme_mod_symphony_navigation_font_size_max( $max, $default = false, $nav_menu_id = false ) {
		// Global
		if ( ! isset( $nav_menu_id ) || ! $nav_menu_id )
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'navigation', 'font_size', 'default' );
		// Individual
		else {
			// Get theme support
			$navigation_support = $this->get_theme_mod_value( $max, $default, 'fonts', 'navigation', $nav_menu_id, 'font_size' );

			// Return min value
			return ( isset( $navigation_support['max'] ) ) ? $navigation_support['max'] : $max;
		}
	}

	/**
	 * This function sets the default font family for the navigation in the Customizer.
	 */
	function theme_mod_symphony_navigation_font_family( $family, $default = false, $nav_menu_id = false ) {
		// Global
		if ( ! isset( $nav_menu_id ) || ! $nav_menu_id )
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'navigation', 'font_family', 'default' );
		// Individual
		else {
			// Get theme support
			$navigation_support = $this->get_theme_mod_value( $family, $default, 'fonts', 'navigation', $nav_menu_id, 'font_family' );

			// Return default value
			return ( isset( $navigation_support['default'] ) ) ? $navigation_support['default'] : $family;
		}
	}

	/**
	 * Headings
	 */

	/**
	 * This function sets the default font size for the headings in the Customizer.
	 */
	function theme_mod_symphony_headings_font_size( $size, $default = false ) {
		return $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute value for the headings in the Customizer.
	 */
	function theme_mod_symphony_headings_font_size_min( $min, $default = false ) {
		return $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute value for the headings in the Customizer.
	 */
	function theme_mod_symphony_headings_font_size_max( $max, $default = false ) {
		return $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font size for Heading 1 in the Customizer.
	 */
	function theme_mod_symphony_h1_font_size( $size, $default = false ) {
		$font_size = $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'h1', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['default'] ) )
			return $font_size['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute for Heading 1 in the Customizer.
	 */
	function theme_mod_symphony_h1_font_size_min( $min, $default = false ) {
		$font_size = $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'h1', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['min'] ) )
			return $font_size['min'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute for Heading 1 in the Customizer.
	 */
	function theme_mod_symphony_h1_font_size_max( $max, $default = false ) {
		$font_size = $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'h1', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['max'] ) )
			return $font_size['max'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font size for Heading 2 in the Customizer.
	 */
	function theme_mod_symphony_h2_font_size( $size, $default = false ) {
		$font_size = $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'h2', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['default'] ) )
			return $font_size['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute for Heading 2 in the Customizer.
	 */
	function theme_mod_symphony_h2_font_size_min( $min, $default = false ) {
		$font_size = $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'h2', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['min'] ) )
			return $font_size['min'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute for Heading 2 in the Customizer.
	 */
	function theme_mod_symphony_h2_font_size_max( $max, $default = false ) {
		$font_size = $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'h2', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['max'] ) )
			return $font_size['max'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font size for Heading 3 in the Customizer.
	 */
	function theme_mod_symphony_h3_font_size( $size, $default = false ) {
		$font_size = $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'h3', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['default'] ) )
			return $font_size['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute for Heading 3 in the Customizer.
	 */
	function theme_mod_symphony_h3_font_size_min( $min, $default = false ) {
		$font_size = $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'h3', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['min'] ) )
			return $font_size['min'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute for Heading 3 in the Customizer.
	 */
	function theme_mod_symphony_h3_font_size_max( $max, $default = false ) {
		$font_size = $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'h3', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['max'] ) )
			return $font_size['max'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font size for Heading 4 in the Customizer.
	 */
	function theme_mod_symphony_h4_font_size( $size, $default = false ) {
		$font_size = $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'h4', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['default'] ) )
			return $font_size['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute for Heading 4 in the Customizer.
	 */
	function theme_mod_symphony_h4_font_size_min( $min, $default = false ) {
		$font_size = $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'h4', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['min'] ) )
			return $font_size['min'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute for Heading 4 in the Customizer.
	 */
	function theme_mod_symphony_h4_font_size_max( $max, $default = false ) {
		$font_size = $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'h4', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['max'] ) )
			return $font_size['max'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font size for Heading 5 in the Customizer.
	 */
	function theme_mod_symphony_h5_font_size( $size, $default = false ) {
		$font_size = $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'h5', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['default'] ) )
			return $font_size['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute for Heading 5 in the Customizer.
	 */
	function theme_mod_symphony_h5_font_size_min( $min, $default = false ) {
		$font_size = $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'h5', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['min'] ) )
			return $font_size['min'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute for Heading 5 in the Customizer.
	 */
	function theme_mod_symphony_h5_font_size_max( $max, $default = false ) {
		$font_size = $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'h5', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['max'] ) )
			return $font_size['max'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font size for Heading 1 in the Customizer.
	 */
	function theme_mod_symphony_h6_font_size( $size, $default = false ) {
		$font_size = $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'h6', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['default'] ) )
			return $font_size['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'headings', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute for Heading 6 in the Customizer.
	 */
	function theme_mod_symphony_h6_font_size_min( $min, $default = false ) {
		$font_size = $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'h6', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['min'] ) )
			return $font_size['min'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'headings', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute for Heading 6 in the Customizer.
	 */
	function theme_mod_symphony_h6_font_size_max( $max, $default = false ) {
		$font_size = $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'h6', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['max'] ) )
			return $font_size['max'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'headings', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font family for the site title in the Customizer.
	 */
	function theme_mod_symphony_headings_font_family( $family, $default = false ) {
		return $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'font_family', 'default' );
	}

	/**
	 * This function sets the default font family for Heading 1 in the Customizer.
	 */
	function theme_mod_symphony_h1_font_family( $family, $default = false ) {
		$font_family = $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'h1', 'font_family' );

		// Individual
		if ( is_array( $font_family ) && isset( $font_family['default'] ) )
			return $font_family['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'font_family', 'default' );
	}

	/**
	 * This function sets the default font family for Heading 2 in the Customizer.
	 */
	function theme_mod_symphony_h2_font_family( $family, $default = false ) {
		$font_family = $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'h2', 'font_family' );

		// Individual
		if ( is_array( $font_family ) && isset( $font_family['default'] ) )
			return $font_family['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'font_family', 'default' );
	}

	/**
	 * This function sets the default font family for Heading 3 in the Customizer.
	 */
	function theme_mod_symphony_h3_font_family( $family, $default = false ) {
		$font_family = $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'h3', 'font_family' );

		// Individual
		if ( is_array( $font_family ) && isset( $font_family['default'] ) )
			return $font_family['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'font_family', 'default' );
	}

	/**
	 * This function sets the default font family for Heading 4 in the Customizer.
	 */
	function theme_mod_symphony_h4_font_family( $family, $default = false ) {
		$font_family = $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'h4', 'font_family' );

		// Individual
		if ( is_array( $font_family ) && isset( $font_family['default'] ) )
			return $font_family['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'font_family', 'default' );
	}

	/**
	 * This function sets the default font family for Heading 5 in the Customizer.
	 */
	function theme_mod_symphony_h5_font_family( $family, $default = false ) {
		$font_family = $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'h5', 'font_family' );

		// Individual
		if ( is_array( $font_family ) && isset( $font_family['default'] ) )
			return $font_family['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'font_family', 'default' );
	}

	/**
	 * This function sets the default font family for Heading 6 in the Customizer.
	 */
	function theme_mod_symphony_h6_font_family( $family, $default = false ) {
		$font_family = $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'h6', 'font_family' );

		// Individual
		if ( is_array( $font_family ) && isset( $font_family['default'] ) )
			return $font_family['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'headings', 'font_family', 'default' );
	}

	/**
	 * Body (content)
	 */

	/**
	 * This function sets the default font size for the body (content) in the Customizer.
	 */
	function theme_mod_symphony_body_font_size( $size, $default = false ) {
		return $this->get_theme_mod_value( $size, $default, 'fonts', 'body', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute value for the body (content) in the Customizer.
	 */
	function theme_mod_symphony_body_font_size_min( $min, $default = false ) {
		return $this->get_theme_mod_value( $min, $default, 'fonts', 'body', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute value for the body (content) in the Customizer.
	 */
	function theme_mod_symphony_body_font_size_max( $max, $default = false ) {
		return $this->get_theme_mod_value( $max, $default, 'fonts', 'body', 'font_size', 'max' );
	}

	/**
	 * This function sets the default line height for the body (content) in the Customizer.
	 */
	function theme_mod_symphony_body_line_height( $size, $default = false ) {
		return $this->get_theme_mod_value( $size, $default, 'fonts', 'body', 'line_height', 'default' );
	}

	/**
	 * This function sets the default line height min attribute value for the body (content) in the Customizer.
	 */
	function theme_mod_symphony_body_line_height_min( $min, $default = false ) {
		return $this->get_theme_mod_value( $min, $default, 'fonts', 'body', 'line_height', 'min' );
	}

	/**
	 * This function sets the default line height max attribute value for the body (content) in the Customizer.
	 */
	function theme_mod_symphony_body_line_height_max( $max, $default = false ) {
		return $this->get_theme_mod_value( $max, $default, 'fonts', 'body', 'line_height', 'max' );
	}

	/**
	 * This function sets the default font family for the body (content) in the Customizer.
	 */
	function theme_mod_symphony_body_font_family( $family, $default = false ) {
		return $this->get_theme_mod_value( $family, $default, 'fonts', 'body', 'font_family', 'default' );
	}

	/**
	 * Widgets
	 */

	/**
	 * This function sets the default font size for the widget titles in the Customizer.
	 */
	function theme_mod_symphony_widget_title_font_size( $size, $default = false ) {
		$font_size = $this->get_theme_mod_value( $size, $default, 'fonts', 'widget', 'title', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['default'] ) )
			return $font_size['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $size, $default, 'fonts', 'widget', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute value for the widget titles in the Customizer.
	 */
	function theme_mod_symphony_widget_title_font_size_min( $min, $default = false ) {
		$font_size = $this->get_theme_mod_value( $min, $default, 'fonts', 'widget', 'title', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['min'] ) )
			return $font_size['min'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $min, $default, 'fonts', 'widget', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute value for the widget titles in the Customizer.
	 */
	function theme_mod_symphony_widget_title_font_size_max( $max, $default = false ) {
		$font_size = $this->get_theme_mod_value( $max, $default, 'fonts', 'widget', 'title', 'font_size' );

		// Individual
		if ( is_array( $font_size ) && isset( $font_size['max'] ) )
			return $font_size['max'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $max, $default, 'fonts', 'widget', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font family for the widget titles in the Customizer.
	 */
	function theme_mod_symphony_widget_title_font_family( $family, $default = false ) {
		$font_family = $this->get_theme_mod_value( $family, $default, 'fonts', 'widget', 'title', 'font_family' );

		// Individual
		if ( is_array( $font_family ) && isset( $font_family['default'] ) )
			return $font_family['default'];
		// Global (fallback)
		else
			return $this->get_theme_mod_value( $family, $default, 'fonts', 'widget', 'font_family', 'default' );
	}

	/**
	 * This function sets the default font size for the widgets in the Customizer.
	 */
	function theme_mod_symphony_widget_font_size( $size, $default = false ) {
		return $this->get_theme_mod_value( $size, $default, 'fonts', 'widget', 'font_size', 'default' );
	}

	/**
	 * This function sets the default font size min attribute value for the widgets in the Customizer.
	 */
	function theme_mod_symphony_widget_font_size_min( $min, $default = false ) {
		return $this->get_theme_mod_value( $min, $default, 'fonts', 'widget', 'font_size', 'min' );
	}

	/**
	 * This function sets the default font size max attribute value for the widgets in the Customizer.
	 */
	function theme_mod_symphony_widget_font_size_max( $max, $default = false ) {
		return $this->get_theme_mod_value( $max, $default, 'fonts', 'widget', 'font_size', 'max' );
	}

	/**
	 * This function sets the default font family for the widgets in the Customizer.
	 */
	function theme_mod_symphony_widget_font_family( $family, $default = false ) {
		return $this->get_theme_mod_value( $family, $default, 'fonts', 'widget', 'font_family', 'default' );
	}

	/**********************
	 * Internal Functions *
	 **********************/

	/**
	 * This function is identical to the core get_theme_mods() function found in WordPress 4.1,
	 * however we pass a parameter that can be used to get the theme mods of a different theme,
	 * and we are not checking for the depreciated location.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/5eb5afac3450f2bd02886f5f1f4fe56ed208fd79/wp-includes/theme.php#L870-L890
	 */
	function get_theme_mods( $theme_slug = false ) {
		$theme_slug = ( $theme_slug ) ? $theme_slug : get_option( 'stylesheet' );
		$mods = get_option( 'theme_mods_' . $theme_slug );

		return $mods;
	}

	/**
	 * This function sets all theme mods on the current theme or any theme if $theme_slug
	 * is passed.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/5eb5afac3450f2bd02886f5f1f4fe56ed208fd79/wp-includes/theme.php#L932-L960
	 * 		for set_theme_mod() in WordPress 4.1
	 */
	function set_theme_mods( $mods, $theme_slug = false ) {
		$theme_slug = ( $theme_slug ) ? $theme_slug : get_option( 'stylesheet' );

		// Update theme mods option
		update_option( 'theme_mods_' . $theme_slug, $mods );
	}
}

/**
 * Create an instance of the Symphony_Theme_Helper class.
 */
function Symphony_Theme_Helper() {
	return Symphony_Theme_Helper::instance();
}

Symphony_Theme_Helper();