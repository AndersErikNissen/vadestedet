<?php 
// @@ RENDER A RATIO CONTAINER (VIA ACF IMAGE AS ARRAY)
function render_acf_img( $desktop_img, $mobile_img = null, $ratios = [ 'desktop' => '16:9', 'mobile' => '1:1' ], $size = 'full', $loading = 'lazy' ) {
  if ( ! $desktop_img ) return;


  // ## use desktop_img as fallback
  $mobile_img = $mobile_img ?: $desktop_img;


  // ## helper: parse ratios & calculate scale
  $get_scale = function( $img, $target_ratio_str ) {
    $base_ratio = $img[ 'width' ] / $img[ 'height' ];
    $parts = explode( ':', $target_ratio_str );
    $target_ratio = ( count( $parts ) === 2 ) ? (float) $parts[0] / (float) $parts[1] : $base_ratio;
    return [
      'scale' => max( 1, $base_ratio / $target_ratio ),
      'inverse_ratio' => 1 / $target_ratio
    ];
  };


  // ## calculated scales
  $desktop_data = $get_scale( $desktop_img, $ratios['desktop'] );
  $mobile_data  = $get_scale( $mobile_img, $ratios['mobile'] );


  // ## define base widths (visual widths)
  $all_widths = [
    'full' => [ 'desktop' => 1920, 'tablet' => 1440, 'mobile' => 980 ],
    '1/2'  => [ 'desktop' => 980,  'tablet' => 720,  'mobile' => 490 ],
    '1/4'  => [ 'desktop' => 490,  'tablet' => 360,  'mobile' => 360 ],
  ];
  $base_width = $all_widths[ $size ] ?? $all_widths[ 'full' ];


  // ## calculate over-sampled widths for 'sizes' attribute
  $scaled_widths = [
    'laptop-up' => round( $base_width[ 'desktop' ] * $desktop_data[ 'scale' ] ),
    'tablet-up' => round( $base_width[ 'tablet' ]  * $desktop_data[ 'scale' ] ), // tablet usually follows desktop crop
    'mobile'    => round( $base_width[ 'mobile' ]  * $mobile_data[ 'scale' ]  )
  ];


  // ## build CSS variables
  $style_vars = "--ratio-desktop:{$desktop_data['inverse_ratio']};--ratio-mobile:{$mobile_data['inverse_ratio']};";

  echo '<div class="ratio-container" style="' . esc_attr( $style_vars ) . '">';
    echo '<picture class="ratio-container-item">';
      echo sprintf(
        '<source media="(max-width: 979px)" srcset="%s" sizes="%spx">',
        wp_get_attachment_image_srcset( $mobile_img[ 'id' ], 'full' ),
        $scaled_widths[ 'mobile' ]
      );

      echo wp_get_attachment_image( $desktop_img[ 'id' ], 'full', false, [
        'loading' => $loading,
        'class'   => 'ratio-container-item',
        'width'   => $desktop_img[ 'width' ],
        'height'  => $desktop_img[ 'height' ],
        'sizes'   => "(min-width: 1440px) {$scaled_widths[ 'laptop-up' ]}px, {$scaled_widths[ 'tablet-up' ]}px",
      ]);
    echo '</picture>';
  echo '</div>';
};


// @@ RENDER BADGES
function render_badges( $badges ) {
  if ( ! is_array( $badges ) || is_array( $badges ) && empty( $badges ) ) {
    return;
  }

  echo '<div class="badges">';
    foreach( $badges as $badge ) {
      echo '<span class="badge">' . $badge . '</span>';
    }
  echo '</div>';
}


// @@ RENDER BTN VIA LINK
function render_btn( $link, $class = 'btn' ) {
  if ( ! is_array( $link ) || empty( $link['url'] ) ) {
    return;
  }

  printf(
    '<a class="%s" href="%s" target="%s">%s</a>',
    esc_attr( $class ),
    esc_url( $link[ 'url' ] ),
    esc_attr( $link[ 'target' ] ?: '_self' ),
    esc_html( $link[ 'title' ] )
  );
}


// @@ GET LOCALIZED DATE FROM ACF FIELD
function get_localized_acf_date( $acf_date, $format = 'j F, Y' ) {
  if ( ! $acf_date ) return '';

  $date_obj = DateTime::createFromFormat('Ymd', $acf_date);
  
  // ## return raw if format is wrong
  if ( ! $date_obj ) return $acf_date; 

  return date_i18n( $format, $date_obj->getTimestamp() );
}


// @@ GET LOCALIZED THEME STRING 
function get_theme_string( $string ) {
  return function_exists( 'pll__' ) ? pll__( $string ) : $string; 
}


// @@ GET AN ICON FROM THE CATALOGUE
function get_icon( $name ) {
  $catalogue = [
    'hamburger' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 11V13H4V11H28Z" fill="currentColor"/><path d="M28 19V21H4V19H28Z" fill="currentColor"/></svg>',
    'x'         => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.9706 7.02944L25.3848 8.44365L8.41421 25.4142L7 24L23.9706 7.02944Z" fill="currentColor"/><path d="M24.9706 23.9706L23.5563 25.3848L6.58579 8.41421L8 7L24.9706 23.9706Z" fill="currentColor"/></svg>',
  ];

  return $catalogue[ $name ] ?? null;
}