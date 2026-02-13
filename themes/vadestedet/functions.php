<?php
// @@ INCLUDES
include get_theme_file_path( '/assets/php/utility.php' );


/* @@ HIDE ADMIN-BAR */
add_filter( 'show_admin_bar', '__return_false' );


// @@ LOAD STYLING
add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_style( 
    'theme-style', 
    get_theme_file_uri() . '/assets/css/theme-style.css',
    [],
    wp_get_theme()->get( 'Version' )
  );
} );


// @@ PRELOAD FONT
add_action( 'wp_head', function () { ?>
  <link
    rel="preload"
    href="<?= esc_url( get_theme_file_uri() . '/assets/fonts/primary/inter-variable.woff2' ); ?>"
    as="font"
    type="font/woff2"
    crossorigin
  >
<?php }, 1 );


// @@ SCRIPT(S)
add_action( 'get_footer', function() {
  wp_enqueue_script( 'main', get_theme_file_uri( 'assets/js/main.js' ), array(), "1.0", TRUE );
} );


// @@ REWRITE THE URL-BASE FOR PAGINATION
add_action('init', function() {
  global $wp_rewrite;
  $wp_rewrite->pagination_base = 'side';
}, 1);


// @@ REMOVE EDITOR FROM PAGES / POSTS
add_action( 'admin_init', function() {
  remove_post_type_support( 'page', 'editor' );
  remove_post_type_support( 'post', 'editor' );
} );


// @@ CORE 
add_action( 'after_setup_theme', function() {
  add_theme_support (
    'html5',
    array (
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
      'script',
      'style',
      'navigation-widgets',
    )
  );

  // Adds <title> to <head>
  add_theme_support( 'title-tag' );

  // Extra images sizes
  add_image_size( 'phone',             480 );
  add_image_size( 'medium-large',      768 );
  add_image_size( 'tablet-landscape', 1024 );
  add_image_size( 'laptop',           1440 );
  add_image_size( 'xlarge',           1920 );

  // ## for schema.org
  add_image_size('schema_1x1', 1200, 1200, true);
  add_image_size('schema_4x3', 1200, 900, true);
  add_image_size('schema_16x9', 1200, 675, true);
} );
