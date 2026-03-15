<?php
$type = $args[ 'type' ] ?? get_post_type();
$schema = [];
$timezone  = new DateTimeZone( 'Europe/Copenhagen' );

// @@ SINGLE POST
if ( $type === 'event' ) {
  $relation = 'section_event_information_';
  $blocks = [
    $relation . 'post_description_block_',
    $relation . 'event_information_block_'
  ];
  
  $image     = get_field( $blocks[0] . 'image' );
  $image_id  = $image[ 'id' ];
  $raw_date  = get_field( $blocks[1] . 'date', false, false   );
  $raw_times = [
    'start' => get_field( $blocks[1] . 'start_time' ),
    'end'   => get_field( $blocks[1] . 'end_time'   )
  ];
  
  $times     = [
    'start' => DateTime::createFromFormat('Ymd H:i:s', $raw_date . ' ' . $raw_times[ 'start' ], $timezone ),
    'end'   => DateTime::createFromFormat('Ymd H:i:s', $raw_date . ' ' . $raw_times[ 'end' ],   $timezone )
  ];

  $schema = [
    '@context'            => 'https://schema.org',
    '@type'               => 'Event',
    'name'                => get_field( $blocks[1] . 'event_name' ),
    'description'         => get_field( $blocks[0] . 'short_description' ),
    'startDate'           => $times[ 'start' ] ? $times[ 'start' ]->format( 'c' ) : '',
    'endDate'             => $times[ 'end' ] ? $times[ 'end' ]->format( 'c' ) : '',
    'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
    'eventStatus'         => 'https://schema.org/EventScheduled',
    'location'            => [
      '@type'             => 'Place',
      'name'              => sts_option( 'company.name' ),
      'address'           => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => sts_option( 'company.address' ),
        'addressLocality' => sts_option( 'company.city' ),
        'addressRegion'   => sts_option( 'company.region' ),
        'postalCode'      => sts_option( 'company.postal_code' ),
        'addressCountry'  => 'DK'
      ]
    ],
    'organizer'           => [
      '@type' => 'Organization',
      'name'  => sts_option( 'company.name' ),
      'url'   => home_url(),
    ],
    'image'               => [
      wp_get_attachment_image_url( $image_id, 'schema_1x1'  ),
      wp_get_attachment_image_url( $image_id, 'schema_4x3'  ),
      wp_get_attachment_image_url( $image_id, 'schema_16x9' )
    ]
  ];
}

if ( $type === 'post' ) {
  $schema = [
    '@context'      => 'https://schema.org',
    '@type'         => 'BlogPosting',
    'headline'      => get_field( 'section_post_information_post_description_heading' ),
    'author'        => [
      '@type'       => 'Organization',
      'name'        => sts_option( 'company.name' ),
      'url'         => home_url()
    ],
    'publisher'     => [
      '@type'       => "LocalBusiness",
      'name'        => sts_option( 'company.name' ),
      'telephone'   => sts_option( 'company.telephone' ),
      'image'       => sts_option( 'company.storefront_image' ),
      'address'     => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => sts_option( 'company.address' ),
        'addressLocality' => sts_option( 'company.city' ),
        'addressRegion'   => sts_option( 'company.region' ),
        'postalCode'      => sts_option( 'company.postal_code' ),
        'addressCountry'  => 'DK'
      ],
      'priceRange'  => sts_option( 'company.price_range' ),
      'logo'        => [
        '@type'     => 'ImageObject',
        'url'       => sts_option( 'company.logo' )
      ]
    ],
    'datePublished' => get_the_date('c', get_the_ID() ),
    'dateModified'  => get_the_modified_date('c', get_the_ID() )
  ];
}

