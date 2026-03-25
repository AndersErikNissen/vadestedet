<?php
/**
 * Plugin Name: Remove WordPress Comment Feature (Nuclear Option)
 * Description: Disables every single trace of comments from the UI and Database logic.
 * Version: 1.1
 * Author: AENDERS.DK
 */

// 1. Core Redirects and Meta Boxes
add_action( 'admin_init', function () {
  global $pagenow;
  if ( $pagenow === 'edit-comments.php' || $pagenow === 'options-discussion.php' ) {
    wp_safe_redirect( admin_url() );
    exit;
  }

  remove_submenu_page( 'options-general.php', 'options-discussion.php' );
  remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

  foreach ( get_post_types() as $post_type ) {
    if ( post_type_supports( $post_type, 'comments' ) ) {
      remove_post_type_support( $post_type, 'comments' );
      remove_post_type_support( $post_type, 'trackbacks' );
    }
  }
} );

// 2. Disable Frontend Logic
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// 3. Clean Admin Menu
add_action( 'admin_menu', function () {
  remove_menu_page( 'edit-comments.php' );
  remove_submenu_page( 'options-general.php', 'options-discussion.php' );
} );

// 4. Remove Admin Bar Link
add_action( 'wp_before_admin_bar_render', function() {
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu('comments');
});

// 5. Remove Columns from Post/Page Lists
add_filter( 'manage_posts_columns', 'remove_comm_cols' );
add_filter( 'manage_pages_columns', 'remove_comm_cols' );

function remove_comm_cols( $columns ) {
  unset( $columns['comments'] );
  return $columns;
}

// 6. Hide Gutenberg Discussion Panel
add_action( 'enqueue_block_editor_assets', function() {
  wp_add_inline_script( 'wp-edit-post', 'wp.data.dispatch( "core/edit-post" ).removeEditorPanel( "discussion-panel" );' );
});