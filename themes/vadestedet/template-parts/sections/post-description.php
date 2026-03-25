<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

$is_event = get_post_type() === 'event' ? true : false;

// @@ BLOCKS
$block_relation    = $relation . 'post_description_block_';
$heading           = get_field( $block_relation . 'heading'           );
$event_name        = get_field( $block_relation . 'event_name'        );
$short_description = get_field( $block_relation . 'short_description' );
$image             = get_field( $block_relation . 'image'             );
$description       = get_field( $block_relation . 'description'       );
$button            = get_field( $block_relation . 'button'            );


// @@ EVENT CONFIG
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
  
  if ( $price === '0' ) {
    $price = get_theme_string( 'Gratis' );
  }
  
  $block_relation     = $relation . 'event_relationship_block_';
  $event_relationship = get_field( $block_relation . 'event_relationship' );

  $dom_times = [
    'start' => new DateTime( $raw_times[ 'start' ] ),
    'end'   => new DateTime( $raw_times[ 'end' ]   ),
  ];  
}; ?>

<section class="section-post-description">
  <div class="pw:wrapper">
    <div class="py-1">
      <a class="txt-btn" href="<?= esc_url( get_post_type_archive_link( 'event' ) ); ?>">
        <?= sts_option( 'ui.buttons.back_to_archive' ); ?>
      </a>
    </div>
  </div>

  <div class="py-3">
    <div class="pw:wrapper">
      <h1 class="h1"><?= $heading; ?></h1>
  
      <?php if ( $short_description ) : ?>
        <p class="mt-2 l1"><?= $short_description; ?></p>
      <?php endif; ?>
      
      <?php if ( $button ) : ?>
        <div class="mt-2">
          <?php render_btn( $button ); ?> 
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="pw:wrapper">
    <?php if ( $image ) : $alt_text = $image[ 'alt' ] ?? null; ?>
      <?php render_acf_img( $image, null, [ 'desktop' => '4:1.5', 'mobile' => '1:1' ], null, 'eager' ); ?>

      <?php if ( $alt_text ) : ?>
        <p class="alt-text"><?= $alt_text ?></p>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <div class="section">
    <div class="pw:wrapper section-post-description-items">
      <?php if ( $event_name ) : ?>
        <div class="section-post-description-event-item">
          <div class="top:sticky">
            <ul class="column">
              <li>
                <span><?= get_theme_string( 'Event dato' ); ?></span>
                <span class="l2 block"><?= get_localized_acf_date( $raw_date ); ?></span>
              </li>

              <li>
                <span><?= get_theme_string( 'Event tidsramme' ); ?></span>
                <span class="l2 block"><?= $dom_times[ 'start' ]->format( 'H:i' ) . '-' . $dom_times[ 'end' ]->format( 'H:i' ); ?></span>
              </li>

              <?php if ( $price ) : ?>
                <li>
                  <span><?= get_theme_string( 'Pris' ); ?></span>
                  <span class="l2 block"><?= $price; ?></span>
                </li>
              <?php endif; ?>

              <?php if ( $ticket_url ) : ?>
                <li>
                  <?php render_btn( [ 'title' => get_theme_string( 'Bestil billet' ), 'url' => $ticket_url ] ); ?> 
                </li>
              <?php endif; ?>
            </ul>

            <?php if ( $event_relationship ) : ?>
              <div class="mt-2">
                <p><?= get_theme_string( 'Relaterede event(s)' ); ?></p>

                <ul class="column">
                  <?php foreach ( $event_relationship as $event ) : ?>
                    <li class="l2">
                      <a href="<?= get_permalink( $event ); ?>">
                        <?= get_field( $relation . 'post_description_block_heading', $event->ID ); ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="section-post-description-description-item">
        <div class="rte">
          <?= $description; ?>
        </div>
      </div>
    </div>    
  </div>
</section>