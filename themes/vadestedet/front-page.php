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

sts_schema_graph( [
  sts_schema_restaurant(),
  sts_schema_website(),
  sts_schema_webpage( 
    name:        sts_option( 'company.name' )  ?: get_field( 'section_text_and_image_text_block_heading' ), 
    description: get_bloginfo( 'description' ) ?: get_field( 'section_text_and_image_text_block_text' )
  ),
  sts_schema_faqpage(),
] );

get_footer();