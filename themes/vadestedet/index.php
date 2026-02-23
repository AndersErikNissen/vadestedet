<?php get_header(); ?>

<section class="section-index section">
  <div class="pw:wrapper">
    <?php get_template_part( 'template-parts/snippets/archive-header' ); ?>

    <?php if ( have_posts() ) : 
      $links = paginate_links( array(
        'prev_text' => get_theme_string( 'Tidligere side' ),
        'next_text' => get_theme_string( 'Næste side'     ),
      ) ); ?>
      
      <div class="grid">
        <?php while ( have_posts() ) {
          the_post();
          get_template_part( 'template-parts/blocks/card', null, [ 'class' => 'clmns-12/12 laptop:clmns-6/12' ] );    
        } ?>
      </div>

      <?php if ( $links ) : ?>
        <nav class="pagination" aria-label="Pagination">
          <?= $links; ?>
        </nav>
      <?php endif; ?>

    <?php else : ?>
      <div class="py-2">
        <p class="h4"><?= get_theme_string( 'Vi kunne desværre ikke finde nogen resultater' ); ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php get_footer();

