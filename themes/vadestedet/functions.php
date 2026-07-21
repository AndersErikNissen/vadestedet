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
    if ( ! is_post_type_archive() ) {
      return $url;
    }

    $queried_obj = get_queried_object();
    if ( ! $queried_obj || empty( $queried_obj->name ) ) {
      return $url;
    }

    $post_type       = $queried_obj->name;
    $archive_link    = get_post_type_archive_link( $post_type );
    
    if ( ! $archive_link ) {
      return $url;
    }

    $default_lang    = pll_default_language();
    $current_lang    = pll_current_language();

    $default_home = trailingslashit( pll_home_url( $default_lang ) );
    $current_home = trailingslashit( pll_home_url( $current_lang ) );

    $relative = ltrim( str_replace(
      [ $current_home, $default_home ],
      '',
      $archive_link
    ), '/' );

    return trailingslashit( pll_home_url( $slug ) ) . $relative;
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


// @@ DISABLE PAGES (SIKRET MOD REDIRECT-LOOPS)
add_action( 'template_redirect', function () {
  if ( is_front_page() ) {
    return;
  }

  if ( is_singular( 'event' ) ) {
    $link = get_post_type_archive_link( 'event' );
    if ( $link ) { wp_redirect( $link, 301 ); exit; }
  }

  if ( is_singular( 'menu' ) ) {
    $link = get_post_type_archive_link( 'menu' );
    if ( $link ) { wp_redirect( $link, 301 ); exit; }
  }

  if ( is_singular( 'boardgame' ) ) {
    $link = get_post_type_archive_link( 'boardgame' ); 
    if ( $link ) { wp_redirect( $link, 301 ); exit; }
  }

  if ( is_singular( 'post' ) || is_category() || is_tag() ) {
    wp_redirect( home_url(), 301 );
    exit;
  }
} );


// @@ REMOVE POSTS FROM ADMIN
add_action( 'admin_menu', function() {
  remove_menu_page( 'edit.php' );
} );

add_action( 'wp_before_admin_bar_render', function() {
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu( 'new-post' );
} );


// @@ DISABLE FEEDS
add_action( 'do_feed', 'disable_feeds', 1 );
add_action( 'do_feed_rdf', 'disable_feeds', 1 );
add_action( 'do_feed_rss', 'disable_feeds', 1 );
add_action( 'do_feed_rss2', 'disable_feeds', 1 );
add_action( 'do_feed_atom', 'disable_feeds', 1 );
add_action( 'do_feed_rss2_comments', 'disable_feeds', 1 );
add_action( 'do_feed_atom_comments', 'disable_feeds', 1 );

function disable_feeds() {
  wp_redirect( home_url(), 301 );
  exit;
}