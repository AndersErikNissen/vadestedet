<?php
// @@ GET THEME OPTION
function get_sts_option( string $key, $default = null ) {
	$options = get_option( 'sts_options', [] );

	if ( ! is_array( $options ) ) {
		return $default;
	}

	// ## support dotted syntax: "contact.phone"
	$keys = explode( '.', $key );

	$value = $options;

	foreach ( $keys as $k ) {
		if ( ! is_array( $value ) || ! array_key_exists( $k, $value ) ) {
			return $default;
		}

		$value = $value[ $k ];
	}

  // ## if polylang (plugin) is active
  if ( function_exists( 'pll__' ) && is_string( $value ) ) {
    $translated = pll__( $value );
    
    // ## if the translation exists and isn't empty, use it.
    return ( ! empty( $translated ) ) ? $translated : $value;
  }

	return $value;
}