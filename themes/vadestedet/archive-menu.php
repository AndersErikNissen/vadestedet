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

?>

<section class="section-menu section">
  <div class="pw:wrapper">
    <?php get_template_part( 'template-parts/snippets/archive-header' ); ?>

    <?php if ( $menu_query->have_posts() ) : ?>
      <div class="column gap-2">
        <?php while ( $menu_query->have_posts() ) {
          $menu_query->the_post();
          get_template_part( 'template-parts/blocks/menu' ); 
        }; ?>
      </div>

    <?php wp_reset_postdata(); else : ?>
      <div class="py-2">
        <p class="h4"><?= get_theme_string( 'Vi kunne desværre ikke finde nogen resultater' ); ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>