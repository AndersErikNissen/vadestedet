<?php 
get_header(); 

$acf_key        = 'section_event_information_event_information_block_date';
$current_date   = date( 'Ymd' );
$posts_per_page = get_option( 'posts_per_page' ) ?? 12;
$paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$future_query = new WP_Query( [
  'post_type'      => 'event',
  'posts_per_page' => $posts_per_page,
  'paged'          => $paged,
  'meta_key'       => $acf_key,
  'orderby'        => 'meta_value_num',
  'order'          => 'ASC',
  'meta_query'     => [
    [
      'key'     => $acf_key,
      'value'   => $current_date,
      'compare' => '>=',
      'type'    => 'NUMERIC',
    ]
  ]
] );

echo '<section class="section-events section">';
  echo '<div class="pw:wrapper">';
    get_template_part( 'template-parts/snippets/archive-header' );

    if ( $future_query->have_posts() ) {
      echo '<div class="grid">';
        while ( $future_query->have_posts() ) {
          $future_query->the_post();

          get_template_part( 'template-parts/blocks/card', null, [ 
            'class' => 'clmns-12/12 laptop:clmns-6/12' 
          ] ); 
        }
      echo '</div>';

      $links = paginate_links( array(
        'total'     => $future_query->max_num_pages,
        'current'   => $paged,
        'prev_text' => get_theme_string( 'Tidligere side' ),
        'next_text' => get_theme_string( 'Næste side'     ),
      ) ); 

      if ( $links ) {
        echo '<nav class="pagination" aria-label="Pagination">';
          echo $links;
        echo '</nav>';
      } 

      wp_reset_postdata();
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
      name:        sts_option( 'archive.event.heading' ), 
      description: sts_option( 'archive.event.description' ),
      is_archive:  true
    )
] );

get_footer();