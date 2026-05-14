<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;


// @@ BLOCKS
$block_relation = $relation . 'text_block_';
$heading        = get_field( $block_relation . 'heading'              );
$text           = get_field( $block_relation . 'text'                 );
$button         = get_field( $block_relation . 'button'               );

$block_relation      = $relation . 'image_block_';
$image_desktop       = get_field( $block_relation . 'image_desktop' );
$image_mobile        = get_field( $block_relation . 'image_mobile' );
$image_ratios        = [
  'desktop' => get_field( $block_relation . 'image_ratio_desktop' ),
  'mobile'  => get_field( $block_relation . 'image_ratio_mobile' ),
];
$image_first         = get_field( $block_relation . 'image_first' );
$has_image           = $image_desktop ?? null;
$image_class = 'text-and-image-layout-image';
if ( $image_first ) {
  if ( str_contains( $image_first, 'mobile' ) ) {
    $image_class .= " mobile:image-first";
  }
  
  if ( str_contains( $image_first, 'desktop' ) ) {
    $image_class .= " laptop:image-first";
  } 
}; 

$block_relation = $relation . 'option_2_block_';
$color_theme    = get_field( $block_relation . 'color_theme' );
$layout         = get_field( $block_relation . 'layout' );
$layout_class   = $layout . ':text-and-image-layout';
$layout_wrapper_class = '';

$h_class = 'h2';

if ( $layout !== 'one' ) {
  $h_class = 'h1';
}

if ( $layout === 'three' ) {
  $layout_wrapper_class = ' pw:wrapper';
}

if ( $has_image ) {
  $layout_class = 'image:' . $layout_class;
}

if ( ! $heading && ! $text && ! $button && ! $images['desktop'] ) return; ?>

<section class="section-text-and-image <?= $color_theme . ':color-theme ' . $layout_class; ?>">
  <div class="text-and-image-layout-wrapper<?= $layout_wrapper_class; ?>">
    <?php if ( $heading || $text || $button ) { ?>
      <div class="text-and-image-layout-text">
        <div class="top:sticky">
          <?php if ( $heading ) { ?> 
            <h2 class="text-and-image-heading <?= $h_class; ?>">
              <?= $heading; ?>
            </h2>
          <?php } ?>
  
          <?php if ( $text ) { ?> 
            <p class="text-and-image-description rte">
              <?= $text; ?>
            </p>
          <?php } ?>      
  
          <?php if ( $button ) { ?> 
            <a class="text-and-image-button btn mt-15" href="<?= esc_url( $button['url'] ?? '' ); ?>" target="<?= $button['target'] ?? '_self'; ?>">
              <?= $button[ 'title' ]; ?>
            </a>
          <?php } ?>      
        </div>
      </div>
    <?php } ?>

    <?php if ( $image_desktop ) { ?>
      <div class="<?= esc_attr( $image_class ); ?>">
        <div class="top:sticky">
          <?php render_acf_img( $image_desktop, $image_mobile, $image_ratios, '1/2' ); ?>
        </div>
      </div>
    <?php } ?>
  </div>
</section>