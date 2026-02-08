<?php 
// @@ Render an .ratio-container from an ACF Image Array
// NOTE: This uses a modular pipeline that maps specific device aspect ratios to both CSS layout variables and calculated 'over-sampled' image widths for the sizes attribute.
function render_acf_img( $image_array, $ratios = [ 'desktop' => '16:9', 'mobile' => '1:1' ], $size = 'full', $loading = 'lazy' ) {
  if ( ! $image_array || empty( $image_array ) ) return;
  
  $base_ratio = $image_array[ 'width' ] / $image_array[ 'height' ];


  // ## helper: convert aspect-strings(e.g. "16:9") to float ratios
  $parse_ratio = function( $r ) use ( $base_ratio ) {
    if ( $r === 'default' ) return $base_ratio;
    $parts = explode( ':', $r );
    return ( count( $parts ) === 2 ) ? (float) $parts[0] / (float) $parts[1] : 1;
  };


  // ## map the ratios to scales (e.g. result: ['desktop' => 1.2, 'mobile' => 1.8])
  $scales = array_map( function( $r_str ) use ( $parse_ratio, $base_ratio) {
    $target_ratio = $parse_ratio( $r_str );
    return max( 1, $base_ratio / $target_ratio );
  }, $ratios );


  // ## define base widths (visual widths)
  $all_widths = [
    'full' => [ 'desktop' => 1920, 'tablet' => 1440, 'mobile' => 980 ],
    '1/2'  => [ 'desktop' => 980,  'tablet' => 720,  'mobile' => 490 ],
    '1/4'  => [ 'desktop' => 490,  'tablet' => 360,  'mobile' => 360 ],
  ];


  // ## calculate scaled widths (matches the scale to the specific device width)
  $base_width = $all_widths[ $size ] ?? $all_widths[ 'full' ];
  $scaled_widths = [
    'laptop-up' => round( $base_width[ 'desktop' ] * $scales[ 'desktop'] ),
    'tablet-up' => round( $base_width[ 'tablet' ]  * $scales[ 'desktop'] ), // tablet usually follows desktop crop
    'mobile'    => round( $base_width[ 'mobile' ]  * ( $scales[ 'mobile' ] ?? $scales[ 'desktop' ] ) )
  ];

  $attr = [
    'loading' => $loading,
    'height'  => $image_array[ 'height' ],
    'width'   => $image_array[ 'width' ],
    'class'   => 'ratio-container-img',
    'sizes'   => "(min-width: 1440px) {$scaled_widths['laptop-up']}px, (min-width: 980px) {$scaled_widths['tablet-up']}px, {$scaled_widths['mobile']}px",
  ];


  // ## build CSS variables
  $style_vars = "";
  foreach ( $ratios as $device => $r_str ) {
    $val = 1 / $parse_ratio( $r_str );
    $style_vars .= "--ratio-{$device}: {$val}; ";
  }
 
  echo '<div class="ratio-container" style="' . esc_attr( $style_vars ) . '">';
    echo wp_get_attachment_image( $image_array[ 'id' ], 'full', false, $attr);
  echo '</div>';
};


// @@ GET AN ICON FROM THE CATALOGUE
function get_icon( $name ) {
  $catalogue = [
    'hamburger' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M28 11V13H4V11H28Z" fill="currentColor"/><path d="M28 19V21H4V19H28Z" fill="currentColor"/></svg>',
    'x'         => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.9706 7.02944L25.3848 8.44365L8.41421 25.4142L7 24L23.9706 7.02944Z" fill="currentColor"/><path d="M24.9706 23.9706L23.5563 25.3848L6.58579 8.41421L8 7L24.9706 23.9706Z" fill="currentColor"/></svg>',
  ];

  return $catalogue[ $name ] ?? null;
}