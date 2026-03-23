<?php
/**
 * Schema.org helpers
 *
 * Three layers:
 *  1. Fragment builders  — reusable sub-schemas (address, org, images …)
 *  2. Type builders      — one function per schema @type
 *  3. Graph output       — sts_schema_graph() renders the <script> tag
 *
 * Usage in a template-part:
 *
 *   sts_schema_graph( [
 *       sts_schema_restaurant(),
 *       ...array_map( 'sts_schema_event', get_upcoming_event_posts() ),
 *   ] );
 *
 *   sts_schema_graph( [
 *       sts_schema_blog_posting( get_the_ID() ),
 *   ] );
 */


// ============================================================
// 1. FRAGMENT BUILDERS
// ============================================================

/**
 * PostalAddress fragment — shared by every type that needs an address.
 */
function sts_schema_postal_address(): array {
    return [
        '@type'           => 'PostalAddress',
        'streetAddress'   => sts_option( 'company.address' ),
        'addressLocality' => sts_option( 'company.city' ),
        'addressRegion'   => sts_option( 'company.region' ),
        'postalCode'      => sts_option( 'company.postal_code' ),
        'addressCountry'  => 'DK',
    ];
}


/**
 * Organization fragment — used as author / organizer.
 */
function sts_schema_organization(): array {
    return [
        '@type' => 'Organization',
        'name'  => sts_option( 'company.name' ),
        'url'   => home_url(),
    ];
}


/**
 * Three aspect-ratio image URLs for a given attachment ID.
 *
 * @param int $image_id  WordPress attachment ID.
 */
function sts_schema_image_urls( int $image_id ): array {
    return array_filter( [
        wp_get_attachment_image_url( $image_id, 'schema_1x1'  ),
        wp_get_attachment_image_url( $image_id, 'schema_4x3'  ),
        wp_get_attachment_image_url( $image_id, 'schema_16x9' ),
    ] );
}


/**
 * OpeningHoursSpecification for regular weekly hours.
 *
 * Returns an array of spec objects (one per day that has hours set).
 */
function sts_schema_opening_hours(): array {
    $days  = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];
    $specs = [];

    foreach ( $days as $day ) {
        $lower      = strtolower( $day );
        $open_time  = sts_option( "hours.{$lower}.open"  );
        $close_time = sts_option( "hours.{$lower}.close" );

        if ( $open_time && $close_time ) {
            $specs[] = [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => $day,
                'opens'     => $open_time,
                'closes'    => $close_time,
            ];
        }
    }

    return $specs;
}


/**
 * OpeningHoursSpecification for special / holiday hours.
 *
 * Polylang-aware: only includes a translated description when a
 * translation actually exists (avoids emitting the default-language
 * string on every locale).
 */
function sts_schema_special_opening_hours(): array {
    $entries = sts_option( 'hours.special_hours' ) ?? [];
    $specs   = [];

    foreach ( $entries as $entry ) {
        $spec = [
            '@type'        => 'OpeningHoursSpecification',
            'validFrom'    => $entry['date'],
            'validThrough' => $entry['date'],
            'opens'        => $entry['open']  ?? '00:00',
            'closes'       => $entry['close'] ?? '00:00',
        ];

        if ( function_exists( 'pll__' ) && ! empty( $entry['description'] ?? '' ) ) {
            $original   = $entry['description'];
            $translated = pll__( $original );
            $is_default = pll_current_language() === pll_default_language();

            if ( $is_default || $translated !== $original ) {
                $spec['description'] = $translated;
            }
        }

        $specs[] = $spec;
    }

    return $specs;
}


/**
 * BreadcrumbList fragment for a given page.
 *
 * Walks from the site root down through the page's ancestors to the
 * page itself. Returns null when the page has no parent and is the
 * homepage, because a single-item breadcrumb carries no useful signal.
 *
 * @param int|null $post_id  Defaults to the current post.
 */

