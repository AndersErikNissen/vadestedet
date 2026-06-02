<?php
$post_type  = get_post_type();
$relation   = 'section_' . $post_type . '_information_';
$class      = $args[ 'class' ] ?? '';
$image_size = $args[ 'image_size' ] ?? '1/4'; 
$badges     = [];
$is_event   = $post_type === 'event' ?? null;

// @@ IS EVENT
if ( $is_event ) {
  $block_relation = $relation . 'event_information_block_';
  $event_name     = get_field( $block_relation . 'event_name' );
  $raw_date       = get_field( $block_relation . 'date', false, false   );
  $raw_times      = [
    'start' => get_field( $block_relation . 'start_time' ),
    'end'   => get_field( $block_relation . 'end_time'   )
  ];
  $price          = get_field( $block_relation . 'price' );
  $ticket_url     = get_field( $block_relation . 'ticket_url' );
  
  if ( $price === '0' || $price === 0 ) {
    $price = get_theme_string( 'Gratis' );
  }

  $dom_times = [
    'start' => new DateTime( $raw_times[ 'start' ] ),
    'end'   => new DateTime( $raw_times[ 'end' ]   ),
  ];  
} else {
  
}


// @@ BLOCKS
$block_relation    = $relation . 'post_description_block_';
$heading           = get_field( $block_relation . 'heading'           );
$short_description = get_field( $block_relation . 'short_description' );
$image             = get_field( $block_relation . 'image'             ); 

if ( ! $heading ) return; ?>

<div class="block-card <?= $class; ?>">
  <?php if ( $is_event ) { ?>
    <div class="block-card-event">
      <?php if ( $price ) : ?>
        <span class="block-card-price">
          <?= $price; ?>
        </span>
      <?php endif; ?>

      <span class="block-card-date">
        <?= get_localized_acf_date( $raw_date ); ?>
      </span>

      <span class="block-card-time">
        <?= $dom_times[ 'start' ]->format( 'H:i' ) . '-' . $dom_times[ 'end' ]->format( 'H:i' ); ?>
      </span>
    </div>
  <?php } ?>

  <div class="block-card-main">
    <div class="block-card-heading">
      <?php if ( $ticket_url ) : ?>
        <?php render_btn( [ 'title' => get_theme_string( 'Bestil billet' ), 'url' => $ticket_url ] ); ?> 
      <?php endif; ?>
    </div>

    <div class="block-card-description">

    </div>
  </div>


  <div class="flex contain">





    
    <?= render_acf_img( $image, null, [ 'desktop' => '4:5', 'mobile' => '4:5' ], $image_size ); ?>
    <a class="cover" href="<?= get_permalink(); ?>"></a>
  </div>
  
  <a class="block h4 mb-1" href="<?= get_permalink(); ?>">
    <?= $heading; ?>
  </a>

  <p><?= mb_strimwidth( $short_description, 0, 203, '...' ); ?></p>

  <a class="btn" href="<?= get_permalink(); ?>">
    <?= get_theme_string( 'Se event' ); ?>
  </a>
  <div class="block-card-content">

    <div>

    </div>
  </div>
</div>
