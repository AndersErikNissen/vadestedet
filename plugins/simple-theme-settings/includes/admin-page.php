<?php
// @@ CREATE THE ADMIN MENU PAGE
add_action( 'admin_menu', function() {
  add_menu_page(
    'Simple Theme Settings',
    'Tema indstillinger',
    'manage_options',
    'simple-theme-settings',
    'sts_render_options_page',
    'dashicons-admin-generic',
    60
  );
});


// @@ REGISTER SETTINGS
add_action( 'admin_init', function() {
	// ## the settings group, that contains call the sections
	register_setting(
		'sts_options_group',
		'sts_options',      
		[
			'type' => 'array',
			'sanitize_callback' => 'sts_sanitize_options',
			'default' => [],
		],
	);


	// ## all sections
	add_settings_section(
		'sts_section_company',
		'Virksomheds information',
		'__return_false',
		'sts-theme-settings',
	);

	add_settings_section(
		'sts_section_contact',
		'Kontakt information',
		'__return_false',
		'sts-theme-settings',
	);

	add_settings_section(
		'sts_section_header',
		'Header indhold',
		'__return_false',
		'sts-theme-settings',
	);

	add_settings_section(
		'sts_section_footer',
		'Footer indhold',
		'__return_false',
		'sts-theme-settings',
	);


	// ## all fields
	$fields = sts_get_fields_definition();

	foreach ( $fields as $field ) {
		add_settings_field(
			$field[ 'key' ],
			$field[ 'label' ],
			'sts_render_field',
			'sts-theme-settings',
			'sts_section_' . $field[ 'group' ],
			$field
		);
	};
});


// @@ KEEP DATA NICE AND SAFE
function sts_process_single_value( $input ) {
  // ## default cleaning for standard text
  $result = sanitize_text_field( $value );
  
	// ## special case: allow HTML for anything with "logo" in the key
  if ( ! empty( $key ) && strpos( $key, 'logo' ) !== false ) {
      // ## allowed HTML including SVG
    $allowed_tags = [
      'svg' => [
        'xmlns'     	=> true,
        'viewbox'   	=> true,
        'width'     	=> true,
        'height'    	=> true,
        'fill'      	=> true,
        'stroke'    	=> true,
        'class'     	=> true,
        'id'        	=> true,
        'role'      	=> true,
        'aria-hidden' => true,
      ],
      'g' => [
        'id'        	=> true,
        'fill'      	=> true,
        'stroke'    	=> true,
        'transform' 	=> true,
        'class'     	=> true,
      ],
      'defs' => [],
      'use' => [
        'xlink:href' 	=> true,
        'href'       	=> true,
        'fill'       	=> true,
        'stroke'     	=> true,
      ],
      'path' => [
        'd'     			=> true,
        'fill'  			=> true,
        'stroke'			=> true,
        'id'    			=> true,
        'class' 			=> true,
      ],
      'circle' => [
        'cx'    			=> true,
        'cy'    			=> true,
        'r'     			=> true,
        'fill'  			=> true,
        'stroke'			=> true,
        'id'    			=> true,
        'class' 			=> true,
      ],
      'ellipse' => [
        'cx'    			=> true,
        'cy'    			=> true,
        'rx'    			=> true,
        'ry'    			=> true,
        'fill'  			=> true,
        'stroke'			=> true,
        'id'    			=> true,
        'class' 			=> true,
      ],
      'rect' => [
        'x'      			=> true,
        'y'      			=> true,
        'width'  			=> true,
        'height' 			=> true,
        'rx'     			=> true,
        'ry'     			=> true,
        'fill'   			=> true,
        'stroke'			=> true,
        'id'     			=> true,
        'class'  			=> true,
      ],
      'line' => [
        'x1'    			=> true,
        'y1'    			=> true,
        'x2'    			=> true,
        'y2'    			=> true,
        'stroke'			=> true,
        'id'    			=> true,
        'class' 			=> true,
      ],
      'polygon' => [
        'points'			=> true,
        'fill'  			=> true,
        'stroke'			=> true,
        'id'    			=> true,
        'class' 			=> true,
      ],
      'polyline' => [
        'points'			=> true,
        'fill'  			=> true,
        'stroke'			=> true,
        'id'    			=> true,
        'class' 			=> true,
      ],
      'text' => [
        'x'        		=> true,
        'y'        		=> true,
        'fill'     		=> true,
        'stroke'   		=> true,
        'font-family' => true,
        'font-size'   => true,
        'class'    		=> true,
        'id'       		=> true,
      ],
      'tspan' => [
        'x' 					=> true,
        'y' 					=> true,
        'dx'					=> true,
        'dy'					=> true,
        'fill' 				=> true,
        'stroke'			=> true,
        'class'				=> true,
        'id'   				=> true,
      ],
    ];

    $result = wp_kses( $value, $allowed_tags );
  } 

  return $result;
}