function sts_schema_breadcrumb( ?int $post_id = null, bool $is_archive = false ): ?array {
    $post_id   = $post_id ?? get_the_ID();
    $post_type = get_post_type( $post_id );
    $home_id   = (int) get_option( 'page_on_front' );

    if ( $post_id === $home_id ) return null;

    $position = 1;
    $items    = [];

    // ## front page
    $items[] = [
        '@type'    => 'ListItem',
        'position' => $position++,
        'name'     => get_field( 'section_page_page_description_block_heading', $home_id ),
        'item'     => home_url(),
    ];

    // ## archive crumb
    $post_type_obj = get_post_type_object( $post_type );
    $has_archive   = $post_type_obj && $post_type_obj->has_archive;

    // The built-in 'post' type uses a dedicated posts page instead of has_archive
    $posts_page_id = (int) get_option( 'page_for_posts' );

    if ( $post_type === 'post' && $posts_page_id ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => sts_option( 'archive.' . $post_type . '.heading' ),
            'item'     => get_permalink( $posts_page_id ),
        ];
    } elseif ( $has_archive ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => sts_option( 'archive.' . $post_type . '.heading' ),
            'item'     => get_post_type_archive_link( $post_type ),
        ];
    }

    // ## current single — skipped when we're on the archive itself
    if ( ! $is_archive ) {
        $description_type = $post_type === 'page' ? 'page' : 'post';
        $item_heading = get_field( "section_{$post_type}_information_{$description_type}_description_block_heading", $post_id );

        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => $item_heading,
            'item'     => get_permalink( $post_id ),
        ];
    }

    return [
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

function sts_schema_website(): array {
    return [
        '@type' => 'WebSite',
        '@id'   => home_url( '/#website' ),
        'name'  => sts_option( 'company.name' ),
        'url'   => home_url(),
    ];
}

// ============================================================
// 2. TYPE BUILDERS
// ============================================================

/**
 * Event schema for a single event post.
 *
 * Accepts either a WP_Post object or a post ID (int).
 * Defaults to the current post when called with no argument.
 *
 * @param WP_Post|int|null $post
 */
function sts_schema_event( $post = null ): array {
    $post_id  = is_a( $post, 'WP_Post' ) ? $post->ID : ( $post ?? get_the_ID() );
    $timezone = new DateTimeZone( 'Europe/Copenhagen' );

    $relation = 'section_event_information_';
    $desc     = $relation . 'post_description_block_';
    $info     = $relation . 'event_information_block_';

    $image    = get_field( $desc . 'image', $post_id );
    $raw_date = get_field( $info . 'date',  $post_id, false, false );
    $times    = [
        'start' => DateTime::createFromFormat(
            'Ymd H:i:s',
            $raw_date . ' ' . get_field( $info . 'start_time', $post_id ),
            $timezone
        ),
        'end'   => DateTime::createFromFormat(
            'Ymd H:i:s',
            $raw_date . ' ' . get_field( $info . 'end_time', $post_id ),
            $timezone
        ),
    ];

    $schema = [
        '@type'               => 'Event',
        'name'                => get_field( $info . 'event_name',        $post_id ),
        'description'         => get_field( $desc . 'short_description', $post_id ),
        'url'                 => get_permalink( $post_id ),
        'startDate'           => $times['start'] ? $times['start']->format( 'c' ) : '',
        'endDate'             => $times['end']   ? $times['end']->format( 'c' )   : '',
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'eventStatus'         => 'https://schema.org/EventScheduled',
        'location'            => [
            '@type'   => 'Place',
            'name'    => sts_option( 'company.name' ),
            'address' => sts_schema_postal_address(),
        ],
        'organizer'           => sts_schema_organization(),
        'offers'              => [
            '@type'         => 'Offer',
            'price'         => get_field( $info . 'price' ) ?? '0',
            'priceCurrency' => 'DKK',
            'availability'  => 'https://schema.org/' . get_field( $info . 'ticket_status' )
        ]
    ];

    // ## optional event information
    $ticket_url = get_field( $info . 'ticket_url' );
    if ( $ticket_url ) {
        $schema[ 'offers' ][ 'url' ] = $ticket_url;
    }

    $performer_type = get_field( $info . 'performer_type', $post_id );
    $performer_name = get_field( $info . 'performer_name', $post_id );

    if ( $performer_type && $performer_name ) {
        if ( $performer_type !== 'default' ) {
            $schema['performer'] = [
                '@type' => $performer_type,
                'name'  => $performer_name,
            ];
        };
    };
    

    // ## add social media links
    $available_some = [ 'facebook', 'instagram', 'linkedin', 'twitter' ];

    $some_links = [];
    foreach ( $available_some as $platform ) {
        $field = sts_option( 'company.some.' . $platform );
        
        if ( ! empty( $field ) ) {
            $some_links[] = $field;
        }
    }

    if ( ! empty( $some_links ) ) {
        $schema['sameAs'] = $some_links;
    }

    // Only attach images when we have a valid attachment.
    if ( ! empty( $image['id'] ) ) {
        $schema['image'] = sts_schema_image_urls( (int) $image['id'] );
    }

    return $schema;
}


/**
 * BlogPosting schema for a single post.
 *
 * @param int|null $post_id  Defaults to the current post.
 */
function sts_schema_blog_posting( ?int $post_id = null ): array {
    $post_id = $post_id ?? get_the_ID();

    return [
        '@type'         => 'BlogPosting',
        'headline'      => get_field( 'section_post_information_post_description_block_heading', $post_id ),
        'author'        => sts_schema_organization(),
        'publisher'     => [
          '@id'         => home_url( '/#cafe' )
        ],
        'datePublished' => get_the_date( 'c', $post_id ),
        'dateModified'  => get_the_modified_date( 'c', $post_id ),
    ];
}


/**
 * WebPage schema — with optional subtype support.
 *
 * Defaults to 'WebPage'. Pass a subtype string for more specific pages:
 *
 *   sts_schema_webpage()                      → WebPage
 *   sts_schema_webpage( subtype: 'AboutPage'  )
 *   sts_schema_webpage( subtype: 'ContactPage' )
 *   sts_schema_webpage( subtype: 'FAQPage'    )
 *
 * Supported Schema.org WebPage subtypes (non-exhaustive):
 *   AboutPage, CheckoutPage, CollectionPage, ContactPage,
 *   FAQPage, ItemPage, MedicalWebPage, ProfilePage,
 *   QAPage, SearchResultsPage
 *
 * @param string|null $subtype  Schema.org WebPage subtype. Defaults to 'WebPage'.
 * @param int|null    $post_id  Defaults to the current post.
 */
function sts_schema_webpage( 
    ?string $subtype     = null, 
    ?int    $post_id     = null,
    ?string $name        = null,
    ?string $description = null,
    ?bool   $is_archive  = false,
): array {
    $post_id = $post_id ?? get_the_ID();
    $type    = $subtype ?? 'WebPage';

    $locale = function_exists( 'pll_current_language' )
        ? pll_current_language( 'locale' )
        : 'da_DK';

    $schema = [
        '@type'         => $type,
        'name'          => $name        ?? get_field( 'section_page_page_description_block_heading', $post_id ), 
        'description'   => $description ?? get_field( 'section_page_page_description_block_description', $post_id ),
        'url'           => get_permalink( $post_id ),
        'datePublished' => get_the_date( 'c', $post_id ),
        'dateModified'  => get_the_modified_date( 'c', $post_id ),
        'isPartOf'      => [ '@id' => home_url( '/#website' ) ],
        'inLanguage'    => str_replace( '_', '-', $locale ),
    ];

    $breadcrumb = sts_schema_breadcrumb( $post_id, $is_archive );
    if ( $breadcrumb ) {
        $schema['breadcrumb'] = $breadcrumb;
    }

    if ( $type === 'AboutPage' ) {
      $schema[ 'about' ] = [
        '@id' => home_url( '/#cafe' )
      ];
    }

    return $schema;
}

function sts_schema_faqpage( ?int $post_id = null ):array {
    $block_relation = 'section_faq_faq_block_';
    $post_id        = $post_id ?? get_the_ID();
    $items          = get_field( $block_relation . 'items', $post_id ) ?? null;
    $entities       = [];

    if ( empty( $items ) ) return [];

    for ( $i = 1; $i <= 12; $i++ ) {
        $question = $items[ $block_relation . "sub_field_{$i}_question" ] ?? null;
        $answer   = $items[ $block_relation . "sub_field_{$i}_answer" ]   ?? null;

        if ( ! $question || ! $answer ) continue;

        $entities[] = [
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $answer
            ]
        ];
    }

    if ( empty( $entities ) ) return [];

    $schema = [
        '@type'      => 'FAQPage',
        'mainEntity' => $entities
    ];

    return $schema;
}


