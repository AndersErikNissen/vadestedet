<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// @@ 1. CREATE THE ADMIN MENU PAGE
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

// @@ 2. REGISTER SETTINGS
function sts_get_sections_definition() {
  return [
    'company'    => 'Virksomheds information',
    'contact'    => 'Kontakt information',
    'hours'      => 'Åbningstidpuntker',
    'header'     => 'Header indhold',
    'footer'     => 'Footer indhold',
    'ui'         => 'Brugerflade tekst',
    'archive'    => 'Arkiv sider',
    'page'       => 'Sider',
    'inject'     => 'Indsæt scripts',
  ];
}

add_action( 'admin_init', function() {
  register_setting(
    'sts_options_group',
    'sts_options',      
    [
      'type' => 'array',
      'sanitize_callback' => 'sts_sanitize_options',
      'default' => [],
    ],
  );

  $sections = sts_get_sections_definition();

  foreach ( $sections as $id => $title ) {
    add_settings_section( "sts_section_{$id}", $title, '__return_false', 'sts-theme-settings' );
  }

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

// @@ 3. UNIVERSAL SANITIZER
function sts_sanitize_options( $input, $parent_key = '' ) {
  if ( ! is_array( $input ) ) return [];
  $clean = [];
  foreach ( $input as $key => $value ) {
    if ( is_array( $value ) ) {
      $clean[ $key ] = sts_sanitize_options( $value, $key );
    } else {
      $clean[ $key ] = sts_process_single_value( $key, $value, $parent_key );
    }
  }
  return $clean;
}

function sts_process_single_value( $key, $value, $parent_key = '' ) {
  $value = (string) $value;
  
  if ( $parent_key === 'inject' ) {
    return wp_unslash( $value );
  }

  if ( strpos( $key, 'logo' ) !== false ) {
     $allowed_tags = [
      'svg' => [ 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'id' => true, 'role' => true, 'aria-hidden' => true ],
      'path' => [ 'd' => true, 'fill' => true, 'stroke' => true ],
      'circle' => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true ],
      'g' => [ 'id' => true, 'fill' => true, 'transform' => true ],
      'defs' => [], 'use' => [ 'xlink:href' => true, 'href' => true ],
      'rect' => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true ],
    ];
     return wp_kses( $value, $allowed_tags );
  }
  return wp_kses_post( $value );
}

// @@ 4. RENDER PAGE
function sts_render_options_page() { ?>
  <section class="simple-theme-settings wrap">
    <h1>Tema indstillinger (STS)</h1>
    <form method="post" action="options.php">
      <?php
        settings_fields( 'sts_options_group' );
        do_settings_sections( 'sts-theme-settings' );
        submit_button();
      ?>
    </form>
  </section>
<?php }

// @@ 5. RENDER FIELDS (W. HELPERS)
function sts_render_repeater_row( $field, $index, $values ) {
  $group = $field[ 'group' ];
  $parent_key = $field[ 'key' ];
  
  echo '<div class="sts-repeater-row">';
    foreach ( $field[ 'fields' ] as $sub ) {
      $sub_key = $sub[ 'key' ];
      $val = $values[ $sub_key ] ?? '';
      $type = $sub[ 'type' ] ?? 'text';
      $sub_desc = $sub['description'] ?? '';

      echo '<div class="sts-field-wrap">';
      echo '<label class="sts-label">' . esc_html( $sub[ 'label' ] ) . '</label>';
      
      if ( $type === 'checkbox' ) {
        printf(
          '<input type="checkbox" name="sts_options[%s][%s][%s][%s]" value="1" %s>',
          esc_attr( $group ), esc_attr( $parent_key ), esc_attr( $index ), esc_attr( $sub_key ), checked( 1, $val, false )
        );
      } elseif ( $type === 'radio' ) {
        foreach ( $sub['options'] as $opt_val => $opt_label ) {
          printf(
            '<label style="margin-right:12px;"><input type="radio" name="sts_options[%s][%s][%s][%s]" value="%s" %s> %s</label>',
            esc_attr( $group ), esc_attr( $parent_key ), esc_attr( $index ), esc_attr( $sub_key ),
            esc_attr( $opt_val ), checked( $val, $opt_val, false ), esc_html( $opt_label )
          );
        }
      } elseif ( $type === 'textarea' ) {
        printf(
          '<textarea name="sts_options[%s][%s][%s][%s]" class="large-text" rows="3">%s</textarea>',
          esc_attr( $group ), esc_attr( $parent_key ), esc_attr( $index ), esc_attr( $sub_key ), esc_textarea( $val )
        );
      } else {
        printf(
          '<input type="%s" name="sts_options[%s][%s][%s][%s]" value="%s" class="regular-text">',
          esc_attr( $type ), esc_attr( $group ), esc_attr( $parent_key ), esc_attr( $index ), esc_attr( $sub_key ), esc_attr( $val )
        );
      }

      if ( strpos( $sub_key, 'logo' ) !== false && ! empty( $val ) ) {
        $svg_data_uri = 'data:image/svg+xml;base64,' . base64_encode( $val );
        echo '<div class="sts-svg-preview"><img src="'.esc_attr($svg_data_uri).'" style="max-height:40px;"></div>';
      }

      if ( $sub_desc ) {
        echo '<p class="description">' . wp_kses_post( $sub_desc ) . '</p>';
      }
      echo '</div>';
    }
    echo '<button type="button" class="button sts-remove-row">Fjern</button>';
  echo '</div>';
}
 
