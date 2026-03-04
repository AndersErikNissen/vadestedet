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

get_template_part( 'template-parts/sections/next-events', null, [ 'post_count' => 4 ] );

get_template_part( 'template-parts/snippets/schema', null, [ 'type' => 'company' ] );

get_footer();