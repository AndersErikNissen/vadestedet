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

$relation =  'section_boardgame_boardgame_block_';
$boardgame_meta_categories = [
  'card-game' => 'Kortspil',
  'strategy-game' => 'Strategispil',
  'party-game' => 'Selskabsspil',
  'tile-laying' => 'Tile-laying',
  'tile-placement' => 'Tile-placement',
  'classic' => 'Klassiker',
  'quiz-game' => 'Quizspil',
  'game' => 'Spil',
  'childrens-game' => 'Børnespil',
  'accessory-expansion' => 'Tilbehør/udvidelse',
  'euro-game' => 'Eurogame',
  'coop-detective-game' => 'Coop Detektivspil',
  'deduction-game' => 'Deduktionsspil',
  'detective-game' => 'Detektivspil',
  'coop-game' => 'Samarbejdsspil',
  'family-game' => 'Familiespil',
  'engine-building' => 'Engine-building',
  'racer-strategy-game' => 'Racer-/strategispil',
  'family' => 'Familie',
  'mystery-escape-game' => 'Mysterie-/escape-spil',
  'coop' => 'Coop',
  'abstract' => 'Abstrakt',
  'strategy-family-game' => 'Strategi- / familiespil',
  'abstract-game' => 'Abstrakt spil',
  'coop-escape-room' => 'Coop Escaperoom',
];
$boardgame_meta_difficulties = [
  'very-easy' => 'Meget let',
  'easy' => 'Let',
  'medium' => 'Mellem svær',
  'hard' => 'Svær',
  'very-hard' => 'Meget svær',
];
$boardgame_groups = [];

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $name = get_field( $relation . 'name' ) ?: get_the_title();

    if ( ! $name ) return;

    $first_sign = mb_substr( trim( $name ), 0, 1 );
    $first_sign = mb_strtolower( $first_sign );

    if ( ! preg_match( '/^[a-zæøå]/u', $first_sign ) ) {
      $first_sign = '0';
    }

    if ( ! array_key_exists( $first_sign, $boardgame_groups ) ) {
      $boardgame_groups[$first_sign] = [];
    }

    $boardgame = [
      'name' => $name,
      'url' => get_field( $relation . 'url' ) ?? null,
      'meta' => [
        'complexity' => get_field( $relation . 'complexity' ),
        'playtime' => get_field( $relation . 'playtime' ) . ' min.',
        'players' => get_field( $relation . 'players' ),
        'difficulty' => get_theme_string( $boardgame_meta_difficulties[get_field( $relation . 'difficulty' )] ),
        'category' => get_theme_string( $boardgame_meta_categories[get_field( $relation . 'category' )] ),
      ]
    ];
    
    $boardgame_groups[$first_sign][] = $boardgame;
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
      <span class="section-boardgames-filter-label"><?= get_theme_string( 'Filtrer' ); ?></span>

      <ul class="section-boardgames-filter-row">
        <div class="section-boardgames-filter-row-inner">
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
        </div>
      </ul>
    </div>

    <ul class="section-boardgames-items">
      <?php foreach( $boardgame_groups as $group => $boardgames ) { ?>
        <li class="section-boardgames-item" id="boardgame-group-<?= $group ?>">
          <h3 class="section-boardgames-item-title h2">
            <?= $group; ?>
          </h3>

          <ul class="section-boardgames-item-list">
            <?php foreach( $boardgames as $boardgame ) { ?>
              <li class="section-boardgames-item-list-item">
                <span class="section-boardgames-item-list-item-title">
                  <?= $boardgame['name']; ?>
                </span>

                <ul class="section-boardgames-item-list-item-meta">
                  <?php foreach ( $boardgame['meta'] as $name => $meta ) { ?>
                    <li class="section-boardgames-item-list-item-meta-item">
                      <div class="section-boardgames-item-list-item-meta-item-icon">
                        <?= get_icon( $name ); ?>
                      </div>
                      
                      <span class="section-boardgames-item-list-item-meta-item-name">
                        <?= $meta; ?>
                      </span>
                    </li>
                  <?php } ?>
                </ul>
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