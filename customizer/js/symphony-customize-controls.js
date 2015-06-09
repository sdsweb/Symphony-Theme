/**
 * Symphony Customizer Controls
 */
( function ( wp, $ ) {
	"use strict";

	var api = wp.customize,
		symphony_font_support = ( symphony_customize_controls.hasOwnProperty( 'theme_support' ) && symphony_customize_controls.theme_support.hasOwnProperty( 'fonts' ) && symphony_customize_controls.theme_support.fonts ),
		symphony_ff_regex = new RegExp( symphony_customize_controls.symphony_ff_control_regex ),
		symphony_google_choices = '';

	// Document ready
	$( function() {
		// Control visibility for background image controls
		$.each( {
			// Fixed Width Background Image
			'symphony_fixed_width_background_image': {
				controls: [ 'symphony_fixed_width_background_image_repeat', 'symphony_fixed_width_background_image_position_x', 'symphony_fixed_width_background_image_attachment' ],
				callback: function( to ) { return !! to; }
			},
			// Fluid Width Background Image
			'symphony_fluid_width_background_image': {
				controls: [ 'symphony_fluid_width_background_image_repeat', 'symphony_fluid_width_background_image_position_x', 'symphony_fluid_width_background_image_attachment' ],
				callback: function( to ) { return !! to; }
			},
			// Top Header Background Image
			'symphony_top_header_background_image': {
				controls: [ 'symphony_top_header_background_image_repeat', 'symphony_top_header_background_image_position_x', 'symphony_top_header_background_image_attachment' ],
				callback: function( to ) { return !! to; }
			},
			// Header Background Image
			'symphony_header_background_image': {
				controls: [ 'symphony_header_background_image_repeat', 'symphony_header_background_image_position_x', 'symphony_header_background_image_attachment' ],
				callback: function( to ) { return !! to; }
			},
			// Secondary Header Background Image
			'symphony_secondary_header_background_image': {
				controls: [ 'symphony_secondary_header_background_image_repeat', 'symphony_secondary_header_background_image_position_x', 'symphony_secondary_header_background_image_attachment' ],
				callback: function( to ) { return !! to; }
			},
			// Footer Background Image
			'symphony_footer_background_image': {
				controls: [ 'symphony_footer_background_image_repeat', 'symphony_footer_background_image_position_x', 'symphony_footer_background_image_attachment' ],
				callback: function( to ) { return !! to; }
			}
		}, function( settingId, o ) {
			api( settingId, function( setting ) {
				$.each( o.controls, function( i, controlId ) {
					api.control( controlId, function( control ) {
						var visibility = function( to ) {
							control.container.toggle( o.callback( to ) );
						};

						visibility( setting.get() );
						setting.bind( visibility );
					} );
				} );
			} );
		} );

		// WordPress 4.0
		/*if ( symphony_customize_controls.is_wp_4_0 === '1' ) {
			$.each( {
				// Fluid Width
				'fluid_width': {
					controls: [ 'symphony_fixed_width_background_color' ]
				}
			}, function( settingId, o ) {
				api( settingId, function( setting ) {
					$.each( o.controls, function( i, controlId ) {
						api.control( controlId, function( control ) {
							var visibility = function( to ) {
								// Fixed Width
								if ( ! setting.get() ) {
									// Panel
									control.container.parents( 'li.control-panel' ).slideDown();
								}
								// Fluid Width
								else {
									// Panel
									control.container.parents( 'li.control-panel' ).slideUp();
								}
							};

							visibility( setting.get() );
							setting.bind( visibility );
						} );
					} );
				} );
			} );
		}*/


		// If we have Symphony font support
		if ( symphony_font_support ) {
			// Build a list of choices
			$.each( symphony_customize_controls.google_font_families, function( property, choice ) {
				// String
				if ( typeof choice === 'string' ) {
					symphony_google_choices += '<option value="' + property + '"';
					symphony_google_choices += ( ! property ) ? ' disabled="true">' : '>';
					symphony_google_choices += choice + '</option>';
				}
				// Object
				else {
					symphony_google_choices += ( choice.hasOwnProperty( 'class' ) ) ? '<option value="' + property + '" class="symphony-select2-result ' + choice.class + '"' : '<option value="' + property + '" class="symphony-select2-result"';
					symphony_google_choices += ( ! property ) ? ' disabled="true">' : '>';
					symphony_google_choices += ( choice.hasOwnProperty( 'family' ) ) ? choice.family + '</option>' : property + '</option>';
				}
			} );

			// Populate Google Web Font choices
			api.control.each( function ( control ) {
				// Find Symphony Font Family Controls
				if ( control.id.search( symphony_ff_regex ) !== -1 ) {
					var $select = control.container.find( 'select' );
					// Populate this control with choices and set the value
					$select.html( symphony_google_choices ).val( control.setting.get() );

					// Select2
					setTimeout( function() { $select.select2(); }, 500 );
				}
			} );
		}
	} );
} )( wp, jQuery );