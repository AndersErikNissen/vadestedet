<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

// @@ BLOCK
$block_relation = $relation . 'faq_block_';
$heading        = get_field( $block_relation . 'heading'     );
$items          = get_field( $block_relation . 'items'       );

// @@ FAQ ENTITIES FOR THE SCHEMA
$mainEntities = []; 

if ( ! is_array( $items ) ) return; ?>

<section class="section-faq section">
  <div class="pw:wrapper">
    <?php if ( $heading ) : ?>
      <div class="py-2">
        <h2 class="h2">
          <?= $heading; ?>
        </h2>
      </div>
    <?php endif; ?>

    <ul class="accordion">
      <?php 
      $first_render = false; 

      for ( $i = 1; $i <= 12; $i++ ) : 
        $prefix = $block_relation . 'sub_field_' . $i . '_';

        $question = $items[ $prefix . 'question' ] ?? null;
        $answer   = $items[ $prefix . 'answer' ]   ?? null;
        
        if ( ! $question || ! $answer ) continue; ?>
        <li class="accordion__item">
          <div class="accordion__header">
            <h4 class="accordion__title h3"><?= $question; ?></h4>
          </div>

          <div class="accordion__drawer">
            <div class="accordion__content">
              <p><?= $answer; ?></p>
            </div>
          </div>
        </li>
      <?php 
      $mainEntities[] = [
        '@type' => 'Question',
        'name' => $question,
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => $answer
        ]
      ];

      if ( ! $first_render ) $first_render = true; 
      endfor; ?>
    </ul>
  </div>
</section>

<?php
// @@ SCHEMA RENDER
$schema = [
  '@context' => 'https://schema.org',
  '@type' => 'FAQPage',
  'mainEntity' => $mainEntities
];

echo '<script type="application/ld+json">' . json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . '</script>';