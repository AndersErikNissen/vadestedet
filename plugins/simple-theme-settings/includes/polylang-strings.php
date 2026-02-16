<?php
// @@ NOTE: THESE ARE STRINGS THAT SHOULDN'T SHOW UP ON THE STS OPTIONS PAGE, BUT SHOULD STILL BE TRANSLATEABLE.

$register_strings = function ( $name, $strings ) {
  if ( ! function_exists( 'pll_register_string' ) ) return;

  foreach( $strings as $string ) {
    pll_register_string( $name, $string, 'Tema tekst' );  
  };
};

add_action( 'init', function() use ( $register_strings ) {
  // ## strings
  $event_strings = [
    'Event dato',
    'Event tidsramme',
    'Relaterede event(s)',
    'Kommende event(s)'
  ];

  $general_strings = [
    'Se alle',
    'Tidligere side',
    'Næste side',
  ];

  
  // ## register strings
  $register_strings( 'General', $general_strings );
  $register_strings( 'Event', $event_strings );
});