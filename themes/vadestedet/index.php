<?php 
get_header(); 

$post_type = get_post_type();
$heading = sts_option( 'archive.' . $post_type . '.heading' ) ?? false;
$description = sts_option( 'archive.' . $post_type . '.description' ) ?? false; ?>

<section class="section-index section">
  <div class="pw:wrapper">
    <?php if ( $heading || $description ) : ?>
      <div class="pb-2">
        <?php if ( $heading ) : ?>
          <h1 class="h1 mb-1">
            <?= $heading; ?>
          </h1>
        <?php endif; ?>

        <?php if ( $description ) : ?>
          <p class="l1">
            <?= $description; ?>
          </p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

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
      <div>
        <p></p>
        <!-- INGEN POSTS... -->
      </div>
    <?php endif; ?>
  </div>
</section>

<?php get_footer();

