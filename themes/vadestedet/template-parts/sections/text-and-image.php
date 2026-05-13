<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;


// @@ BLOCKS
$block_relation = $relation . 'text_block_';
$heading        = get_field( $block_relation . 'heading'              );
$text           = get_field( $block_relation . 'text'                 );
$button         = get_field( $block_relation . 'button'               );

$block_relation = $relation . 'image_block_';
$images         = [
  'desktop' => get_field( $block_relation . 'image_desktop' ),
  'mobile'  => get_field( $block_relation . 'image_mobile'  ),
];
$image_ratios   = [
  'desktop' => get_field( $block_relation . 'image_ratio_desktop' ),
  'mobile'  => get_field( $block_relation . 'image_ratio_mobile' ),
];
$image_first    = get_field( $block_relation . 'image_first' );

$block_relation = $relation . 'option_2_block_';
$layout         = get_field( $block_relation . 'layout' );

if ( $images['desktop'] && $layout === '3' ) {
  $layout = '1';
}


// @@ OPTION: SET THE IMAGE CONTAINER AS THE FIRST ELEMENT IN THE GRID
$image_first_classes = "";
if ( $image_first ) {
  if ( str_contains( $image_first, 'mobile' ) ) {
    $image_first_classes .= " mobile:clmns-first";
  }
  
  if ( str_contains( $image_first, 'desktop' ) ) {
    $image_first_classes .= " laptop:clmns-first";
  } 
}; 

if ( ! $heading && ! $text && ! $button && ! $images[ 'desktop' ] ) return; ?>

<section class="section-text-and-image section <?= $layout . ':layout'; ?>">
  <div class="grid pw:wrapper">
    <?php if ( $heading || $text || $button ) : ?>
      <div class="section-text-and-image-text-wrapper">
        <div class="top:sticky">
          <?php if ( $heading ) : ?> 
            <h2 class="h2">
              <?= $heading ;?>
            </h2>
          <?php endif; ?>
  
          <?php if ( $text ) : ?> 
            <p class="rte">
              <?= $text ;?>
            </p>
          <?php endif; ?>      
  
          <?php if ( $button ) : ?> 
            <a class="btn" href="<?= esc_url( $button['url'] ?? '' ); ?>" target="<?= $button['target'] ?? '_self'; ?>">
              <?= $button[ 'title' ]; ?>
            </a>
          <?php endif; ?>      
        </div>
      </div>
    <?php endif; ?>

    <?php if ( $images[ 'desktop' ] ) : ?>
      <div class="section-text-and-image-image-wrapper<?= esc_attr( $image_first_classes ); ?>">
        <div class="top:sticky">
          <?php render_acf_img( $images[ 'desktop' ], $images[ 'mobile' ], $image_ratios, '1/2' ); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>