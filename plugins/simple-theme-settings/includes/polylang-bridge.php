<?php
add_action('init', function() {
  if ( ! function_exists( 'pll_register_string' ) ) return;

  $options = get_option( 'sts_options', [] );
  $fields = sts_get_fields_definition(); // grab the shared field list

  foreach ( $fields as $field ) {
    $val = $options[ $field[ 'group' ] ] [$field[ 'key' ] ] ?? '';
    
    if ( ! empty( $val ) && strpos( $field[ 'key' ], 'logo' ) === false ) {
      pll_register_string( 'STS: ' . $field[ 'label' ], $val, 'Simple Theme Settings', true );
    }
  }
} );