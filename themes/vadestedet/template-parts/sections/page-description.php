<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

$is_event = get_post_type() === 'event' ? true : false;

// @@ BLOCKS
$block_relation    = $relation . 'page_description_block_';
$heading           = get_field( $block_relation . 'heading' );
$short_description = get_field( $block_relation . 'short_description' );
$image             = get_field( $block_relation . 'image' );
$content           = get_field( $block_relation . 'content' ); ?>

<section class="section-page-description section">
  <div class="pw:wrapper">
    <h1 class="h1"><?= $heading; ?></h1>

    <?php if ( $short_description ) : ?>
      <p class="mt-2 l1"><?= $short_description; ?></p>
    <?php endif; ?>

    <?php if ( $image ) : $alt_text = $image[ 'alt' ] ?? null; ?>
      <div class="mt-2">
        <?php render_acf_img( $image, null, [ 'desktop' => '4:1.5', 'mobile' => '1:1' ], null, 'eager' ); ?>
    
        <?php if ( $alt_text ) : ?>
          <p class="alt-text"><?= $alt_text ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ( $content ) : ?>
      <div class="mt-2 grid">
        <div class="clmns-12/12 laptop:clmns-8/12 laptop:start-clmn-3 desktop:clmns-6/12 desktop:start-clmn-4">
          <div class="rte">
            <?= $content; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>