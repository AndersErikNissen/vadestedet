<?php
$post_type = $args['post_type'] ?? get_post_type();

$heading     = sts_option( 'archive.' . $post_type . '.heading' )     ?? false;
$description = sts_option( 'archive.' . $post_type . '.description' ) ?? false; 

if ( ! $heading && ! $description ) return; ?>

<div class="archive-header">
  <?php if ( $heading ) { ?>
    <h1 class="h1 mb-2">
      <?= $heading; ?>
    </h1>
  <?php }

  if ( $description ) { ?>
    <p class="l2">
      <?= $description; ?>
    </p>
  <?php } ?> 
</div>