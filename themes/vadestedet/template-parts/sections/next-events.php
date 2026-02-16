<?php
$post_count = $args[ 'post_count' ] ?? 2;
$current_date = date('Ymd');
$is_event = get_post_type() === 'event' ? true : false;
$acf_key = 'section_event_information_event_information_block_date';

$args = [
  'post_type'      => 'event',
  'posts_per_page' => $post_count,
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
];

if ( $is_event ) {
  $args[ 'post__not_in' ] = [ get_the_ID() ];
}

$next_events_query = new WP_Query( $args );

if ( $next_events_query->have_posts() ) : ?>

<section class="section-next-events section">
  <div class="pw:wrapper">
    <div class="spaced:row pb-2">
      <h2 class="h2"><?= get_theme_string( 'Kommende event(s)' ); ?></h2>
      <a class="txt-btn" href="<?= esc_url( get_post_type_archive_link( 'event' ) ); ?>">
        <?= get_theme_string( 'Se alle' ); ?>
      </a>
    </div>

    <div class="grid">
      <?php while ( $next_events_query->have_posts() ) {
        $next_events_query->the_post();
        get_template_part( 'template-parts/blocks/card', null, [ 'class' => 'clmns-12/12 laptop:clmns-6/12' ] );
      }; ?>
    </div>
  </div>
</section>

<?php wp_reset_postdata(); endif;
