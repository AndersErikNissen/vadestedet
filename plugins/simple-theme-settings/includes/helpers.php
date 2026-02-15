<?php
// @@ GET THEME OPTION
function sts_option( string $key, $default = null ) {
	$options = get_option( 'sts_options', [] );

	if ( ! is_array( $options ) ) return $default;

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
	if ( function_exists('pll__') && is_string( $value ) ) {
			
		// ## find the field in the config to check the 'translate' flag
		$fields = sts_get_fields_definition();
		$is_translatable = true;

		foreach ( $fields as $field ) {
			if ( isset( $field[ 'key' ] ) && $field[ 'key' ] === end( $keys ) ) {
				if ( isset($field[ 'translate' ] ) && $field[ 'translate' ] === false ) {
					$is_translatable = false;
				}

				break;
			}
		}

		if ( $is_translatable ) {
			$translated = pll__( $value );
			return ( ! empty($translated) ) ? $translated : $value;
		}
	}
  

	return $value;
}