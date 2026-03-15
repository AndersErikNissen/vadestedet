<?php

/**
 * Plugin Name: Custom Post Types
 * Description: This plugin will create the required Custom Post Type(s) required for the theme.
 * Version: 1.0
 * Author: AENDERS.DK
 * Author URI: https://aenders.dk
 */

add_action( 'init', function() {
  register_post_type( 'event', [
    'labels'             => [
      'name'          => __( 'Events', 'textdomain' ),
      'singular_name' => __( 'Event', 'textdomain' ),
    ],
    'public'             => true,
    'has_archive'        => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_rest'       => false,
    'menu_icon'          => 'dashicons-heart',
    'supports'           => [ 'title', 'custom-fields' ],
    'rewrite'            => [ 'slug' => 'events' ],
  ] );

  register_post_type( 'menu', [
    'labels'             => [
        'name'          => __( 'Menu', 'textdomain' ),
        'singular_name' => __( 'Menu',   'textdomain' ),
    ],
    'public'             => true,
    'publicly_queryable' => true,
    'has_archive'        => true, 
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_rest'       => false,
    'menu_icon'          => 'dashicons-food',
    'supports'           => [ 'title', 'custom-fields' ],
    'rewrite'            => [ 'slug' => 'menu' ],
  ] );

  // register_post_type( 'arrangement', 
  //   array(
  //     'labels' => array(
  //       'name'          => __( 'Arrangementer', 'textdomain' ),
  //       'singular_name' => __( 'Arrangement', 'textdomain' ),
  //     ),
  //     'public'       => true,
  //     'has_archive'  => false,
  //     'show_ui'      => true,
  //     'show_in_menu' => true,
  //     'show_in_rest' => false,
  //     'menu_icon'    => 'dashicons-buddicons-groups',
  //     'supports'     => array( 'title', 'custom-fields' ),
  //     'rewrite'      => [
  //       'slug' => 'arrangementer',
  //     ],
  //   )   
  // );
} );


// @@ BLOCK SHOWING A SINGLE MENU POST
add_action( 'template_redirect', function () {
  if ( is_singular( 'menu' ) ) {
    wp_redirect( get_post_type_archive_link( 'menu' ), 301 );
    exit;
  }
} );