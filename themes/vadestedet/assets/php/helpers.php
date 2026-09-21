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
  $style_vars = "--ratio-laptop:{$desktop_data['inverse_ratio']};--ratio-mobile:{$mobile_data['inverse_ratio']};";

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


// @@ RENDER ADVANCED BUTTON VIA LINK
function render_btn( $link, $class = 'btn' ) {
  $url = $link['url'];

  if ( ! is_array( $link ) || empty( $url ) ) return;

  $target = isset( $link['target'] ) ? $link['target'] : '_self'; ?>

  <a class="<?= esc_attr( $class ); ?>" href="<?= esc_url( $url ); ?>" target="<?= esc_attr( $target ); ?>">
    <span class="btn-label">
      <?= esc_html( $link['title'] ); ?>
    </span>
  </a>
<?php }


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
    'hamburger' =>  '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">' .
                      '<path d="M28 25H5V23H28V25Z" fill="currentColor"/>' .
                      '<path d="M28 17H5V15H28V17Z" fill="currentColor"/>' .
                      '<path d="M28 9H5V7H28V9Z" fill="currentColor"/>' .
                    '</svg>',
    'x'         =>  '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">' .
                      '<path d="M24.8379 8.5752L17.4141 15.998L24.8418 23.4258L23.4277 24.8398L16 17.4121L8.57422 24.8389L7.16016 23.4248L14.5859 15.998L7.16406 8.57715L8.57812 7.16211L16 14.584L23.4238 7.16113L24.8379 8.5752Z" fill="currentColor"/>' .
                    '</svg>',
    'complexity' => '<svg width="160" height="160" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">' .
                      '<path fill-rule="evenodd" clip-rule="evenodd" d="M97.2686 63.2803L105.305 80.2646L97.2695 97.25L80.2842 105.285L63.2979 97.25L55.2637 80.2646L63.2979 63.2803L80.2842 55.2432L97.2686 63.2803ZM69.3066 69.2871L64.1133 80.2646L69.3066 91.2412L80.2842 96.4355L91.2617 91.2412L96.4551 80.2646L91.2607 69.2871L80.2842 64.0938L69.3066 69.2871Z" fill="currentColor"/>' .
                      '<path fill-rule="evenodd" clip-rule="evenodd" d="M93.9248 16.001L100.658 28.2764L115.279 26.3223L134.641 45.6836L131.872 58.4854L144.818 67.123V93.6963L131.871 102.334L134.643 115.137L115.137 134.643L102.333 131.872L93.6963 144.818L66.8945 144.819L60.1592 132.543L45.5371 134.499L26.3193 115.282L28.2744 100.66L16 93.9248V67.124L28.9414 58.4844L26.1768 45.6826L45.5371 26.3223L60.1582 28.2744L66.8945 16L93.9248 16.001ZM64.5342 36.9316L48.3984 34.7744L34.918 48.2549L37.9111 62.1162L24 71.4023V89.1885L36.9297 96.2861L34.7734 112.422L48.3965 126.045L64.5352 123.888L71.6309 136.819L89.417 136.818L98.7012 122.902L112.564 125.901L125.901 112.564L122.899 98.7012L136.818 89.417V71.4033L122.901 62.1182L125.899 48.2568L112.419 34.7764L96.2822 36.9336L89.1885 24.001L71.6299 24L64.5342 36.9316Z" fill="currentColor"/>' .
                    '</svg>',
    'category' =>   '<svg width="160" height="160" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">' .
                      '<path d="M97.4922 27.2568L105.691 44.6055L100.142 58.2607L105.313 60.2979L105.303 77.6523L95.1895 77.6719L95.1826 86.8662L112.286 111.357L124.166 119.757L124.151 140.959H36L36.0146 119.756L47.8887 111.351L64.7891 86.8564L64.7959 77.6709L54.6709 77.6992L54.6826 60.3301L59.8486 58.2842L54.3262 44.6279L62.5439 27.2773L80.0205 19L97.4922 27.2568ZM68.5508 33.2832L63.0576 44.8809L70.2871 62.7578L62.6777 65.7686L62.6758 69.6768L72.8018 69.6484L72.7881 89.3506L53.668 117.062L44.0107 123.896L44.0059 132.959H116.157L116.162 123.896L106.527 117.084L106.128 116.511L87.1816 89.3809L87.1953 69.6875L97.3066 69.667L97.3096 65.7441L89.6855 62.7412L96.957 44.8467L91.4824 33.2656L80.0244 27.8496L68.5508 33.2832Z" fill="currentColor"/>' .
                    '</svg>',
    'difficulty' => '<svg width="160" height="160" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">' .
                      '<path d="M56.4033 84.1562V130.28L40.2021 147L24 130.28V84.1562L40.2021 67.4375L56.4033 84.1562ZM96.4824 59.0625V130.249L80.2812 146.999L64.0791 130.249V59.0625L80.2812 42.3125L96.4824 59.0625ZM136.56 29.75V130.249L120.358 146.999L104.156 130.249V29.75L120.358 13L136.56 29.75ZM32 87.3965V127.04L40.2012 135.504L48.4033 127.04V87.3965L40.2012 78.9326L32 87.3965ZM72.0791 62.2979V127.013L80.2803 135.492L88.4824 127.013V62.2979L80.2803 53.8184L72.0791 62.2979ZM112.156 32.9854V127.013L120.357 135.492L128.56 127.013V32.9854L120.357 24.5059L112.156 32.9854Z" fill="currentColor"/>' .
                    '</svg>',
    'players'    => '<svg width="160" height="160" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">' .
                      '<path fill-rule="evenodd" clip-rule="evenodd" d="M134.208 90.4658L146.998 132.866H14L26.793 90.4658L53.8311 77.6748L80.499 90.292L107.172 77.6748L134.208 90.4658ZM80.499 99.1426L53.8301 86.5244L33.4268 96.1777L24.7695 124.865H136.229L127.574 96.1787L107.171 86.5244L80.499 99.1426Z" fill="currentColor"/>' .
                      '<path fill-rule="evenodd" clip-rule="evenodd" d="M71.0303 35.1387L79.168 52.3379L71.0303 69.5371L53.8311 77.6748L36.6309 69.5371L28.4941 52.3379L36.6309 35.1377L53.8311 27.001L71.0303 35.1387ZM42.6377 41.1445L37.3428 52.3369L42.6377 63.5293L53.8301 68.8242L65.0225 63.5293L70.3184 52.3359L65.0225 41.1445L53.8311 35.8496L42.6377 41.1445Z" fill="currentColor"/>' .
                      '<path fill-rule="evenodd" clip-rule="evenodd" d="M124.37 35.1377L132.508 52.3369L124.371 69.5371L107.172 77.6748L89.9707 69.5371L81.834 52.3369L89.9717 35.1377L107.171 27L124.37 35.1377ZM95.9785 41.1445L90.6836 52.3369L95.9795 63.5293L107.171 68.8242L118.363 63.5293L123.658 52.3369L118.362 41.1455L107.172 35.8496L95.9785 41.1445Z" fill="currentColor"/>' .
                    '</svg>',
    'playtime'   => '<svg width="160" height="160" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">' .
                      '<path d="M125.25 35.2842L146.534 80.2666L125.251 125.251L80.2666 146.534L35.2812 125.253L14 80.2666L35.2812 35.2822L80.2666 14L125.25 35.2842ZM41.2881 41.29L22.8496 80.2666L41.2881 119.244L80.2656 137.684L119.243 119.243L137.684 80.2666L119.241 41.291L80.2666 22.8496L41.2881 41.29ZM84.2666 38.2861V77.957L107.26 91.2324L103.26 98.1611L76.2666 82.5762V38.2861H84.2666Z" fill="currentColor"/>' .
                    '</svg>',
  ];

  return $catalogue[ $name ] ?? null;
}