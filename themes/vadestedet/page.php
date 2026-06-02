<?php
get_header();

if ( have_posts() ) {

  while ( have_posts() )  {
    the_post();

    $block_relation = 'section_page_page_description_block_';
    
    
    $schemas   = [
      sts_schema_website(),
      sts_schema_webpage( 
        subtype:     get_field( $block_relation . 'page_type' ),
        name:        get_field( $block_relation . 'heading' ),
        description: get_field( $block_relation . 'short_description' )
      )
    ];

    $groups = acf_get_field_groups( [ 'post_id' => get_the_ID() ] );

    foreach( $groups as $group ) {
      $acfgg   = $group[ 'acfgg' ];
      $section = $acfgg[ 'section' ];
      $path    = 'template-parts/sections/' . $section;

      if ( locate_template( $path . '.php' ) ) {
        get_template_part( $path, null, [ 'relation' => $acfgg[ 'relation' ] ] );
      }
    }

    sts_schema_graph( $schemas );
  }

}

get_footer();
