<?php
$block_relation = 'section_menu_menu_block_';
$args = wp_parse_args( $args ?? [], [ 'class' => 'block-menu' ] );

[ 'class' => $class ] = $args;

$heading = get_field( $block_relation . 'heading' );
$items   = get_field( $block_relation . 'items'   );

if ( ! $heading ) return; 

echo '<div class="' . esc_attr( $class ) . '">';
  echo '<div class="">';
    echo '<h3 class="h3 mb-1">' . $heading . '</h3>';
    echo '<ul class="column gap-1">';
      for ( $i = 1; $i <= 20; $i++ ) {
        $prefix = $block_relation . 'sub_field_' . $i . '_';

        $name        = $items[ $prefix . 'name' ]        ?? null;
        $description = $items[ $prefix . 'description' ] ?? null;
        $price       = $items[ $prefix . 'price' ]       ?? null;

        if ( ! $name || ! $price ) continue;

        echo '<li class="">';
          echo '<h4 class="h4">' . $name . '</h4>';
          if ( $description ) echo "<p>{$description}</p>";
          echo '<p class="block-menu__price">' . $price . '</p>';
        echo '</li>';
      };
    echo '</ul>';
  echo '</div>';
echo '</div>';