if ( $type === 'company' ) {
  // ## opening hours
  $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  $opening_hours_schema = [];

  foreach ( $days as $day ) {
      $lower_day = strtolower( $day );

      $open_time   = sts_option( "hours.{$lower_day}.open"  );
      $close_time  = sts_option( "hours.{$lower_day}.close" );

      if ($open_time && $close_time) {
        $opening_hours_schema[] = [
          "@type" => "OpeningHoursSpecification",
          "dayOfWeek" => $day,
          "opens" => $open_time,
          "closes" => $close_time
        ];
      }
  }


  // ## special opening hours
  $special_opening_hours = sts_option( 'hours.special_hours' ) ?? [];
  $special_opening_hours_schema = [];

  foreach ( $special_opening_hours as $special_opening_hour ) { 
    $opens = $special_opening_hour[ 'open' ]   ?? '00:00';
    $closes = $special_opening_hour[ 'close' ] ?? '00:00';

    $special_opening_hour_schema = [
      '@type'        => 'OpeningHoursSpecification',
      'validFrom'    => $special_opening_hour[ 'date' ],
      'validThrough' => $special_opening_hour[ 'date' ],
      'opens'        => $opens,
      'closes'       => $closes
    ];

    if ( function_exists( 'pll__' ) && ! empty( $special_opening_hour[ 'description' ] ?? '' ) ) {
      $original    = $special_opening_hour[ 'description' ];
      $translated  = pll__( $original );
      $is_default  = pll_current_language() === pll_default_language();

      if ( $is_default || $translated !== $original ) {
        $special_opening_hour_schema[ 'description' ] = $translated;
      }
    }

    $special_opening_hours_schema[] = $special_opening_hour_schema;
  }


  // ## events
  $events = get_posts( [
    'post_type'      => 'event',
    'posts_per_page' => 10,
    'orderby'        => 'meta_value',
    'meta_key'       => 'section_event_information_event_information_block_date',
    'order'          => 'ASC',
    'meta_query'     => [ [
      'key'     => 'section_event_information_event_information_block_date',
      'value'   => date( 'Ymd' ),
      'compare' => '>=',
      'type'    => 'DATE',
    ] ]
  ] );

  $event_schema = array_map( function( $event ) use ( $timezone ) {
    $relation = 'section_event_information_';
    $blocks = [
      $relation . 'post_description_block_',
      $relation . 'event_information_block_'
    ];

    $image     = get_field( $blocks[0] . 'image',      $event->ID );
    $image_id  = $image['id'];
    $raw_date  = get_field( $blocks[1] . 'date',       $event->ID, false );
    $raw_times = [
      'start' => get_field( $blocks[1] . 'start_time', $event->ID ),
      'end'   => get_field( $blocks[1] . 'end_time',   $event->ID ),
    ];
    $times = [
      'start'  => DateTime::createFromFormat( 'Ymd H:i:s', $raw_date . ' ' . $raw_times['start'], $timezone ),
      'end'    => DateTime::createFromFormat( 'Ymd H:i:s', $raw_date . ' ' . $raw_times['end'],   $timezone ),
    ];

    return [
      '@type'       => 'Event',
      'name'        => get_field( $blocks[1] . 'event_name',        $event->ID ),
      'description' => get_field( $blocks[0] . 'short_description', $event->ID ),
      'url'         => get_permalink( $event->ID ),
      'startDate'   => $times['start'] ? $times['start']->format( 'c' ) : '',
      'endDate'     => $times['end']   ? $times['end']->format( 'c' )   : '',
    ];
  }, $events );


  // ## the schema
  $locale = function_exists( 'pll_current_language' ) ? pll_current_language( 'locale' ) : 'da_DK';
  $inLang = str_replace( '_', '-', $locale );

  $schema = [
    '@context'                         => 'https://schema.org',
    '@type'                            => 'Restaurant',
    'name'                             => sts_option( 'company.name' ),
    'image'                            => sts_option( 'company.storefront_image' ),
    '@id'                              => home_url( '/#cafe' ),
    'inLanguage'                       => $inLang,
    'url'                              => home_url(),
    'telephone'                        => sts_option( 'company.telephone' ),
    'priceRange'                       => sts_option( 'company.price_range' ),
    'openingHoursSpecification'        => $opening_hours_schema,
    'specialOpeningHoursSpecification' => $special_opening_hours_schema,
    'address'                          => [
      '@type'           => 'PostalAddress',
      'streetAddress'   => sts_option( 'company.address' ),
      'addressLocality' => sts_option( 'company.city' ),
      'addressRegion'   => sts_option( 'company.region' ),
      'postalCode'      => sts_option( 'company.postal_code' ),
      'addressCountry'  => 'DK'
    ],
    'geo'                             => [
      '@type'     => 'GeoCoordinates',
      'latitude'  => 55.70745,
      'longitude' => 9.532762
    ],
    'events'                          => $event_schema
  ];

  $menu_url = sts_option( 'company.menu_url' ) ?? false;

  if ( $menu_url ) {
    $schema[ 'menu' ] = $menu_url;
  }

  $amenity_feature = sts_option( 'company.amenity' ) ?? false;

  if ( $amenity_feature ) {
    $schema[ 'amenityFeature' ] = [
      '@type' => 'LocationFeatureSpecification',
      'name'  => $amenity_feature,
      'value' => true
    ];
  };

  $reservation_url = sts_option( 'company.reservation_url' ) ?? false;

  if ( $reservation_url ) {
    $schema[ 'acceptsReservations' ] = true;
  }
}

echo '<script type="application/ld+json">' . json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . '</script>';