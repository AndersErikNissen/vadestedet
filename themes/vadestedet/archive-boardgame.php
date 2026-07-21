<?php 
get_header(); 

$schemas        = [
  sts_schema_website(),
  sts_schema_webpage(  
    name:        sts_option( 'archive.boardgame.heading' ), 
    description: sts_option( 'archive.boardgame.description' ),
    is_archive:  true
  )
];

$boardgame_groups = [];


if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $title = get_the_title() ?? null;

    if ( ! $title ) return;

    $first_sign = mb_substr( trim( $title ), 0, 1 );
    $first_sign = mb_strtolower( $first_sign );

    if ( ! preg_match( '/^[a-zæøå]/u', $first_sign ) ) {
      $first_sign = '#';
    }

    if ( ! array_key_exists( $first_sign, $boardgame_groups ) ) {
      $boardgame_groups[$first_sign] = [];
    }
    
    $boardgame_groups[$first_sign][] = $title;
  }

  wp_reset_postdata();
}

ksort( $boardgame_groups );

if ( count( $boardgame_groups ) === 0 ) return; ?>

<section class="section-boardgames bg:section color-theme-swap-trigger color-theme-section" data-color-theme="white-brown">
  <div class="pw:wrapper">
    <?php get_template_part( 'template-parts/snippets/archive-header', null, [
      'post_type' => 'boardgame'
    ] ); ?>

    <div class="section-boardgames-filter">
      <span><?= get_theme_string( 'Filtrer' ); ?></span>

      <ul class="section-boardgames-filter-row">
        <?php foreach( $boardgame_groups as $group => $boardgames ) { ?>
          <li>
            <button class="section-boardgames-filter-btn" data-filter-for="boardgame-group-<?= $group; ?>">
              <?= $group; ?>
            </button>
          </li>
        <?php } ?>

        <li>
          <button class="section-boardgames-clear-filter-btn" data-clear-filters>
            <?= get_theme_string( 'Fjern filtre' ); ?>
          </button>
        </li>
      </ul>
    </div>
  </div>

  <div class="pw:wrapper">
    <ul class="section-boardgames-items">
      <?php foreach( $boardgame_groups as $group => $boardgames ) { ?>
        <li class="section-boardgames-item" id="boardgame-group-<?= $group ?>">
          <h3 class="section-boardgames-item-title h2">
            <?= $group; ?>
          </h3>

          <ul class="section-boardgames-item-list">
            <?php foreach( $boardgames as $boardgame ) { ?>
              <li>
                <span>
                  <?= $boardgame; ?>
                </span>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
    </ul>
  </div>
</section>

<?php 
sts_schema_graph( $schemas );
get_footer();