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
    'has_archive'        => _x( 'events', 'URL slug', 'textdomain' ), 
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_rest'       => false,
    'menu_icon'          => 'dashicons-heart',
    'supports'           => [ 'title', 'custom-fields' ],
    'rewrite'            => [
      'slug'       => _x( 'events', 'URL slug', 'textdomain' ),
      'with_front' => false,
      'feeds'      => false,
      'pages'      => true
    ],
    'query_var'          => true,
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
    'rewrite'            => [
      'slug'       => 'menu',
      'with_front' => false,
      'feeds'      => false,
      'pages'      => true
    ],
    'query_var'          => true,
  ] );
  
  register_post_type( 'boardgame', [
    'labels'             => [
        'name'          => __( 'Brætspil', 'textdomain' ),
        'singular_name' => __( 'Brætspil',   'textdomain' ),
    ],
    'public'             => true,
    'publicly_queryable' => true,
    'has_archive'        => true, 
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_rest'       => false,
    'menu_icon'          => 'dashicons-buddicons-activity',
    'supports'           => [ 'title', 'custom-fields' ],
    'rewrite'            => [
      'slug'       => 'braetspil',
      'with_front' => false,
      'feeds'      => false,
      'pages'      => true
    ],
    'query_var'          => true,
  ] );
} );