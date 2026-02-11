<?php

/**
 * Plugin Name: Custom Post Types
 * Description: This plugin will create the required Custom Post Type(s) required for the theme.
 * Version: 1.0
 * Author: AENDERS.DK
 * Author URI: https://aenders.dk
 */

add_action( 'init', function() {
  register_post_type( 'event', 
    array(
      'labels' => array(
        'name'          => __( 'Events', 'textdomain' ),
        'singular_name' => __( 'Event', 'textdomain' ),
      ),
      'public'       => true,
      'has_archive'  => false,
      'show_ui'      => true,
      'show_in_menu' => true,
      'show_in_rest' => false,
      'menu_icon'    => 'dashicons-heart',
      'supports'     => array( 'title', 'custom-fields' ),
      'rewrite'      => [
        'slug' => 'events',
      ],
    )   
  );

  register_post_type( 'arrangement', 
    array(
      'labels' => array(
        'name'          => __( 'Arrangementer', 'textdomain' ),
        'singular_name' => __( 'Arrangement', 'textdomain' ),
      ),
      'public'       => true,
      'has_archive'  => false,
      'show_ui'      => true,
      'show_in_menu' => true,
      'show_in_rest' => false,
      'menu_icon'    => 'dashicons-buddicons-groups',
      'supports'     => array( 'title', 'custom-fields' ),
      'rewrite'      => [
        'slug' => 'arrangementer',
      ],
    )   
  );
} );