function sts_sanitize_options( $input ) {
  if ( ! is_array( $input ) ) return [];

  $clean = [];

  foreach ( $input as $key => $value ) {
    if ( is_array( $value ) ) {
      // ## if the value is an array, we've hit a group
      // ## call this same function again to clean the inside
      $clean[ $key ] = sts_sanitize_options( $value );
    } else {
      // ## it's a single string, clean with helper
      $clean[ $key ] = sts_process_single_value( $key, $value );
    }
  }
  
  return $clean;
}


// @@ RENDER PAGE
function sts_render_options_page() { ?>
	<section class="simple-theme-settings wrap">
		<h1>Tema indstillinger</h1>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'sts_options_group' );
				do_settings_sections( 'sts-theme-settings' );
				submit_button();
			?>
		</form>
	</section>
<?php }


// @@ RENDER SINGLE FIELD
function sts_render_field( $field ) {
	$options = get_option( 'sts_options', [] );
	$group = $field[ 'group' ] ?? '';
	$key = $field[ 'key' ] ?? '';
	$placeholder = $field[ 'placeholder' ] ?? '';

	$value = $options[ $group ][ $key ] ?? '';

	if ( $field['type'] === 'group' ) {
		echo '<fieldset class="sts-field-group">';
		echo '<legend>' . esc_html( $field['label'] ) . '</legend>';

		if ( ! empty( $field['description'] ) ) {
			echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
		}

		foreach ( $field['fields'] as $sub ) {
			$sub_key = $sub['key'];
			$value   = $options[ $group ][ $field['key'] ][ $sub_key ] ?? '';
			$placeholder = $sub['placeholder'] ?? '';

			echo '<div class="sts-sub-field">';
			echo '<label for="sts_options[' . $group . '][' . $field[ 'key' ] . '][' . $sub_key . ']">' . esc_html( $sub['label'] ) . '</label>';

			printf(
				'<input type="%s" id="sts_options[%s][%s][%s]" value="%s" placeholder="%s" class="regular-text">',
				esc_attr( $sub[ 'type' ] ?? 'text' ),
				esc_attr( $group ),
				esc_attr( $field[ 'key' ] ),
				esc_attr( $sub_key ),
				esc_attr( $value ),
				esc_attr( $placeholder )
			);

			echo '</div>';
		}

		echo '</fieldset>';
		return;
	}


	if ( $field[ 'type' ] === 'textarea' ) {
		printf(
			'<textarea class="regular-text" name="sts_options[%s][%s]" rows="5" cols="50" placeholder="%s">%s</textarea>',
			esc_attr( $group ),
			esc_attr( $key ),
			esc_attr( $placeholder ),
			esc_textarea( $value )
		);

		// ## output description (if it exists)
		if ( ! empty( $field[ 'description' ] ) ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $field[ 'description' ] )
			);
		}

		if ( strpos( $key, 'logo' ) !== false && ! empty( $value ) ) : $svg_data_uri = 'data:image/svg+xml;base64,' . base64_encode( $value ); ?>
			<div class="sts-svg-preview">
				<span>Preview:</span>
				<img src="<?= esc_attr($svg_data_uri) ?>">
			</div>
		<?php endif;
	} else {
		printf(
			'<input type="%s" name="sts_options[%s][%s]" value="%s" class="regular-text" placeholder="%s">',
			esc_attr( $field[ 'type' ] ?? 'text' ),
			esc_attr( $group ),
			esc_attr( $key ),
			esc_attr( $value ),
			esc_attr( $placeholder ),
		);

		// ## output description (if it exists)
		if ( ! empty( $field[ 'description' ] ) ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $field[ 'description' ] )
			);
		}
	};
}


// @@ ENQUEUE CSS FOR STS ADMIN PAGE
add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( $hook !== 'toplevel_page_simple-theme-settings' ) {
			return;
	}

	wp_enqueue_style(
		'sts-admin-style',
		plugin_dir_url( __FILE__ ) . 'css/sts-admin.css',
		[],
		'1.0'
	);
});
