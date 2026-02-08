<?php
/**
 * Plugin Name: Advanced Custom Fields - Group Generator (ACFGG)
 * Description: This plugin is creating field groups for the plugin ACF (Advanced Custom Fields), so you don't have to create them manually. You are free to add your own, and if you don't want these premade ones, just disable/uninstall this plugin.
 * Version: 1.0
 * Author: AENDERS.DK
 * Author URI: https://aenders.dk
 */


// @@ HIDE ACF FROM BACKEND
add_filter( 'acf/settings/show_admin', '__return_false' );


// @@ OPTIONS-PAGE REDIRECT
add_action( 'template_redirect', function() {
  if ( is_page( 'indstillinger' ) || is_page( 'options' ) ) {
    wp_redirect( home_url() );
    exit();
  }
} );


// @@ OPTIONS-PAGE NOINDEX
add_action( 'wp_head', function() {
  if ( is_page( 'indstillinger' ) || is_page( 'options' ) ) : ?>
    <meta name="robots" content="noindex, nofollow" />
  <? endif;
} );


// @@ GENERATE UNIQUE KEY
function acfgg_key( $relation, $name, $context = 'field_' ):string {
  return $context . substr( md5( $relation . '_' . $name ), 0, 12);
}


// @@ GENERATE FIELD
function acfgg_field( $relation, $label, $name, $type, $args = [] ):array {
  return array_merge( [
    'key'   => acfgg_key( $relation, $name ),
    'label' => $label,
    'name'  => $relation . $name,
    'type'  => $type,
  ], $args );
}


// @@ GENERATE ACCORDION
function acfgg_accordion( $relation, $name, $instructions = '', $open = 0 ):array {
  return [
    'key'   => acfgg_key( $relation, $name ),
    'label' => $name,
    'type'  => 'accordion',
    'open'  => $open,
    'instructions' => $instructions,
  ];
}


// @@ GENERATE BLOCK
function acfgg_block( $type, $relation ):array {
  $block = [];

  switch ( $type ) {
    case 'text':
      $block[] = acfgg_accordion( $relation . 'tab_', 'Tekst indhold' );

      $block[] = acfgg_field( $relation, 'Overskrift', 'heading', 'text'     );
      $block[] = acfgg_field( $relation, 'Tekst',      'text',    'textarea' );
      $block[] = acfgg_field( $relation, 'Knap',       'button',  'link'     );
      break;
    
    case 'image':
      $image_ratio_args = [
        'choices' => [
          'default'     => 'Original',
          '4:5'         => 'Portræt',
          '1:1'         => 'Kvadratisk',
          '16:9'        => 'Landskab',
          '4:1.5'       => 'Banner'
        ],
        'default_value' => 'default'
      ];

      $image_first_args = [
        'choices'  => [
          'default'        => 'Ingen',
          'desktop'        => 'Computer',
          'mobile'         => 'Mobil',
          'desktop/mobile' => 'Computer & Mobil',
        ],
        'default_value'    => 'default'
      ];

      $block[] = acfgg_accordion( $relation . 'tab_', 'Billede opsætning' );

      $block[] = acfgg_field( $relation, 'Vælg billede',                  'image',               'image'                                               );
      $block[] = acfgg_field( $relation, 'Vælg billedformat (Computer)',  'image_ratio_desktop', 'button_group', $image_ratio_args );
      $block[] = acfgg_field( $relation, 'Vælg billedformat (Mobil)',     'image_ratio_mobile',  'button_group', $image_ratio_args );  
      $block[] = acfgg_field( $relation, 'Vis billede først på',          'image_first',         'button_group', $image_first_args );
      break;
  };

  return $block;
}

// @@ GENERATE LOCATION
function acfgg_location( $locations ):array {
  $selected_locations = [];

  $awailable_locations = [
    'frontpage' => [
      "param"    => "page_type",
      "operator" => "==",
      "value"    => "front_page"
    ],
    'post' => [
      'param'    => 'post_type',
      'operator' => '==',
      'value'    => 'post',
    ],
    'page' => [
      'param'    => 'post_type',
      'operator' => '==',
      'value'    => 'page',
    ],
  ];

  foreach( $locations as $location ) {
    $available_location = ( $awailable_locations[ $location ] ?: false );

    if ( $available_location ) {
      array_push( $selected_locations, $available_location );
    }
  }

  return $selected_locations;
}


// @@ GENERATE GROUP
function acfgg_group( $relation, $name, $section, $fields, $location, $menu_order = 9 ) {
  acf_add_local_field_group( [
    'key'        => acfgg_key( $relation, $name, 'group_' ),
    'title'      => $name,
    'acfgg' => [ // Custom key, used only by this plugin
      'relation' => $relation,
      'section'  => $section,
    ], 
    'fields'     => $fields,
    'location'   => $location,
    'menu_order' => $menu_order,
  ] );
}


// @@ GENERATE FIELDS FOR OPTIONS-PAGE
function acfgg_option_sections() {

}


// @@ CREATE ALL SECTIONS
function acfgg_sections() {
  // ## text and image
  $relation = 'section_text_and_image_';

  acfgg_group( 
    $relation, 
    'Sektion: Tekst & billede', 
    'text-and-image',
    array_merge(
      acfgg_block( 'text',  $relation ),
      acfgg_block( 'image', $relation ),
    ), [
      acfgg_location( [ 'post' ] ),
      acfgg_location( [ 'page' ] ),
    ], 
  );

  // ## text and image 2
  $relation = 'section_text_and_image_2_';

  acfgg_group( 
    $relation, 
    'Sektion: Tekst & billede 2', 
    'text-and-image',
    array_merge(
      acfgg_block( 'text', $relation ),
      acfgg_block( 'image', $relation ),
    ), [
      acfgg_location( [ 'post' ] ),
      acfgg_location( [ 'page' ] ),
  ] );
}

add_action('acf/init', 'acfgg_sections');
