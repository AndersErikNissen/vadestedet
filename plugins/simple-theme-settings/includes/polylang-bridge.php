<?php
if ( ! defined( 'ABSPATH' ) ) exit;


// @@ STS POLYLANG BRIDGE
add_action('init', function() {
  if ( ! function_exists('pll_register_string') ) return;

  $options  = get_option( 'sts_options', [] );
  $fields   = sts_get_fields_definition();
  $sections = sts_get_sections_definition();

  if ( empty( $fields ) ) return;

  foreach ( $fields as $field ) {
    if ( isset( $field[ 'translate' ] ) && $field[ 'translate' ] === false ) {
      continue;
    }

    $group_id     = $field[ 'group' ] ?? 'general';
    $section_name = $sections[ $group_id ] ?? ucfirst( $group_id );
    $context      = 'STS: ' . $section_name;

    // ## 1. HANDLE REPEATERS (Dynamic rows)
    if ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'repeater' ) {
      $repeater_data = $options[ $group_id ][ $field[ 'key' ] ] ?? [];
      
      if ( ! empty( $repeater_data ) && is_array( $repeater_data ) ) {
        foreach ( $repeater_data as $row_index => $row_values ) {
          foreach ( $field[ 'fields' ] as $sub_field ) {
            // ## skip sub-fields marked as non-translatable
            if ( isset( $sub_field[ 'translate' ] ) && $sub_field[ 'translate' ] === false ) {
              continue;
            }

            $val = $row_values[ $sub_field[ 'key' ] ] ?? '';
            
            if ( ! empty( $val ) && is_string( $val ) ) {
              $name = $field['label'] . ' [' . $row_index . ']: ' . $sub_field['label'];
              pll_register_string( $name, $val, $context, true );
            }
          }
        }
      }
    } 

    // ## 2. HANDLE NESTED GROUPS (Fixed children)
    elseif ( isset( $field[ 'type' ] ) && $field[ 'type' ] === 'group' && ! empty( $field[ 'fields' ] ) ) {
      foreach ( $field[ 'fields' ] as $sub_field ) {
        // ## skip sub-fields marked as non-translatable
        if ( isset( $sub_field[ 'translate' ] ) && $sub_field[ 'translate' ] === false ) {
          continue;
        }

        $val = $options[ $group_id ][ $field[ 'key' ] ][ $sub_field[ 'key' ] ] ?? '';
        if ( ! empty( $val ) && is_string( $val ) ) {
          pll_register_string( $field[ 'label' ] . ': ' . $sub_field[ 'label' ], $val, $context, true );
        }
      }
    } 

    // ## 3. HANDLE STANDARD FIELDS
    else {
      $val = $options[ $group_id ][ $field[ 'key' ] ] ?? '';
      if ( ! empty( $val ) && is_string( $val ) ) {
        pll_register_string( $field[ 'label' ], $val, $context, true );
      }
    }
  }
});