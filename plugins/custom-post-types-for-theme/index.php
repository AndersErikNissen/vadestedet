<?php

/**
 * Plugin Name: Custom Post Types
 * Description: This plugin will create the required Custom Post Type(s) required for the theme.
 * Version: 1.0
 * Author: AENDERS.DK
 * Author URI: https://aenders.dk
 */

add_action( 'init', function() {
  register_post_type( 'ctf', 
    array(
      'labels' => array(
        'name'          => __( 'ctfs', 'textdomain' ),
        'singular_name' => __( 'ctf', 'textdomain' ),
      ),
      'public'       => true,
      'has_archive'  => false,
      'show_ui'      => true,
      'show_in_menu' => true,
      'show_in_rest' => false,
      // 'menu_icon'    => '', // dashicon
      'supports'     => array( 'title', 'custom-fields' ),
      'rewrite'      => [
        'slug' => 'ctfs',
      ],
    )   
  );
} );