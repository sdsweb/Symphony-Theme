<?php
/**
 * Symphony Customizer Typography
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

final class Symphony_Customizer_Typography {
	/**
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * @var string, Transient name
	 */
	public $transient_name = 'symphony_customizer_fonts_';

	/**
	 * @var array, Transient data
	 */
	public $transient_data = array();

	/**
	 * @var Symphony_Customizer_Typography, Instance of the class
	 */
	protected static $_instance;

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
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 9999 ); // After Setup Theme (late; load assets based on theme support)
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) ); // Enqueue scripts/styles
		add_action( 'wp_head', array( $this, 'wp_head' ) ); // Output Customizer CSS
		add_action( 'customize_save_after', array( $this, 'reset_transient' ) ); // Customize Save (reset transients)
		//add_action( 'after_switch_theme', array( $this, 'reset_transient' ) ); // After Switch Theme (reset transients)
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance

		// Bail if no support defined by theme
		if ( ! $symphony_theme_helper->theme_support || ! is_array( $symphony_theme_helper->theme_support ) || empty( $symphony_theme_helper->theme_support ) )
			return;

		// Symphony Font Customizer Settings/Controls
		if ( $symphony_theme_helper->current_theme_supports( 'fonts' ) )
			include_once( 'class-symphony-customizer-fonts.php' ); // Symphony Customizer Font Settings/Controls
	}

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

	/**
	 * This function enqueues scripts and styles relating to Customizer functionality.
	 */
	function wp_enqueue_scripts() {
		$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance
		$protocol = is_ssl() ? 'https' : 'http';

		// Bail if no support defined by theme
		if ( ! $symphony_theme_helper->theme_support || ! is_array( $symphony_theme_helper->theme_support ) || empty( $symphony_theme_helper->theme_support ) )
			return;

		// Check to see if this theme supports any Google Web Fonts
		if ( $symphony_theme_helper->current_theme_supports( 'fonts' ) ) {
			$symphony_customizer_fonts = Symphony_Customizer_Fonts();

			if ( $symphony_theme_helper->has_google_web_font_support() && ! $symphony_customizer_fonts->has_default_font_families() )
				wp_enqueue_style( 'symphony-' . $symphony_theme_helper->theme->get_template() . '-google-web-fonts', $protocol . '://fonts.googleapis.com/css?family=' . $symphony_customizer_fonts->get_google_web_font_stylesheet_families() );
		}
	}

	/**
	 * This function outputs all CSS associated with Customizer settings within this plugin.
	 */
	function wp_head() {
		$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance

		// Bail if no support defined by theme
		if ( ! $symphony_theme_helper->theme_support || ! is_array( $symphony_theme_helper->theme_support ) || empty( $symphony_theme_helper->theme_support ) )
			return;

		echo $this->get_customizer_css();
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
	 * This function generates CSS for a customizer value based on parameters.
	 */
	function get_customizer_setting_css( $css, $value, $type = false ) {
		$r = ''; // Return value

		// If we have a type, we need special output
		if ( $type ) {
			switch( $type ) {
				// Individual Navigation Menus
				case 'individual_navigation_menus':
					// Remove empty navigation Menus
					$value = array_filter( $value );

					// Loop through Headings
					foreach( $value as $key => $val ) {
						// Open selector
						$r .= implode( ', ', $css[$key]['selector'] ). ' {' . "\n";

						// Output properties
						foreach ( $css[$key]['properties'] as $property => $unit )
							$r .= "\t" . $property . ': ' . $val . $unit . ';' . "\n";

						// Close selector
						$r .= '}' . "\n";
					}
				break;
				// Individual Headings
				case 'individual_headings':
					// Remove empty Headings
					$value = array_filter( $value );

					// Loop through Headings
					foreach( $value as $key => $val ) {
						// Open selector
						$r .= implode( ', ', $css[$key]['selector'] ). ' {' . "\n";

						// Output properties
						foreach ( $css[$key]['properties'] as $property => $unit )
							$r .= "\t" . $property . ': ' . $val . $unit . ';' . "\n";

						// Close selector
						$r .= '}' . "\n";
					}
				break;
				// Global Headings
				case 'headings':
					$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance
					$percentages = $symphony_theme_helper->get_theme_support_value( 'fonts', 'headings', 'font_size', 'percent' );

					// Make sure we have an array of selectors
					if ( ! is_array( $css['selector'] ) )
						$css['selector'] = explode( ', ', $css['selector'] );

					// Bail if we don't have at least one CSS selector or percentages
					if ( empty( $css['selector'] ) && empty( $percentages ) )
						return $r;

					// Loop through selectors
					foreach ( $css['selector'] as $selector ) {
						// Open selector
						$r .= $selector . ' {' . "\n";

						// Output properties (find the percentage if need be)
						foreach ( $css['properties'] as $property => $unit )
							if ( isset( $percentages[$selector] ) )
								$r .= "\t" . $property . ': ' . round( $value * ( $percentages[$selector] / 100 ) ) . $unit . ';' . "\n";

						// Close selector
						$r .= '}' . "\n";
					}
				break;
			}
		}
		// Otherwise normal output
		else {
			// Open selector
			$r = ( is_array( $css['selector'] ) ) ? implode( ', ', $css['selector'] ) . ' {' . "\n" : $css['selector'] . ' {' . "\n";

			// Output properties
			foreach( $css['properties'] as $property => $unit )
				$r .= "\t" . $property . ': ' . $value . $unit . ';' . "\n";

			// Close selector
			$r .= '}' . "\n";
		}

		return $r;
	}

	/**
	 * This function returns a CSS <style> block for Customizer theme mods.
	 */
	function get_customizer_css() {
		// Check transient first (not in the Customizer)
		if ( ! $this->is_customize_preview() && ! empty( $this->transient_data ) && isset( $this->transient_data['customizer_css' ] ) )
			return $this->transient_data['customizer_css'];
		// Otherwise return data
		else {
			$symphony_theme_helper = Symphony_Theme_Helper(); // Grab the Symphony_Theme_Helper instance

			// Open <style>
			$r = '<style type="text/css" id="symphony-' . $symphony_theme_helper->theme->get_template() . '-theme-customizer">' . "\n";

			/**
			 * Site Title Font Size
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'site_title', 'font_size' ) ) {
				$theme_mod_site_title_font_size = get_theme_mod( 'symphony_site_title_font_size' );
				$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'font_size', 'default' );
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'font_size', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_site_title_font_size && $theme_support_default && $theme_mod_site_title_font_size !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.site-title' ) : array(), // CSS Selector
						'properties' => array(
							'font-size' => 'px'
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$site_title_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'font_size', 'css' ) ) );
					if ( $site_title_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $site_title_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_site_title_font_size );
				}
			}

			/**
			 * Site Title Font Family
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'site_title', 'font_family' ) ) {
				$theme_mod_site_title_font_family = "'" . get_theme_mod( 'symphony_site_title_font_family' ) . "'";
				$theme_support_default = "'" . $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'font_family', 'default' ) . "'";
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'font_family', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_site_title_font_family && $theme_support_default && $theme_mod_site_title_font_family !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.site-title' ) : array(), // CSS Selector
						'properties' => array(
							'font-family' => ''
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$site_title_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'site_title', 'font_family', 'css' ) ) );
					if ( $site_title_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $site_title_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_site_title_font_family );
				}
			}

			/**
			 * Tagline Font Size
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'tagline', 'font_size' ) ) {
				$theme_mod_tagline_font_size = get_theme_mod( 'symphony_tagline_font_size' );
				$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'font_size', 'default' );
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'font_size', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_tagline_font_size && $theme_support_default && $theme_mod_tagline_font_size !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.slogan' ) : array(), // CSS Selector
						'properties' => array(
							'font-size' => 'px'
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$tagline_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'font_size', 'css' ) ) );
					if ( $tagline_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $tagline_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_tagline_font_size );
				}
			}

			/**
			 * Tagline Font Family
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'tagline', 'font_family' ) ) {
				$theme_mod_tagline_font_family = "'" . get_theme_mod( 'symphony_tagline_font_family' ) . "'";
				$theme_support_default = "'" . $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'font_family', 'default' ) . "'";
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'font_family', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_tagline_font_family && $theme_support_default && $theme_mod_tagline_font_family !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.slogan' ) : array(), // CSS Selector
						'properties' => array(
							'font-family' => ''
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$tagline_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'tagline', 'font_family', 'css' ) ) );
					if ( $tagline_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $tagline_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_tagline_font_family );
				}
			}

			/**
			 * Navigation Font Size
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'navigation', 'font_size' ) || $symphony_theme_helper->has_individual_navigation_support() ) {
				// Determine individual navigation support
				$individual_navigation_support = $symphony_theme_helper->get_individual_navigation_support();

				// Individual Navigation Support
				if ( ! empty( $individual_navigation_support ) ) {
					$theme_mod_navigation_font_sizes = array();
					$css_properties = array();

					foreach ( $individual_navigation_support as $nav_menu_id => $nav_menu ) {
						$theme_support_default = $nav_menu['support']['font_size']['default'];

						// Get theme mod for this navigation menu
						$theme_mod_navigation_font_sizes[$nav_menu_id] = get_theme_mod( 'symphony_navigation_' . $nav_menu_id . '_font_size' );

						// Check specific 'ignore_default_selector'
						$ignore_default_selector = ( isset( $nav_menu['support']['css'] ) && isset( $nav_menu['support']['css']['ignore_default_selector'] ) && $nav_menu['support']['css']['ignore_default_selector'] );

						// Populate default CSS properties for this navigation menu
						$css_properties[$nav_menu_id] = array(
							'selector' => ( ! $ignore_default_selector ) ? array( 'nav' ) : array(), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						);

						// Compare current theme mod against the default
						if ( $theme_mod_navigation_font_sizes[$nav_menu_id] !== $theme_support_default ) {
							$navigation_css_properties = array_filter( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', $nav_menu_id, 'css' ) );

							// If we have navigation menu properties
							if ( ! empty( $navigation_css_properties ) ) {
								// Merge the properties
								$css_properties[$nav_menu_id] = array_merge_recursive( $css_properties[$nav_menu_id], $navigation_css_properties );

								// Remove duplicates from CSS properties
								array_walk( $css_properties[$nav_menu_id], array( $this, 'remove_duplicate_array_values' ) );
							}
						}
						// Theme mod is default value, remove it
						else {
							unset( $css_properties[$nav_menu_id] );
							unset( $theme_mod_navigation_font_sizes[$nav_menu_id] );
						}
					}

					// Output the CSS selector, properties, value, and units
					if ( ! empty( $css_properties ) )
						$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_navigation_font_sizes, 'individual_navigation_menus' );

				}
				// Global Navigation Support
				else {
					$theme_mod_navigation_font_size = get_theme_mod( 'symphony_navigation_font_size' );
					$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_size', 'default' );
					$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'css', 'ignore_default_selector' );

					// Check specific 'ignore_default_selector'
					if ( ! $ignore_default_selector ) {
						$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_size', 'css' );
						$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
					}

					// Compare current theme mod against the default
					if ( $theme_mod_navigation_font_size && $theme_support_default && $theme_mod_navigation_font_size !== $theme_support_default ) {
						$css_properties = array(
							'selector' => ( ! $ignore_default_selector ) ? array( '.primary-nav li a' ) : array(), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						);

						// Check if the theme has CSS properties defined and merge them if necessary
						$navigation_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_size', 'css' ) ) );
						if ( $navigation_css_properties )
							$css_properties = array_merge_recursive( $css_properties, $navigation_css_properties );

						// Remove duplicates from CSS properties
						array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

						// Output the CSS selector, properties, value, and units
						$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_navigation_font_size );
					}
				}
			}

			/**
			 * Navigation Font Family
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'navigation', 'font_family' ) || $symphony_theme_helper->has_individual_navigation_support() ) {
				// Determine individual navigation support
				$individual_navigation_support = $symphony_theme_helper->get_individual_navigation_support();

				// Individual Navigation Support
				if ( ! empty( $individual_navigation_support ) ) {
					$theme_mod_navigation_font_families = array();
					$css_properties = array();

					foreach ( $individual_navigation_support as $nav_menu_id => $nav_menu ) {
						$theme_support_default = $nav_menu['support']['font_family']['default'];

						// Get theme mod for this navigation menu
						$theme_mod_navigation_font_families[$nav_menu_id] = get_theme_mod( 'symphony_navigation_' . $nav_menu_id . '_font_family' );

						// Check specific 'ignore_default_selector'
						$ignore_default_selector = ( isset( $nav_menu['support']['css'] ) && isset( $nav_menu['support']['css']['ignore_default_selector'] ) && $nav_menu['support']['css']['ignore_default_selector'] );

						// Populate default CSS properties for this navigation menu
						$css_properties[$nav_menu_id] = array(
							'selector' => ( ! $ignore_default_selector ) ? array( 'nav' ) : array(), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						);

						// Compare current theme mod against the default
						if ( $theme_mod_navigation_font_families[$nav_menu_id] !== $theme_support_default ) {
							$navigation_css_properties = array_filter( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', $nav_menu_id, 'css' ) );

							// If we have navigation menu properties
							if ( ! empty( $navigation_css_properties ) ) {
								// Merge the properties
								$css_properties[$nav_menu_id] = array_merge_recursive( $css_properties[$nav_menu_id], $navigation_css_properties );

								// Remove duplicates from CSS properties
								array_walk( $css_properties[$nav_menu_id], array( $this, 'remove_duplicate_array_values' ) );
							}
						}
						// Theme mod is default value, remove it
						else {
							unset( $css_properties[$nav_menu_id] );
							unset( $theme_mod_navigation_font_families[$nav_menu_id] );
						}
					}

					// Output the CSS selector, properties, value, and units
					if ( ! empty( $css_properties ) )
						$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_navigation_font_families, 'individual_navigation_menus' );

				}
				// Global Navigation Support
				else {
					$theme_mod_navigation_font_family = "'" . get_theme_mod( 'symphony_navigation_font_family' ) . "'";
					$theme_support_default = "'" . $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_family', 'default' ) . "'";
					$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'css', 'ignore_default_selector' );

					// Check specific 'ignore_default_selector'
					if ( ! $ignore_default_selector ) {
						$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_family', 'css' );
						$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
					}

					// Compare current theme mod against the default
					if ( $theme_mod_navigation_font_family && $theme_support_default && $theme_mod_navigation_font_family !== $theme_support_default ) {
						$css_properties = array(
							'selector' => ( ! $ignore_default_selector ) ? array( '.primary-nav li a' ) : array(), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						);

						// Check if the theme has CSS properties defined and merge them if necessary
						$navigation_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_family', 'css' ) ) );
						if ( $navigation_css_properties )
							$css_properties = array_merge_recursive( $css_properties, $navigation_css_properties );

						// Remove duplicates from CSS properties
						array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

						// Output the CSS selector, properties, value, and units
						$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_navigation_font_family );
					}
				}
			}

			/**
			 * Heading Font Sizes
			 */
			// TODO: add 'ignore_default_selector' functionality
			if ( ( $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', 'font_size' ) || $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', 'font_size' ) ) || $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) ) {
				// Determine individual heading support
				$individual_heading_support = ( $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) );

				// Individual Heading Support
				if ( $individual_heading_support ) {
					$theme_mod_headings_font_sizes = array(
						'h1' => get_theme_mod( 'symphony_h1_font_size' ),
						'h2' => get_theme_mod( 'symphony_h2_font_size' ),
						'h3' => get_theme_mod( 'symphony_h3_font_size' ),
						'h4' => get_theme_mod( 'symphony_h4_font_size' ),
						'h5' => get_theme_mod( 'symphony_h5_font_size' ),
						'h6' => get_theme_mod( 'symphony_h6_font_size' )
					);
					$css_properties = array(
						// Heading 1
						'h1' => array(
							'selector' => array( 'h1' ), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						),
						// Heading 1
						'h2' => array(
							'selector' => array( 'h2' ), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						),
						// Heading 1
						'h3' => array(
							'selector' => array( 'h3' ), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						),
						// Heading 4
						'h4' => array(
							'selector' => array( 'h4' ), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						),
						// Heading 5
						'h5' => array(
							'selector' => array( 'h5' ), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						),
						// Heading 6
						'h6' => array(
							'selector' => array( 'h6' ), // CSS Selector
							'properties' => array(
								'font-size' => 'px'
							)
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					foreach ( $css_properties as $heading => &$properties ) {
						$heading_properties = ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'headings', $heading );

						// Compare current theme mod against the default
						if ( $theme_mod_headings_font_sizes[$heading] && isset( $heading_properties['font_size']['default'] ) && $theme_mod_headings_font_sizes[$heading] !== $heading_properties['font_size']['default'] ) {
							if ( isset( $heading_properties['font_size']['css'] ) )
								$heading_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'headings', $heading, 'css' ), ( array ) $heading_properties['font_size']['css'] ) );
							else
								$heading_properties = array_filter( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'headings', $heading, 'css' ) );

							// If we have Heading properties
							if ( ! empty( $heading_properties ) ) {
								// Merge the properties
								$properties = array_merge_recursive( $properties, $heading_properties );

								// Remove duplicates from CSS properties
								array_walk( $properties, array( $this, 'remove_duplicate_array_values' ) );
							}
						}
						// Theme mod is default value, remove it
						else {
							unset( $css_properties[$heading] );
							unset( $theme_mod_headings_font_sizes[$heading] );
						}
					}

					// Output the CSS selector, properties, value, and units
					if ( ! empty( $css_properties ) )
						$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_headings_font_sizes, 'individual_headings' );
				}
				// TODO: Finish/Test the following
				// Global Heading Support
				/*else {
					$theme_mod_headings_font_family = "'" . get_theme_mod( 'symphony_headings_font_family' ) . "'";
					$css_properties = array(
						'selector' => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), // CSS Selector
						'properties' => array(
							'font-size' => 'px'
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					if ( $theme_css_properties = $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_family', 'css' ) )
						$css_properties = array_merge_recursive( $css_properties, $theme_css_properties );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_headings_font_family, 'headings' );
				}*/
			}

			/**
			 * Heading Font Families
			 */
			// TODO: Add 'ignore_default_selector' functionality
			if ( ( $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', 'font_family' ) || $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', 'font_family' ) ) || $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) ) {
				// Determine individual heading support
				$individual_heading_support = ( $symphony_theme_helper->current_theme_supports( 'fonts', 'headings', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) );

				// Individual Heading Support
				if ( $individual_heading_support ) {
					$theme_mod_headings_font_families = array(
						'h1' => get_theme_mod( 'symphony_h1_font_family' ),
						'h2' => get_theme_mod( 'symphony_h2_font_family' ),
						'h3' => get_theme_mod( 'symphony_h3_font_family' ),
						'h4' => get_theme_mod( 'symphony_h4_font_family' ),
						'h5' => get_theme_mod( 'symphony_h5_font_family' ),
						'h6' => get_theme_mod( 'symphony_h6_font_family' )
					);
					$css_properties = array(
						// Heading 1
						'h1' => array(
							'selector' => array( 'h1' ), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						),
						// Heading 1
						'h2' => array(
							'selector' => array( 'h2' ), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						),
						// Heading 1
						'h3' => array(
							'selector' => array( 'h3' ), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						),
						// Heading 4
						'h4' => array(
							'selector' => array( 'h4' ), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						),
						// Heading 5
						'h5' => array(
							'selector' => array( 'h5' ), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						),
						// Heading 6
						'h6' => array(
							'selector' => array( 'h6' ), // CSS Selector
							'properties' => array(
								'font-family' => ''
							)
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					foreach ( $css_properties as $heading => &$properties ) {
						$heading_properties = ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'headings', $heading );
						// Compare current theme mod against the default
						if ( $theme_mod_headings_font_families[$heading] && isset( $heading_properties['font_family']['default'] ) && $theme_mod_headings_font_families[$heading] !== $heading_properties['font_family']['default'] ) {

							if ( isset( $heading_properties['font_family']['css'] ) )
								$heading_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'headings', $heading, 'css' ), ( array ) $heading_properties['font_family']['css'] ) );
							else
								$heading_properties = array_filter( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'headings', $heading, 'css' ) );

							// If we have Heading properties
							if ( ! empty( $heading_properties ) ) {
								// Merge the properties
								$properties = array_merge_recursive( $properties, $heading_properties );

								// Remove duplicates from CSS properties
								array_walk( $properties, array( $this, 'remove_duplicate_array_values' ) );
							}
						}
						// Theme mod is default value, remove it
						else {
							unset( $css_properties[$heading] );
							unset( $theme_mod_headings_font_families[$heading] );
						}
					}

					// Output the CSS selector, properties, value, and units
					if ( ! empty( $css_properties ) )
						$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_headings_font_families, 'individual_headings' );
				}
				// TODO: Finish/Test the following
				// Global Heading Support
				/*else {
					$theme_mod_headings_font_family = "'" . get_theme_mod( 'symphony_headings_font_family' ) . "'";
					$css_properties = array(
						'selector' => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), // CSS Selector
						'properties' => array(
							'font-family' => ''
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					if ( $theme_css_properties = $symphony_theme_helper->get_theme_support_value( 'fonts', 'navigation', 'font_family', 'css' ) )
						$css_properties = array_merge_recursive( $css_properties, $theme_css_properties );

					// Output the CSS selector, properties, value, and units
					if ( ! empty( $css_properties ) )
						$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_headings_font_family, 'headings' );
				}*/
			}

			/**
			 * Body Font Size
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'body', 'font_size' ) ) {
				$theme_mod_body_font_size = get_theme_mod( 'symphony_body_font_size' );
				$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'font_size', 'default' );
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'font_size', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_body_font_size && $theme_support_default && $theme_mod_body_font_size !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( 'body' ) : array(), // CSS Selector
						'properties' => array(
							'font-size' => 'px'
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$body_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'font_size', 'css' ) ) );
					if ( $body_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $body_css_properties );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_body_font_size );
				}
			}

			/**
			 * Body Line Height
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'body', 'line_height' ) ) {
				$theme_mod_body_line_height = get_theme_mod( 'symphony_body_line_height' );
				$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'line_height', 'default' );
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'line_height', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_body_line_height && $theme_support_default && $theme_mod_body_line_height !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( 'body' ) : array(), // CSS Selector
						'properties' => array(
							'line-height' => 'px'
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$body_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'line_height', 'css' ) ) );
					if ( $body_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $body_css_properties );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_body_line_height );
				}
			}

			/**
			 * Body Font Family
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'body', 'font_family' ) ) {
				$theme_mod_body_font_family = "'" . get_theme_mod( 'symphony_body_font_family' ) . "'";
				$theme_support_default = "'" . $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'font_family', 'default' ) . "'";
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'font_family', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_body_font_family && $theme_support_default && $theme_mod_body_font_family !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( 'body' ) : array(), // CSS Selector
						'properties' => array(
							'font-family' => ''
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$body_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'body', 'font_family', 'css' ) ) );
					if ( $body_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $body_css_properties );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_body_font_family );
				}
			}

			/**
			 * Widget Title Font Size
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'widget', 'title', 'font_family' ) ) {
				$theme_mod_widget_font_size = get_theme_mod( 'symphony_widget_title_font_size' );
				$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'title', 'font_size' );
				if ( is_array( $theme_support_default ) && isset( $theme_support_default['default'] ) )
					$theme_support_default = $theme_support_default['default'];
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'title', 'font_size' );
					if ( is_array( $css ) && isset( $css['css'] ) )
						$css = $css['css'];
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_widget_font_size && $theme_support_default && $theme_mod_widget_font_size !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.widget-title' ) : array(), // CSS Selector
						'properties' => array(
							'font-size' => 'px'
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$widget_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_size', 'css' ) ) );
					if ( $widget_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $widget_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_widget_font_size );
				}
			}

			/**
			 * Widget Title Font Family
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'widget', 'title', 'font_family' ) ) {
				$theme_mod_widget_font_family = "'" . get_theme_mod( 'symphony_widget_title_font_family' ) . "'";
				$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'title', 'font_family' );
				if ( is_array( $theme_support_default ) && isset( $theme_support_default['default'] ) )
					$theme_support_default = "'" . $theme_support_default['default'] . "'";
				else
					$theme_support_default = "'" . $theme_support_default . "'";

				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'title', 'font_family' );
					if ( is_array( $css ) && isset( $css['css'] ) )
						$css = $css['css'];

					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_widget_font_family && $theme_support_default && $theme_mod_widget_font_family !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.widget-title' ) : array(), // CSS Selector
						'properties' => array(
							'font-family' => ''
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$widget_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_family', 'css' ) ) );
					if ( $widget_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $widget_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_widget_font_family );
				}
			}

			/**
			 * Widget Font Size
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'widget', 'font_size' ) ) {
				$theme_mod_widget_font_size = get_theme_mod( 'symphony_widget_font_size' );
				$theme_support_default = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_size', 'default' );
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_size', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_widget_font_size && $theme_support_default && $theme_mod_widget_font_size !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.widget' ) : array(), // CSS Selector
						'properties' => array(
							'font-size' => 'px'
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$widget_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_size', 'css' ) ) );
					if ( $widget_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $widget_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_widget_font_size );
				}
			}

			/**
			 * Widget Font Family
			 */
			if ( $symphony_theme_helper->current_theme_supports( 'fonts', 'widget', 'font_family' ) ) {
				$theme_mod_widget_font_family = "'" . get_theme_mod( 'symphony_widget_font_family' ) . "'";
				$theme_support_default = "'" . $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_family', 'default' ) . "'";
				$ignore_default_selector = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css', 'ignore_default_selector' );

				// Check specific 'ignore_default_selector'
				if ( ! $ignore_default_selector ) {
					$css = $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_family', 'css' );
					$ignore_default_selector = ( isset( $css['ignore_default_selector'] ) && $css['ignore_default_selector'] );
				}

				// Compare current theme mod against the default
				if ( $theme_mod_widget_font_family && $theme_support_default && $theme_mod_widget_font_family !== $theme_support_default ) {
					$css_properties = array(
						'selector' => ( ! $ignore_default_selector ) ? array( '.widget' ) : array(), // CSS Selector
						'properties' => array(
							'font-family' => ''
						)
					);

					// Check if the theme has CSS properties defined and merge them if necessary
					$widget_css_properties = array_filter( array_merge_recursive( ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'css' ), ( array ) $symphony_theme_helper->get_theme_support_value( 'fonts', 'widget', 'font_family', 'css' ) ) );
					if ( $widget_css_properties )
						$css_properties = array_merge_recursive( $css_properties, $widget_css_properties );

					// Remove duplicates from CSS properties
					array_walk( $css_properties, array( $this, 'remove_duplicate_array_values' ) );

					// Output the CSS selector, properties, value, and units
					$r .= $this->get_customizer_setting_css( $css_properties, $theme_mod_widget_font_family );
				}
			}

			// Close </style>
			$r .= '</style>';

			return $r;
		}
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

		// Bail if no support defined by theme
		if ( ! $symphony_theme_helper->theme_support || ! is_array( $symphony_theme_helper->theme_support ) || empty( $symphony_theme_helper->theme_support ) )
			return $data;

		// Check to see if this theme supports any Google Web Fonts
		if ( $symphony_theme_helper->current_theme_supports( 'fonts' ) ) {
			$symphony_customizer_fonts = Symphony_Customizer_Fonts();

			$data['google_web_font_stylesheet_families'] = $symphony_customizer_fonts->get_google_web_font_stylesheet_families();
			$data['default_font_families'] = $symphony_customizer_fonts->get_default_font_families();
			$data['has_default_font_families'] = $symphony_customizer_fonts->has_default_font_families();
		}

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

	/**********************
	 * Internal Functions *
	 **********************/

	/**
	 * This function removes duplicates and re-indexes nested arrays.
	 */
	public function remove_duplicate_array_values( &$array ) {
		// Only if the array is not associative
		if ( is_array( $array ) && ! ( bool ) count ( array_filter( array_keys( $array ), 'is_string' ) ) )
			$array = array_unique( $array );
	}
}

/**
 * Create an instance of the Symphony_Customizer_Typography class.
 */
function Symphony_Customizer_Typography() {
	return Symphony_Customizer_Typography::instance();
}

Symphony_Customizer_Typography();