/**
 * Restaurant schema for the company / homepage context.
 *
 * Upcoming events are embedded automatically.
 */
function sts_schema_restaurant(): array {
    $upcoming_events = get_posts( [
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
        ] ],
    ] );

    $schema = [
        '@type'                            => 'Restaurant',
        'name'                             => sts_option( 'company.name' ),
        'image'                            => sts_option( 'company.storefront_image' ),
        '@id'                              => home_url( '/#cafe' ),
        'url'                              => home_url(),
        'telephone'                        => sts_option( 'company.telephone' ),
        'priceRange'                       => sts_option( 'company.price_range' ),
        'address'                          => sts_schema_postal_address(),
        'geo'                              => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => 55.70745,
            'longitude' => 9.532762,
        ],
        'openingHoursSpecification'        => sts_schema_opening_hours(),
        'specialOpeningHoursSpecification' => sts_schema_special_opening_hours(),
        'events'                           => array_map( 'sts_schema_event', $upcoming_events ),
        'hasMenu'                          => [ '@id' => home_url( '/#menu' ) ],
        'contactPoint' => [
          '@type'             => 'ContactPoint',
          'contactType'       => 'customer support',
          'email'             => sts_option( 'contact.email' ),
          'availableLanguage' => [ 'English', 'Danish', 'German' ]
        ]
    ];

    // Optional fields — only added when values are configured.
    $optional = [
        'acceptsReservations' => sts_option( 'company.reservation_url' ) ? true : null,
    ];

    foreach ( $optional as $key => $value ) {
        if ( $value !== null && $value !== false && $value !== '' ) {
            $schema[ $key ] = $value;
        }
    }

    $amenity = sts_option( 'company.amenity' );
    if ( $amenity ) {
        $schema['amenityFeature'] = [
            '@type' => 'LocationFeatureSpecification',
            'name'  => $amenity,
            'value' => true,
        ];
    }

    return $schema;
}

