<?php 
get_header(); 

global $wp;
$current_url = user_trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) );
$data = [
  'food'  => [],
  'drink' => [],
];
$schema = [
  '@context' => 'https://schema.org',
  '@type' => 'Menu',
  '@id' => $current_url . '#menu',
  'name' => get_theme_string( 'Menu' ),
  'mainEntityOfPage' => $current_url,
  'hasMenuSection' => [],
];

$menu_heading = sts_option( 'archive.menu.heading' ) ?: get_theme_string( 'Menu' ) ?: '';

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();

    $block_relation  = 'section_menu_menu_block_';
    $heading         = get_field( $block_relation . 'heading' );
    $type            = get_field( $block_relation . 'type' );
    $schema_products = [];
    $categories      = [];

    // Filter categories
    for ( $i = 1; $i <= 6; $i++ ) {
      $category_relation          = $block_relation . 'category_' . $i . '_';
      $category_heading           = get_field( $category_relation . 'heading' ) ?: null;
      $category_products          = get_field( $category_relation . 'products' );
      $upgraded_category_products = [];

      if ( count( $category_products ) > 0 ) {
        foreach ( $category_products as $product ) {
          $name          = $product['name']          ?: null;
          $variant_names = $product['variant_names'] ?: null;
          $description   = $product['description']   ?: null;
          $price         = $product['price']         ?: 0;

          if ( ! $name ) {
            continue;
          }
          
          $schema_product = [
            '@type'       => 'MenuItem',
            'name'        => $name,
            'description' => $description,
            'offers'      => [
              '@type'         => 'Offer',
              'price'         => $price,
              'priceCurrency' => 'DKK'
            ]
          ];

          
          if ( $variant_names && $price ) {
            $split_variant_names = explode( '/', $variant_names );
            $variant_count = count( $split_variant_names );
            $prices        = explode( '/', $price );
            
            if ( $variant_count === count( $prices ) ) {
              $schema_product['offers'] = [];

              for ( $vi = 0; $vi < $variant_count; $vi++ ) {
                $schema_product['offers'][] = [
                  '@type'         => 'Offer',
                  'name'          => $name  . ' - ' . $split_variant_names[$vi],
                  'price'         => $prices[$vi],
                  'priceCurrency' => 'DKK'
                ];
              }
            }
          }

          $schema_products[] = $schema_product;
          
          $upgraded_category_products[] = [
            'name'          => $name,
            'variant_names' => $variant_names,
            'description'   => $description,
            'price'         => $price,
          ];
        }

        if ( count( $upgraded_category_products ) > 0 ) {
          $categories[] = [
            'heading'  => $category_heading,
            'products' => $upgraded_category_products,
          ];
        }
      }
    }

    if ( count( $schema_products ) > 0 ) {
      $schema['hasMenuSection'][] = [
        '@type' => 'MenuSection',
        '@id' => $current_url . '#' . sanitize_title( $heading ),
        'name' => $heading,
        'hasMenuItem' => $schema_products,
      ];

      if ( ! array_key_exists( $type, $data ) ) {
        $data[$type] = [];
      }

      $data[$type][] = [
        'heading' => $heading,
        'categories' => $categories,
      ];
    }
  }

  wp_reset_postdata();
}

if ( count( $data ) === 0 ) return; ?>

<section class="section-menu bg:section color-theme-section color-theme-swap-trigger" data-color-theme="brown-yellow">
  <div class="pw:wrapper section-menu-main">
    <div class="section-menu-header">
      <h1 class="h0">
        <?= $menu_heading; ?>
      </h1>
    </div>

    <div class="section-menu-types">
      <?php foreach ( $data as $name => $types ) { ?>
        <div class="section-menu-type" data-menu-type="<?= $name; ?>">
          <?php foreach ( $types as $type_item ) { ?>
            <div class="section-menu-type-item">
              <?php if ( $type_item['heading'] ) { ?>
                <h2 class="h3">
                  <?= $type_item['heading']; ?>
                </h2>
              <?php } ?>
  
              <div class="section-menu-categories">
                <?php foreach ( $type_item['categories'] as $category ) { ?>
                  <div class="section-menu-category">
                    <?php if ( $category['heading'] ) { ?>
                      <h3 class="section-menu-category-heading">
                        <?= $category['heading']; ?>
                      </h3>
                    <?php } ?>
  
                    <div class="section-menu-products">
                      <?php foreach ( $category['products'] as $product ) { ?>
                        <div class="section-menu-product-main">
                          <p class="section-menu-product-heading">
                            <span>
                              <?= $product['name']; ?>
                            </span>

                            <?php if ( $product['variant_names'] ) { ?>
                              <span class="section-menu-product-variant-names">
                                <?= $product['variant_names']; ?>
                              </span>
                            <?php } ?>
                          </p>
  
                          <?php if ( $product['description'] ) { ?>
                            <p class="section-menu-product-description">
                              <?= $product['description']; ?>
                            </p>
                          <?php } ?>
                        </div>
  
                        <p class="section-menu-product-price">
                          <?= $product['price'] == 0 ? get_theme_string( 'Gratis' ) : $product['price'] . ',-'; ?>
                        </p>
                      <?php } ?>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } ?>

    </div>
  </div>
  
</section>

<?php 

sts_schema_graph( [
  $schema,
  sts_schema_website(),
  sts_schema_webpage( 
    name:        $menu_heading, 
    description: sts_option( 'archive.menu.description' ) ?: '',
    is_archive:  true
  )
] );

get_footer(); ?>