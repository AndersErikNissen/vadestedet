<?php
$block_relation    = 'section_event_information_event_information_block_';
$event_name        = get_field( $block_relation . 'event_name' );
$event_description = get_field( $block_relation . 'event_description' );
$event_image       = get_field( $block_relation . 'event_image' );
$raw_date          = get_field( $block_relation . 'date',             false, false );
$raw_times         = [
  'start' => get_field( $block_relation . 'start_time' ),
  'end'   => get_field( $block_relation . 'end_time' )
];
$ticket_url        = get_field( $block_relation . 'ticket_url' );
$btn_label         = get_theme_string( 'Bestil billet' );
$price             = get_field( $block_relation . 'price' );

if ( $price === '0' || $price === 0 ) {
  $price     = get_theme_string( 'Gratis' );
} else {
  $price = $price . ' kr';
}
 
$btn_label = $btn_label . ' (' . $price . ')';

$dom_times = [
  'start' => new DateTime( $raw_times[ 'start' ] ),
  'end'   => new DateTime( $raw_times[ 'end' ] ),
];  

if ( ! $event_name ) return; ?>

<div class="block-event-card" id="<?= sanitize_title( $event_name ); ?>">
  <div class="pw:wrapper">
    <div class="block-event-card-inner">
      <div class="block-event-card-meta">
        <span class="block-event-card-time">
          <?= $dom_times[ 'start' ]->format( 'H:i' ) . '-' . $dom_times[ 'end' ]->format( 'H:i' ); ?>
        </span>

        <span class="block-event-card-date">
            <?= get_localized_acf_date( $raw_date, 'j. F' ); ?>
        </span>


        <!-- <span class="block-event-card-date">
          <span class="block-event-card-weekday l1">
            <?= get_localized_acf_date( $raw_date, 'l' ); ?>
          </span>

          <span class="block-event-card-month l2">
            <?= get_localized_acf_date( $raw_date, 'j. F' ); ?>
          </span>
        </span> -->
      </div>

      <div class="block-event-card-text">
        <h3 class="h3">
          <?= $event_name; ?>
        </h3>

        <?php if ( $event_description ) { ?>
          <p class="block-event-card-description">
            <?= $event_description; ?>
          </p>
        <?php } ?>

        <?php if ( $ticket_url ) { ?>
          <div class="block-event-card-btn">
            <?php render_btn( [ 
              'title'  => $btn_label, 
              'url'    => $ticket_url,
              'target' => '_blank'
            ] ); ?>
          </div>
        <?php } ?>
      </div>
      
      <div class="block-event-card-image">
        <?= render_acf_img( 
          $event_image, 
          null, 
          [ 
            'desktop' => '4:5', 
            'mobile'  => '1:1' 
          ], 
          '1/4' 
        ); ?>
      </div>
    </div>
  </div>
</div>