function sts_schema_menu( $query ) {
    if ( ! $query->have_posts() ) return [];

    $sections = [];

    while ( $query->have_posts() ) {
        $query->the_post();
        
        $block_relation = 'section_menu_menu_block_';
        $acf_items = get_field( $block_relation . 'items' );
        $items = [];

        for ( $i = 1; $i <= 20; $i++ ) {
            $prefix = $block_relation . 'sub_field_' . $i . '_';

            $name        = $acf_items[ $prefix . 'name' ]        ?? null;
            $description = $acf_items[ $prefix . 'description' ] ?? null;
            $price       = $acf_items[ $prefix . 'price' ]       ?? null;

            if ( ! $name || ! $price ) continue;

            $item = [
                '@type' => 'MenuItem',
                'name' => $name,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $price,
                    'priceCurrency' => 'DKK'
                ]
            ];

            if ( $description ) {
                $item[ 'description' ] = $description;
            }

            $items[] = $item;
        };

        if ( ! empty( $items ) ) {
            $sections[] = [
                '@type' => 'MenuSection',
                'name' => get_field( $block_relation . 'heading' ),
                'hasMenuItem' => $items
            ];
        }
    }

    wp_reset_postdata();

    if ( empty( $sections ) ) return [];

    return [
        '@id'            => home_url( '/#menu' ),
        '@type'          => 'Menu',
        'name'           => sts_option( 'archive.menu.heading' ),
        'hasMenuSection' => $sections
    ];
}


// ============================================================
// 3. GRAPH OUTPUT
// ============================================================

/**
 * Render a JSON-LD <script> tag containing an @graph with all
 * provided schema objects.
 *
 * Empty / null entries are automatically filtered out, so callers
 * can pass conditional values without wrapping them in if-guards.
 *
 * @param array $nodes  Array of schema arrays (from the type builders above).
 */
function sts_schema_graph( array $nodes ): void {
    // Strip falsy entries so callers can do e.g. [ condition ? sts_schema_event() : null ].
    $nodes = array_values( array_filter( $nodes ) );

    if ( empty( $nodes ) ) {
        return;
    }

    $graph = [
        '@context' => 'https://schema.org',
        '@graph'   => $nodes,
    ];

    echo '<script type="application/ld+json">'
        . json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE )
        . '</script>';
}