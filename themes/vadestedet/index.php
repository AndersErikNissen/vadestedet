<?php 
get_header();

echo '<section class="section-index section">';
  echo '<div class="pw:wrapper">';
    get_template_part( 'template-parts/snippets/archive-header' );

    if ( have_posts() ) {
      $links = paginate_links( array(
        'prev_text' => get_theme_string( 'Tidligere side' ),
        'next_text' => get_theme_string( 'Næste side'     ),
      ) );
      
      echo '<div class="grid">';
        while ( have_posts() ) {
          the_post();

          get_template_part( 'template-parts/blocks/card', null, [ 'class' => 'clmns-12/12 laptop:clmns-6/12' ] );    
        }
      echo '</div>';

      if ( $links ) {
        echo '<nav class="pagination" aria-label="Pagination">';
          echo $links;
        echo '</nav>';
      }
    } else {
      echo '<div class="py-2">';
        echo '<p class="h4">' . get_theme_string( 'Vi kunne desværre ikke finde nogen resultater' ) . '</p>';
      echo '</div>';
    }
  echo '</div>';
echo '</section>';

sts_schema_graph( [
    sts_schema_website(),
    sts_schema_webpage(  
      name:        sts_option( 'archive.post.heading' ), 
      description: sts_option( 'archive.post.description' ),
      is_archive:  true
    )
] );

get_footer();

