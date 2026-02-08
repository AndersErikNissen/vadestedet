<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

$heading       = get_field( $relation . 'heading'              );
$text          = get_field( $relation . 'text'                 );
$button        = get_field( $relation . 'button'               );
$image         = get_field( $relation . 'image'                );
$image_ratios = [
  'desktop'   => get_field( $relation . 'image_ratio_desktop'  ),
  'mobile'    => get_field( $relation . 'image_ratio_mobile'   ),
];
$image_first   = get_field( $relation . 'image_first'          );

$image_first_classes = "";
if ( $image_first ) {
  if ( str_contains( $image_first, 'mobile' ) ) {
    $image_first_classes .= " mobile:clmns-first";
  }
  
  if ( str_contains( $image_first, 'desktop' ) ) {
    $image_first_classes .= " desktop:clmns-first";
  } 
}; ?>

<section class="section-text-and-image section">
  <div class="grid pw:wrapper">
    <?php if ( $heading || $text || $button ) : ?>
      <div class="clmns-12/12 desktop:clmns-6/12">
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

    <?php if ( $image ) : ?>
      <div class="clmns-12/12 desktop:clmns-6/12<?= esc_attr( $image_first_classes ); ?>">
        <div class="top:sticky">
          <?php render_acf_img( $image, $image_ratios, '1/2' ); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>