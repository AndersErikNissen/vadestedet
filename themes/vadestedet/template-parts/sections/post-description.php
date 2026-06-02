<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

// @@ BLOCKS
$block_relation    = $relation . 'post_description_block_';
$heading           = get_field( $block_relation . 'heading'           );
$short_description = get_field( $block_relation . 'short_description' );
$image             = get_field( $block_relation . 'image'             );
$description       = get_field( $block_relation . 'description'       );
$button            = get_field( $block_relation . 'button'            ); ?>

<section class="section-post-description color-theme-swap-trigger">
  <div class="pw:wrapper">
    <div class="py-1">
      <a class="txt:btn" href="<?= esc_url( get_post_type_archive_link( 'event' ) ); ?>">
        <?= get_theme_string( 'Tilbage' ); ?>
      </a>
    </div>
  </div>

  <div class="py-3">
    <div class="pw:wrapper">
      <h1 class="h1"><?= $heading; ?></h1>
  
      <?php if ( $short_description ) : ?>
        <p class="mt-2 l1"><?= $short_description; ?></p>
      <?php endif; ?>
      
      <?php if ( $button ) : ?>
        <div class="mt-2">
          <?php render_btn( $button ); ?> 
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="pw:wrapper">
    <?php if ( $image ) : $alt_text = $image[ 'alt' ] ?? null; ?>
      <?php render_acf_img( $image, null, [ 'desktop' => '4:1.5', 'mobile' => '1:1' ], null, 'eager' ); ?>

      <?php if ( $alt_text ) : ?>
        <p class="alt-text"><?= $alt_text ?></p>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <div class="section">
    <div class="pw:wrapper section-post-description-items">
      <div class="section-post-description-description-item">
        <div class="rte">
          <?= $description; ?>
        </div>
      </div>
    </div>    
  </div>
</section>