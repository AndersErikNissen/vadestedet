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

<section class="section-page-description bg:section color-theme-swap-trigger color-theme-section" data-color-theme="brown-yellow">
  <div class="pw:wrapper">
    <div class="grid pt-4">
      <div class="clmns-12/12 laptop:clmns-8/12 laptop:start-clmn-3">
        <h1 class="h1"><?= $heading; ?></h1>
      </div>

      <?php if ( $short_description ) : ?>
        <div class="mt-2 clmns-12/12 laptop:clmns-8/12 laptop:start-clmn-3">
          <p class="l2">
            <?= $short_description; ?>
          </p>
        </div>
      <?php endif; ?>

      <?php if ( $image ) : $alt_text = $image[ 'alt' ] ?? null; ?>
        <div class="mt-2 clmns-12/12 laptop:clmns-8/12 laptop:start-clmn-3">
          <?php render_acf_img( $image, null, [ 'desktop' => '4:1.5', 'mobile' => '1:1' ], null, 'eager' ); ?>
      
          <?php if ( $alt_text ) : ?>
            <p class="alt-text"><?= $alt_text ?></p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ( $content ) : ?>
        <div class="mt-2 clmns-12/12 laptop:clmns-8/12 laptop:start-clmn-3">
          <div class="rte">
            <?= $content; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>