<?php
// @@ INCLUDES
include get_theme_file_path( '/assets/php/helpers.php' );


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
  add_image_size('schema_1x1',  1200, 1200, true);
  add_image_size('schema_4x3',  1200, 900,  true);
  add_image_size('schema_16x9', 1200, 675,  true);
} );


// @@ INJECT SCRIPTS (VIA STS PLUGIN)
add_action( 'wp_head', function() {
  $inject = sts_option( 'inject.head' );
  if ( ! empty( $inject ) ) echo $inject;
});

add_action( 'wp_body_open', function() {
  $inject = sts_option( 'inject.body' );
  if ( ! empty( $inject ) ) echo $inject;
});


// @@ POLYLANG FIX FOR ARCHIVE-PAGE REDIRECTS
if ( function_exists( 'pll_the_languages' ) ) {
  add_filter( 'pll_translation_url', 'fix_archive_translation_url', 10, 2 );
  
  function fix_archive_translation_url( $url, $slug ) {
      if ( is_post_type_archive() ) {
          $post_type    = get_queried_object()->name;
          $current_lang = pll_current_language();
          $archive_link = get_post_type_archive_link( $post_type );
  
          if ( $archive_link && $current_lang ) {
              // Swap the current language segment for the target language
              return str_replace(
                  '/' . $current_lang . '/',
                  '/' . $slug . '/',
                  $archive_link
              );
          }
      }
  
      return $url;
  }
}


// @@ REMOVE SEARCH PAGE
function disable_wp_search( $query, $error = true ) {
    if ( is_search() && ! is_admin() ) {
        $query->is_search = false;
        $query->query_vars['s'] = false;
        $query->query['s'] = false;

        if ( $error ) {
            $query->is_404 = true;
        }
    }
}

add_action( 'parse_query', 'disable_wp_search' );
add_filter( 'get_search_form', '__return_false' );