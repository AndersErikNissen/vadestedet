<?php
get_header();

// Get the data for the actual home page
$home_page_id = get_option( 'page_for_posts' );

$home_page_query = new WP_Query( array(
  'p'         => $home_page_id,
  'post_type' => 'page',
) );

if ( $home_page_query->have_posts() ) {
  while ( $home_page_query->have_posts() ) {
    $home_page_query->the_post();

    // Home page...
  }

  wp_reset_postdata();
}

if ( have_posts() ) {
  while ( have_posts() ) {  
    the_post();
  
    // Single post...
  }
}

get_footer();