<?php
/**
 * STS Admin Page & Settings Logic
 */

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
    'header'     => 'Header indhold',
    'footer'     => 'Footer indhold',
    'ui'         => 'Brugerflade tekst',
    'archive'    => 'Arkiv sider',
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

  // ## fetch fields from the config file
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
function sts_sanitize_options( $input ) {
  if ( ! is_array( $input ) ) return [];

  $clean = [];

  foreach ( $input as $key => $value ) {
    if ( is_array( $value ) ) {
      // ## if it's an array (a group), clean its children
      $clean[ $key ] = sts_sanitize_options( $value );
    } else {
      // ## ff it's a string, clean it based on the key name
      $clean[ $key ] = sts_process_single_value( (string) $key, $value );
    }
  }
  
  return $clean;
}

// ## helper to handle the actual cleaning of strings
function sts_process_single_value( $key, $value ) {
  $result = sanitize_text_field( $value );

  // ## allow svg for anything with "logo" in the key
  if ( ! empty($key) && strpos( $key, 'logo' ) !== false ) {
    $allowed_tags = [
      'svg' => [ 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'id' => true, 'role' => true, 'aria-hidden' => true ],
      'path' => [ 'd' => true, 'fill' => true, 'stroke' => true ],
      'circle' => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true ],
      'g' => [ 'id' => true, 'fill' => true, 'transform' => true ],
      'defs' => [], 'use' => [ 'xlink:href' => true, 'href' => true ],
      'rect' => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true ],
    ];

    $result = wp_kses( $value, $allowed_tags );
  }

  return $result;
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

// @@ 5. RENDER FIELDS 
function sts_render_field( $field ) {
  $options = get_option( 'sts_options', [] );
  $group = $field[ 'group' ] ?? '';
  $key = $field[ 'key' ] ?? '';
  $placeholder = $field[ 'placeholder' ] ?? '';

  // ## handle nested groups
  if ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'group' ) {
    echo '<fieldset class="sts-field-group">';
    echo '<legend style="font-weight:bold; padding:0 5px;">' . esc_html( $field['label'] ) . '</legend>';

    foreach ( $field['fields'] as $sub ) {
      $sub_key = $sub[ 'key' ];
      $value = $options[ $group ][ $key ][ $sub_key ] ?? '';

      echo '<div style="margin-bottom:8px;">';
        echo '<label style="display:block; font-size:12px;">' . esc_html($sub['label']) . '</label>';
        if ( $sub[ 'type' ] === 'textarea' ) {
          printf(
            '<textarea class="regular-text" name="sts_options[%s][%s]" rows="5" placeholder="%s">%s</textarea>',
            esc_attr( $group ), 
            esc_attr( $sub_key ),
            esc_attr( $sub[ 'placeholder' ] ),
            esc_textarea( $value )
          );
        } else {
          printf(
            '<input type="%s" name="sts_options[%s][%s][%s]" value="%s" placeholder="%s" class="regular-text">',
            esc_attr( $sub[ 'type' ] ?? 'text' ),
            esc_attr( $group ),
            esc_attr( $key ),
            esc_attr( $sub_key ),
            esc_attr( $value ),
            esc_attr( $sub[ 'placeholder' ] ?? '' )
          );
        }
      echo '</div>';
    }
    echo '</fieldset>';
    return;
  }

  // @@ STANDARD FIELDS (TEXTAREA / TEXT)
  $value = $options[ $group ][ $key ] ?? '';

  if ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'textarea' ) {
    printf(
      '<textarea class="regular-text" name="sts_options[%s][%s]" rows="5" placeholder="%s">%s</textarea>',
      esc_attr( $group ), esc_attr( $key ), esc_attr( $placeholder ), esc_textarea( $value )
    );
  } else {
    printf(
      '<input type="%s" name="sts_options[%s][%s]" value="%s" class="regular-text" placeholder="%s">',
      esc_attr( $field[ 'type' ] ?? 'text' ), esc_attr( $group ), esc_attr( $key ), esc_attr( $value ), esc_attr( $placeholder )
    );
  }
}

// @@ 6. ENQUEUE ASSETS
add_action( 'admin_enqueue_scripts', function( $hook ) {
  if ( $hook !== 'toplevel_page_simple-theme-settings' ) return;
  wp_enqueue_style( 'sts-admin-style', plugin_dir_url( dirname(__FILE__) ) . 'css/sts-admin.css' );
});