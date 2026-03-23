<?php 
get_header(); 

$acf_key = 'section_menu_menu_block_sorting_order';
$paged   = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$menu_query = new WP_Query( [
  'post_type'      => 'menu',
  'posts_per_page' => -1,
  'meta_key'       => 'section_menu_menu_block_sorting_order',
  'orderby'        => 'meta_value_num',
  'order'          => 'ASC',
] );

echo '<section class="section-menu section">';
  echo '<div class="pw:wrapper">';
    get_template_part( 'template-parts/snippets/archive-header' );
    if ( $menu_query->have_posts() ) {
      echo '<div class="column gap-2">';
        while ( $menu_query->have_posts() ) {
          $menu_query->the_post();
          get_template_part( 'template-parts/blocks/menu' ); 
        }
      echo '</div>';

      wp_reset_postdata();
    } else {
      echo '<div class="py-2">';
        echo '<p class="h4">' . get_theme_string( 'Vi kunne desværre ikke finde nogen resultater' ) . '</p>';
      echo '</div>';
    }
  echo '</div>';
echo '</section>';

sts_schema_graph( [
    sts_schema_menu( $menu_query ),
    sts_schema_website(),
    sts_schema_webpage( 
      subtype:     'MenuPage', 
      name:        sts_option( 'archive.menu.heading' ), 
      description: sts_option( 'archive.menu.description' ),
      is_archive:  true
    )
] );

get_footer(); ?>