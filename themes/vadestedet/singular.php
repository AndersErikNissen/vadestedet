<?php
get_header();

if ( have_posts() ) {

  while ( have_posts() )  {
    the_post();

    $post_type = get_post_type();
    $is_event  = $post_type === 'event';
    $is_page   = $post_type === 'page';
    $is_post   = $post_type === 'post';

    $block_relation = 'section_' . $post_type . '_information_post_description_block_';

    if ( $is_page ) {
      $block_relation = 'section_page_page_description_block_';
    }
    
    $schemas   = [
      sts_schema_website(),
      sts_schema_webpage( 
        subtype:     $is_page ? get_field( $block_relation . 'page_type' ) : 'WebPage',
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

    if ( $is_event ) {
      get_template_part( 'template-parts/sections/next-events' );
      $schemas[] = sts_schema_event();
    }

    if ( $is_post ) {
      $schemas[] = sts_schema_blog_posting();
    }

    sts_schema_graph( $schemas );
  }

}

get_footer();
