<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;


// @@ BLOCKS
$block_relation = $relation . 'text_block_';
$heading        = get_field( $block_relation . 'heading' );
$sub_heading    = get_field( $block_relation . 'sub_heading' );
$text           = get_field( $block_relation . 'text' );
$button         = get_field( $block_relation . 'button' );

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
$layout_wrapper_class = 'text-and-image-layout-wrapper';

$h_class = 'h4';
$p_class = 'rte';

if ( $layout !== 'one' ) {
  $h_class = 'h1';
}

if ( $has_image ) {
  if ( $layout === 'one' ) {
    $h_class = 'h2';
  }

  if ( $layout !== 'two' ) {
    $layout_wrapper_class .= ' pw:wrapper';
  }

  $layout_class = 'image:' . $layout_class;
} else {
  if ( $layout === 'one' ) {
    $p_class .= ' l2';
  }

  $layout_wrapper_class .= ' pw:wrapper';
}

if ( ! $heading && ! $text && ! $button && ! $images['desktop'] ) return; ?>

<section class="section-text-and-image color-theme-section <?= $layout_class; ?>" data-color-theme="<?= $color_theme; ?>">
  <div class="<?= $layout_wrapper_class; ?>">
    <?php if ( $heading || $text || $button ) { ?>
      <div class="text-and-image-layout-text">
        <div class="top:sticky">
          <?php if ( $heading ) { ?> 
            <h2 class="text-and-image-heading <?= $h_class; ?>">
              <?= $heading; ?>
            </h2>
          <?php } ?>

          <?php if ( $sub_heading ) { ?> 
            <p class="text-and-image-sub-heading">
              <?= $sub_heading; ?>
            </p>
          <?php } ?>
  
          <?php if ( $text ) { ?> 
            <p class="text-and-image-description <?= $p_class; ?>">
              <?= $text; ?>
            </p>
          <?php } ?>      

          <?php if ( $button ) { ?>
            <div class="text-and-image-button">
              <?php render_btn( $button, 'btn mt-15' ); ?>
            </div>
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