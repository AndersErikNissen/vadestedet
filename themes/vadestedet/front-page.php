<?php
get_header();

if ( have_posts() ) {

  while ( have_posts() ) {  
    the_post();

    $groups = acf_get_field_groups( [ 'post_id' => get_the_ID() ] );

    foreach( $groups as $group ) {
      $acfgg = $group[ 'acfgg' ];
      $section = $acfgg[ 'section' ];
      $path = 'template-parts/sections/' . $section;

      if ( locate_template( $path . '.php' ) ) {
        get_template_part( $path, null, [ 'relation' => $acfgg[ 'relation' ] ] );
      }
    }

  } 

}

// get_template_part( 'template-parts/sections/next-events', null, [ 'post_count' => 4 ] );

sts_schema_graph( [
  sts_schema_restaurant(),
  sts_schema_website(),
  sts_schema_webpage( 
    name:        sts_option( 'company.name' )  ?: get_field( 'section_text_and_image_text_block_heading' ), 
    description: get_bloginfo( 'description' ) ?: get_field( 'section_text_and_image_text_block_text' )
  ),
  sts_schema_faqpage(),
] );


// if ( $type === 'event' ) {
//     sts_schema_graph( [
//         sts_schema_event(),
//     ] );
// }

// if ( $type === 'post' ) {
//     sts_schema_graph( [
//         sts_schema_blog_posting(),
//     ] );
// }

// if ( $type === 'page' ) {
//     // Pass breadcrumb items as the second argument using your ACF field names.
//     // Omit the second argument entirely on pages that don't need a breadcrumb.
//     //
//     // Example:
//     //   sts_schema_webpage( 'AboutPage', [
//     //       [ 'name' => get_field( 'hero_title', $home_id ), 'url' => home_url() ],
//     //       [ 'name' => get_field( 'hero_title' ),           'url' => get_permalink() ],
//     //   ] );
//     sts_schema_graph( [
//         sts_schema_webpage(),
//     ] );
// }


get_footer();