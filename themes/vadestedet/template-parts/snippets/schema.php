<?php
$type = $args[ 'type' ] ?? get_post_type();
$schema = [];

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
  $timezone  = new DateTimeZone( 'Europe/Copenhagen' );
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
    'image' => [
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
  $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  $opening_hours_schema = [];

  foreach ( $days as $day ) {
      // 2. Fetch the values from your WP backend (lowercasing the day for the field name)
      $lower_day = strtolower( $day );

      $open_time  = sts_option( "hours.{$lower_day}.open" );  // PLS FIX, format correctly

      // $open_time  = get_post_meta(get_the_ID(), $lower_day . '_open', true);  // e.g., '11:00'
      // $close_time = get_post_meta(get_the_ID(), $lower_day . '_close', true); // e.g., '22:00'

      // 3. Only add the day to the schema if the times are actually set
      if ($open_time && $close_time) {
        $opening_hours_schema[] = [
          "@type" => "OpeningHoursSpecification",
          "dayOfWeek" => $day,
          "opens" => $open_time,
          "closes" => $close_time
        ];
      }
  }

  $locale = function_exists( 'pll_current_language' ) ? pll_current_language( 'locale' ) : 'da_DK';
  $inLang = str_replace( '_', '-', $locale );

  $schema = [
    '@context'      => 'https://schema.org',
    '@type'         => 'Restaurant',
    'name'          => sts_option( 'company.name' ),
    'image'       => sts_option( 'company.storefront_image' ),
    '@id'         => home_url( '/#cafe' ),
    'inLanguage' => $inLang,
    'url'         => home_url(),
    'telephone'   => sts_option( 'company.telephone' ),
    'priceRange'  => sts_option( 'company.price_range' ),
    'acceptsReservations' => true, // PLS FIX
    'amenityFeature' => [
      '@type' => 'LocationFeatureSpecification',
      'name' => 'Board Game Library', // PLS FIX
      'value' => true
    ],    
    'address'     => [
      '@type'           => 'PostalAddress',
      'streetAddress'   => sts_option( 'company.address' ),
      'addressLocality' => sts_option( 'company.city' ),
      'addressRegion'   => sts_option( 'company.region' ),
      'postalCode'      => sts_option( 'company.postal_code' ),
      'addressCountry'  => 'DK'
    ],
    'geo' => [
      '@type' => 'GeoCoordinates',
      'latitude' => 55.70745,
      'longitude' => 9.532762
    ],
    'openingHoursSpecification' => $opening_hours_schema,
  ];

  $menu_url = sts_option( 'company.menu_url' ) ?? false;

  if ( $menu_url ) {
    $schema[ 'menu' ] = $menu_url;
  }
}



echo '<script type="application/ld+json">' . json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . '</script>';