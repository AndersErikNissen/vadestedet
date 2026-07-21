<?php 
get_header();

$acf_key        = 'section_event_information_event_information_block_date';
$current_date   = date( 'Ymd' );
$posts_per_page = get_option( 'posts_per_page' ) ?: 12;
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
] ); ?>

<section class="section-events bg:section color-theme-swap-trigger color-theme-section" data-color-theme="yellow-green">
  <div class="pw:wrapper">
    <?php get_template_part( 'template-parts/snippets/archive-header', null, [
      'post_type' => 'event'
    ] ); ?>
  </div>

  <?php if ( $future_query->have_posts() ) { ?>
    <div class="section-events-cards">
      <?php while ( $future_query->have_posts() ) {
        $future_query->the_post();
        $schemas[] = sts_schema_event( get_the_ID() );
        get_template_part( 'template-parts/blocks/event-card' ); 
      } ?>
    </div>

    <?php $links = paginate_links( array(
      'total'     => $future_query->max_num_pages,
      'current'   => $paged,
      'prev_text' => get_theme_string( 'Tidligere side' ),
      'next_text' => get_theme_string( 'Næste side'     ),
    ) ); 

    if ( $links ) { ?>
      <nav class="pagination pw:wrapper" aria-label="Pagination">
        <?= $links; ?>
      </nav>
    <?php } 
    wp_reset_postdata();
  } else { ?>

    <div class="arhive-no-results">
      <p class="h4">
        <?= get_theme_string( 'Vi kunne desværre ikke finde nogen resultater' ); ?>
      </p>
    </div>
  <?php } ?>
</section>

<?php 
sts_schema_graph( $schemas );
get_footer();