<?php 
get_header(); 

$acf_key = 'section_menu_menu_block_sorting_order';

$menu_query = new WP_Query( [
  'post_type'      => 'menu',
  'posts_per_page' => -1,
  'meta_key'       => 'section_menu_menu_block_sorting_order',
  'orderby'        => 'meta_value_num',
  'order'          => 'ASC',
] ); ?>

<section class="section-menu section">
  <div class="pw:wrapper">
    <h1 class="section-menu-title h1">
      <?= get_theme_string( 'Menu' ); ?>
    </h1>

    <?php if ( $menu_query->have_posts() ) { ?>
      <div class="column gap-2">
        <?php while ( $menu_query->have_posts() ) {
          $menu_query->the_post();
          get_template_part( 'template-parts/blocks/menu' ); 
        } ?>
      </div>

      <?php wp_reset_postdata();
    } else { ?>
      <div class="py-2">
        <p class="h4">
          <?= get_theme_string( 'Vi kunne desværre ikke finde nogen resultater' ); ?>
        </p>
      </div>
    <?php } ?>
  </div>
</section>

<?php sts_schema_graph( [
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