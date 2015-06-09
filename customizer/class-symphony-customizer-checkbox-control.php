<?php

// Make sure the Customize Control class exists
if ( ! class_exists( 'WP_Customize_Control' ) )
	return false;

/**
 * This class is a custom controller for the Theme Customizer API for Slocum Themes
 * which extends the WP_Customize_Control class provided by WordPress.
 */
class Symphony_Customizer_Checkbox_Control extends WP_Customize_Control {
	/*
	 * @var string, CSS ID used to target this particular control
	 */
	public $css_id = '';

	/*
	 * @var string, CSS class used to target this particular control
	 */
	public $css_class = '';

	/*
	 * @var string, Label for the "checked" state of the checkbox
	 */
	public $checked_label = 'Hide';

	/*
	 * @var string, Label for the "unchecked" state of the checkbox
	 */
	public $unchecked_label = 'Show';

	/*
	 * @var string, CSS <style> block with styles for this particular control
	 */
	public $style = array( 'before' => '', 'after' => '', 'general' => '' );

	/**
	 * Constructor
	 */
	function __construct( $manager, $id, $args ) {
		// Actions/ Filters
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) ); // Enqueue scripts on Theme Customizer
		add_action( 'customize_controls_print_styles', array( $this, 'customize_controls_print_styles' ) ); // Output styles on Theme Customizer

		// Call the parent constructor here
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * This function renders the control's content.
	 */
	public function render_content() {
	?>
		<div class="customize-checkbox customize-sds-theme-options-checkbox">
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

			<style type="text/css">
				<?php
					// Before
					echo ( isset( $this->style['before'] ) ) ? '.' . $this->css_class .':before { ' . $this->style['before']. ' }' : false;
				?>
				<?php
					// After
					echo ( isset( $this->style['after'] ) ) ? '.' . $this->css_class .':after { ' . $this->style['after']. ' }' : false;
				?>
				<?php
					// General
					echo ( isset( $this->style['general'] ) ) ? '.' . $this->css_class .' { ' . $this->style['general']. ' }' : false;
				?>
			</style>

			<div class="checkbox sds-theme-options-checkbox checkbox-show-hide <?php echo $this->css_class; ?>" data-label-left="<?php esc_attr_e( sprintf( '%1$s', $this->unchecked_label ), 'symphony' ); ?>" data-label-right="<?php esc_attr_e( sprintf( '%1$s', $this->checked_label ), 'symphony' ); ?>">
				<input type="checkbox" id="<?php echo $this->css_id; ?>" name="<?php echo $this->id; ?>" <?php ( ! $this->value() ) ? checked( false ) : checked( true ); ?> <?php echo $this->get_link(); ?> />
				<label for="<?php echo $this->css_id; ?>">| | |</label>
			</div>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</div>
	<?php
	}

	/**
	 * This function enqueues scripts and styles on the Theme Customizer only.
	 */
	function customize_controls_enqueue_scripts() {
		wp_enqueue_style( 'sds-theme-options', get_template_directory_uri() . '/includes/css/sds-theme-options.css' );
	}

	/**
	 * This function prints styles on the Theme Customizer only.
	 */
	function customize_controls_print_styles() {
		global $_wp_admin_css_colors;

		$user_admin_color = get_user_meta(  get_current_user_id(), 'admin_color', true );

		// Output styles to match selected admin color scheme
		if ( isset( $_wp_admin_css_colors[$user_admin_color] ) ) :
	?>
			<style type="text/css">
				/* Checkboxes */
				.customize-sds-theme-options-checkbox .sds-theme-options-checkbox:before {
					background: <?php echo $_wp_admin_css_colors[$user_admin_color]->colors[2]; ?>;
				}
			</style>
	<?php
		endif;
	}
}