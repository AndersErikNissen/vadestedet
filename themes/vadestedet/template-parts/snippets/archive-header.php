<?php
$post_type = get_post_type();

$heading = sts_option( 'archive.' . $post_type . '.heading' ) ?? false;
$description = sts_option( 'archive.' . $post_type . '.description' ) ?? false; 

if ( ! $heading && ! $description ) return;

echo '<div class="archive-header pb-2">';
  if ( $heading ) {
    echo '<h1 class="h1 mb-1">';
      echo $heading;
    echo '</h1>';
  }

  if ( $description ) {
    echo '<p class="l1">';
      echo $description;
    echo '</p>';
  }
echo '</div>';