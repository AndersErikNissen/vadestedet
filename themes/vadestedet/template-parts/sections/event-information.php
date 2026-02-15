<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;


// @@ BLOCKS
$block_relation    = $relation . 'post_description_block_';
$heading           = get_field( $block_relation . 'heading'           );
$event_name        = get_field( $block_relation . 'event_name'        );
$short_description = get_field( $block_relation . 'short_description' );
$image             = get_field( $block_relation . 'image'             );
$description       = get_field( $block_relation . 'description'       );
$button            = get_field( $block_relation . 'button'            );

$block_relation = $relation . 'event_information_block_';
$event_name     = get_field( $block_relation . 'event_name' );
$raw_date       = get_field( $block_relation . 'date', false, false   );
$raw_times      = [
  'start' => get_field( $block_relation . 'start_time' ),
  'end'   => get_field( $block_relation . 'end_time'   )
];

$block_relation     = $relation . 'event_relationship_block_';
$event_relationship = get_field( $block_relation . 'event_relationship' );

// @@ DATE FORMATING
$timezone = new DateTimeZone( 'Europe/Copenhagen' );

$json_ld_times = [
  'start' => DateTime::createFromFormat('Ymd H:i:s', $raw_date . ' ' . $raw_times[ 'start' ], $timezone ),
  'end'   => DateTime::createFromFormat('Ymd H:i:s', $raw_date . ' ' . $raw_times[ 'end' ],   $timezone )
];

$dom_times = [
  'start' => new DateTime( $raw_times[ 'start' ] ),
  'end'   => new DateTime( $raw_times[ 'end' ]   ),
];

// @@ JSON-LD
$json_ld = [
  '@context' => 'https://schema.org',
  '@type' => 'Event',
  'name' => $event_name,
  'startDate' => $json_ld_times[ 'start' ] ? $json_ld_times[ 'start' ]->format( 'c' ) : '',
  'endDate' => $json_ld_times[ 'end' ] ? $json_ld_times[ 'end' ]->format( 'c' ) : '',
  'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
  'eventStatus' => 'https://schema.org/EventScheduled',
  'location' => [
    '@type' => 'Place',
    'name' => sts_option( 'company.name' ),
    'address' => [
      '@type' => 'PostalAddress',
      'streetAddress' => sts_option( 'company.address' ),
      'addressLocality' => sts_option( 'company.city' ),
      'addressRegion' => sts_option( 'company.region' ),
      'postalCode' => sts_option( 'company.postal_code' ),
      'addressCountry' => 'DK'
    ]
  ],
  'description' => $short_description,
  'organizer' => [
    '@type' => 'Organization',
    'name' => sts_option( 'company.name' ),
    'url' => site_url(),
  ]
];
// ## note: aditional options [offers, performer]


if ( $image ) {
  $image_id = $image[ 'id' ];

  $json_ld['image'] = [
    wp_get_attachment_image_url( $image_id, 'schema_1x1'  ),
    wp_get_attachment_image_url( $image_id, 'schema_4x3'  ),
    wp_get_attachment_image_url( $image_id, 'schema_16x9' )
  ];
}

echo '<script type="application/ld+json">' . json_encode( $json_ld, JSON_UNESCAPED_SLASHES ) . '</script>';
?>

<section class="section-event-information">
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
    <div class="pw:wrapper grid">
      <div class="clmns-12/12 laptop:clmns-4/12">
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

            <?php if ( $button ) : ?>
              <li>
                <?php render_btn( $button ); ?> 
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

      <div class="clmns-12/12 laptop:clmns-8/12">
        <div class="rte">
          <?= $description; ?>
        </div>
      </div>
    </div>    
  </div>
</section>