<?php
/**
 * Symphony Customizer Jetpack Portfolio Control
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

// Make sure the Customize Control class exists
if ( ! class_exists( 'WP_Customize_Control' ) )
	exit;

final class Symphony_Customizer_Jetpack_Portfolio_Control extends WP_Customize_Control {
	/**
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * @var string, cached <select> choices value
	 */
	public $cached_choices = '';

	/**
	 * This function sets up all of the actions and filters on instance. It also loads (includes)
	 * the required files and assets.
	 */
	function __construct( $manager, $id, $args = array() ) {
		// Hooks
		// TODO

		// Call the parent constructor here
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * This function enqueues scripts and styles
	 */
	public function enqueue() {
		// Stylesheets
		// TODO

		// Scripts
		// TODO

		// Call the parent enqueue method here
		parent::enqueue();
	}

	/**
	 * This function renders the control's content.
	 */
	public function render_content() {
	?>
		<div class="customize-symphony-jetpack-portfolio">

		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

			<?php
				// If Jetpack is installed and Custom Content Types Module is activated
				if ( symphony_jetpack_portfolio_active() ) :
			?>
				<p><strong><?php _e( 'Jetpack Portfolio Post Type is enabled.', 'symphony' ); ?></strong></p>
			<?php
				// Jetpack not installed or Custom Content Types Module is deactivated
				else :
					// Pubic post types with archives
					$public_post_types = get_post_types( array(
						'public' => true,
						'has_archive' => true
					) );
					$public_post_types = apply_filters( 'symphony_portfolio_public_post_types', $public_post_types );

					// If we have public post types
					if ( ! empty( $public_post_types ) ) :
			?>
					<select <?php $this->link(); ?> class="symphony-jetpack-portfolio-select">
						<option value=""><?php _e( 'Select a Portfolio Post Type', 'symphony' ); ?></option>
						<?php
							// Loop through public post types
							foreach ( $public_post_types as $public_post_type ) :
								$public_post_type_object = get_post_type_object( $public_post_type );
						?>
							<option value="<?php echo esc_attr( $public_post_type ); ?>" <?php if ( isset( $sds_theme_options['portfolio_post_type'] ) ) { selected( $sds_theme_options['portfolio_post_type'], $public_post_type ); } ?>><?php echo ( $public_post_type_object->labels->singular_name !== $public_post_type_object->labels->name ) ? sprintf( _x( '%1$s(s)', 'Possible plural value of post type singular name.', 'conductor' ), $public_post_type_object->labels->singular_name ) : $public_post_type_object->labels->name; ?></option>
						<?php
							endforeach;
						?>
					</select>
			<?php
					// Otherwise, no public post types found
					else:
			?>
						<p><strong><?php _e( 'No custom content types found. You must create at least one custom post type to be able to select a "Portfolio" post type.', 'symphony' ); ?></strong></p>
			<?php
					endif;
				endif;
			?>
		</label>
		<br />
		<br />
		<span class="description customize-control-description"><?php echo $this->description; ?></span>
	<?php
	}
}