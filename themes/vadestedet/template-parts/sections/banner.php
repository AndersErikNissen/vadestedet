<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

$block_relation = $relation . 'banner_block_';
$text           = get_field( $block_relation . 'text' );

$block_relation = $relation . 'option_1_block_';
$color_theme    = get_field( $block_relation . 'color_theme' );

if ( empty( $text ) ) return; 

$strings = explode( '//', $text ); ?>

<section class="section-banner color-theme-swap-trigger" data-color-theme="<?= $color_theme; ?>">
  <div class="banner">
    <div class="banner-track">
      <!-- JS Injects HTML here... -->
    </div>

    <div class="banner-blueprint">
      <?php foreach ( $strings as $string ) { ?>
        <span class="banner-string">
          <?= $string; ?>
        </span> 
      <?php } ?>
    </div>
  </div>
</section>
