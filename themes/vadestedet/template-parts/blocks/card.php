<?php
$relation = '';
$class = $args[ 'class' ] ?? '';
$image_size = $args[ 'image_size' ] ?? '1/2'; 
$post_type = get_post_type();
$badges = [];


// @@ EVENT BASED MODIFIER(S)
if ( $post_type === 'event' ) {
  $relation = 'section_event_information_';
  $raw_date = get_field( $relation . 'event_information_block_date', false, false );
  $badges[] = get_localized_acf_date( $raw_date );
}


// @@ BLOCKS
$block_relation    = $relation . 'post_description_block_';
$heading           = get_field( $block_relation . 'heading'           );
$short_description = get_field( $block_relation . 'short_description' );
$image             = get_field( $block_relation . 'image'             ); 

if ( ! $heading ) return; ?>

<div class="block-card <?= $class; ?>">
  <div class="block-card-content">
    <div class="flex contain">
      <?= render_badges( $badges ); ?>
      <?= render_acf_img( $image, null, [ 'desktop' => '1:1', 'mobile' => '1:1' ], $image_size ); ?>
      <a class="cover" href="<?= get_permalink(); ?>"></a>
    </div>

    <div>
      <a class="block h4 mb-1" href="<?= get_permalink(); ?>">
        <?= $heading; ?>
      </a>

      <p><?= mb_strimwidth( $short_description, 0, 203, '...' ); ?></p>
    </div>
  </div>
</div>
