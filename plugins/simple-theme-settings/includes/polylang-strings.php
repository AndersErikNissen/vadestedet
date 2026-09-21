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
  $general_strings = [
    'Se alle',
    'Tidligere side',
    'Næste side',
    'Vi kunne desværre ikke finde nogen resultater',
    'Reserver bord',
    'Ring til os',
    'Send os en e-mail',
    'Lukket',
    'Åbningstider',
    'Virksomheden',
    'Tilbage',
    'Tilbage til arkiv',
    'Har du spørgsmål?',
    'Scroll',
    'Åben hjemmeside-menu',
    'Luk hjemmeside-menu',
    'Kontakt',
    'Filtrer',
    'Fjern filtre',
    'Menu',
    'Gratis',
    'Navigation',
    'Bestil billet',
    'Se mere',
  ];

  $boardgame_strings = [
    'Kortspil',
    'Strategispil',
    'Selskabsspil',
    'Tile-laying',
    'Tile-placement',
    'Klassiker',
    'Quizspil',
    'Spil',
    'Børnespil',
    'Tilbehør/udvidelse',
    'Eurogame',
    'Coop Detektivspil',
    'Deduktionsspil',
    'Detektivspil',
    'Samarbejdsspil',
    'Familiespil',
    'Engine-building',
    'Racer-/strategispil',
    'Familie',
    'Mysterie-/escape-spil',
    'Coop',
    'Abstrakt',
    'Strategi- / familiespil',
    'Abstrakt spil',
    'Coop Escaperoom',
    'Meget let',
    'Let',
    'Mellem',
    'Svær',
    'Meget svær',
  ];
  
  // ## register strings
  $register_strings( 'General', $general_strings );
  $register_strings( 'Brætspil', $boardgame_strings );
});