function sts_render_field( $field ) {
  $options     = get_option( 'sts_options', [] );
  $group       = $field[ 'group' ] ?? '';
  $parent_key  = $field[ 'key' ] ?? '';
  $placeholder = $field[ 'placeholder' ] ?? '';
  $description = $field[ 'description' ] ?? '';

  // ## repeater rows
  if ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'repeater' ) {
    $values = $options[$group][$parent_key] ?? [];
    echo '<div class="sts-repeater-container">';
      echo '<div class="sts-repeater-rows">';
        if ( ! empty( $values ) ) {
          foreach ( $values as $index => $row_values ) {
            sts_render_repeater_row($field, $index, $row_values);
          }
        }
      echo '</div>';
      echo '<div class="sts-repeater-rows-btns"><button type="button" class="button sts-add-row">Tilføj række</button></div>';
      echo '<script type="text/template" class="sts-repeater-template">';
        sts_render_repeater_row($field, '999', []); 
      echo '</script>';
    echo '</div>';
    return;
  }

  // ## nested groups
  if ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'group' ) {
    echo '<fieldset class="sts-field-group">';
    echo '<legend style="font-weight:bold; padding:0 5px;">' . esc_html( $field['label'] ) . '</legend>';
    foreach ( $field['fields'] as $sub ) {
      $sub_key = $sub[ 'key' ];
      $value = $options[ $group ][ $parent_key ][ $sub_key ] ?? '';
      $sub_desc = $sub['description'] ?? '';

      echo '<div style="margin-bottom:15px;">';
        echo '<label class="sts-label">' . esc_html($sub['label']) . '</label>';
        if ( $sub['type'] === 'checkbox' ) {
          printf('<input type="checkbox" name="sts_options[%s][%s][%s]" value="1" %s>', esc_attr($group), esc_attr($parent_key), esc_attr($sub_key), checked(1, $value, false));
        } elseif ( $sub['type'] === 'radio' ) {
          foreach ( $sub['options'] as $opt_val => $opt_label ) {
            printf(
              '<label style="margin-right:12px;"><input type="radio" name="sts_options[%s][%s][%s]" value="%s" %s> %s</label>',
              esc_attr( $group ), esc_attr( $parent_key ), esc_attr( $sub_key ),
              esc_attr( $opt_val ), checked( $value, $opt_val, false ), esc_html( $opt_label )
            );
          } 
        } elseif ( $sub[ 'type' ] === 'textarea' ) {
          printf('<textarea class="large-text" name="sts_options[%s][%s][%s]" rows="4" placeholder="%s">%s</textarea>', esc_attr($group), esc_attr($parent_key), esc_attr($sub_key), esc_attr($sub['placeholder'] ?? ''), esc_textarea($value));
        } else {
          printf('<input type="%s" name="sts_options[%s][%s][%s]" value="%s" placeholder="%s" class="regular-text">', esc_attr($sub['type'] ?? 'text'), esc_attr($group), esc_attr($parent_key), esc_attr($sub_key), esc_attr($value), esc_attr($sub['placeholder'] ?? ''));
        }

        if ( strpos( $sub_key, 'logo' ) !== false && ! empty( $value ) ) {
          $svg_data_uri = 'data:image/svg+xml;base64,' . base64_encode( $value );
          echo '<div class="sts-svg-preview"><img src="'.esc_attr($svg_data_uri).'" style="max-height:40px;"></div>';
        }
        if ( $sub_desc ) echo '<p class="description">' . wp_kses_post($sub_desc) . '</p>';
      echo '</div>';
    }
    echo '</fieldset>';
    return;
  }

  // ## standard top-level fields
  $value = $options[ $group ][ $parent_key ] ?? '';
  $type  = $field[ 'type' ] ?? 'text';

  if ( $type === 'checkbox' ) {
    printf('<input type="checkbox" name="sts_options[%s][%s]" value="1" %s>', esc_attr($group), esc_attr($parent_key), checked(1, $value, false));
  } elseif ( $type === 'radio' ) {
    foreach ( $field['options'] as $opt_val => $opt_label ) {
      printf(
        '<label style="margin-right:12px;"><input type="radio" name="sts_options[%s][%s]" value="%s" %s> %s</label>',
        esc_attr( $group ), esc_attr( $parent_key ),
        esc_attr( $opt_val ), checked( $value, $opt_val, false ), esc_html( $opt_label )
      );
    }
  } elseif ( $type === 'textarea' ) {
    printf('<textarea class="large-text" name="sts_options[%s][%s]" rows="5" placeholder="%s">%s</textarea>', esc_attr($group), esc_attr($parent_key), esc_attr($placeholder), esc_textarea($value));
  } else {
    printf('<input type="%s" name="sts_options[%s][%s]" value="%s" class="regular-text" placeholder="%s">', esc_attr($type), esc_attr($group), esc_attr($parent_key), esc_attr($value), esc_attr($placeholder));
  }

  if ( strpos( $parent_key, 'logo' ) !== false && ! empty( $value ) ) {
    $svg_data_uri = 'data:image/svg+xml;base64,' . base64_encode( $value );
    echo '<div class="sts-svg-preview"><img src="'.esc_attr($svg_data_uri).'" style="max-height:50px; margin-top:10px;"></div>';
  }

  if ( $description ) {
    echo '<p class="sts-description description">' . wp_kses_post( $description ) . '</p>';
  }
}

// @@ 6. ENQUEUE ASSETS
add_action( 'admin_enqueue_scripts', function( $hook ) {
  if ( $hook !== 'toplevel_page_simple-theme-settings' ) return;
  wp_enqueue_script( 'sts-admin-js', plugin_dir_url( dirname( __FILE__ ) ) . 'js/sts-admin.js', array(), '1.0.0', true );
  wp_enqueue_style( 'sts-admin-style', plugin_dir_url( dirname( __FILE__ ) ) . 'css/sts-admin.css' );
});