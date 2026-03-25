<?php 
get_header();

echo '<section class="section-404">';
  echo '<div class="pw:wrapper">';
    echo '<div class="grid">';
      echo '<div class="section-404__content clmns-12/12 laptop:clmns-6/12">';
        echo '<div>';
          echo '<h1 class="h1">' . sts_option( 'page.404.heading' ) . '</h1>';
          echo '<p class="my-2">' . sts_option( 'page.404.description' ) . '</p>';
          render_btn( [ 'title' => get_theme_string( 'Tilbage til forsiden' ), 'url' => home_url() ] );
        echo '</div>';      
      echo '</div>';      
    echo '</div>';    
  echo '</div>';
echo '</section>';

get_footer();