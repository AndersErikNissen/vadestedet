<?php
if ( ! defined( 'ABSPATH' ) ) exit;


// @@ STS POLYLANG BRIDGE
add_action('init', function() {
  // ## only proceed if polylang is active
  if ( ! function_exists('pll_register_string') ) return;

  $options  = get_option( 'sts_options', [] );
  $fields   = sts_get_fields_definition();
  $sections = sts_get_sections_definition();

  if ( empty( $fields ) ) return;

  foreach ( $fields as $field ) {
    // ## skip if the field is explicitly marked as non-translatable
    if ( isset( $field[ 'translate' ] ) && $field[ 'translate' ] === false ) {
      continue;
    }

    $group_id = $field[ 'group' ] ?? 'general';
    $section_name = $sections[ $group_id ] ?? ucfirst( $group_id );
    $context = 'STS: ' . $section_name;

    // ## handle nested groups
    if ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'group' && ! empty( $field[ 'fields' ] ) ) {
      foreach ( $field[ 'fields' ] as $sub_field ) {
          $val = $options[ $group_id ][ $field[ 'key' ] ][ $sub_field[ 'key' ] ] ?? '';
          
          // ## only register if value exists and isn't a logo
          if ( ! empty( $val ) ) {
            pll_register_string( $field[ 'label' ] . ': ' . $sub_field[ 'label' ], $val, $context, true );
          }
      }
    } 
    // ## handle standard fields (text, textarea, email, etc.)
    else {
        $val = $options[ $group_id ][ $field[ 'key' ] ] ?? '';

        if ( ! empty( $val ) ) {
          pll_register_string( $field[ 'label' ], $val, $context, true );
        }
    }
  }
});