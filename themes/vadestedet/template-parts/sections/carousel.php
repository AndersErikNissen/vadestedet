<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

$block_relation = $relation . 'carousel_block_';
$elements_group = get_field( $block_relation . 'elements' );

if ( ! is_array( $elements_group ) ) return; ?>

<section class="section-carousel">
  <ul class="carousel">
    <?php 
    $first_render = false;

    for ( $i = 1; $i <= 8; $i++ ) : 
      $prefix = $block_relation . 'sub_field_' . $i . '_';

      $heading       = $elements_group[ $prefix . 'heading' ]       ?? null;
      $text          = $elements_group[ $prefix . 'text' ]          ?? null;
      $button        = $elements_group[ $prefix . 'button' ]        ?? null;
      $image_desktop = $elements_group[ $prefix . 'image_desktop' ] ?? null;
      $image_mobile  = $elements_group[ $prefix . 'image_mobile' ]  ?? null; 
      
      if ( ! $image_desktop ) continue; ?>

      <li class="carousel-item<?php if ( ! $first_render ) echo ' display active'; ?>">
        <?php if ( $image_desktop ) {
          render_acf_img( $image_desktop, $image_mobile, [ 'desktop' => '16:9', 'mobile' => '4:5' ] );
        }; ?>

        <?php if ( $heading || $text || $button ) : ?>
          <div class="carousel-item-content">
            <?php if ( $heading ) : ?>
              <h2 class="h2"><?= $heading; ?></h2>
            <?php endif;?>

            <?php if ( $text ) : ?>
              <p class="mt-1"><?= $text; ?></p>
            <?php endif;?>

            <?php if ( $button ) {
              render_btn( $button, 'btn mt-1' );
            }; ?>
          </div>
        <?php endif; ?>
      </li>
    <?php 
    if ( ! $first_render ) $first_render = true;
    endfor; ?>
  </ul>
</section>
