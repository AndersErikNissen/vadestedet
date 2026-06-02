<?php
$relation = $args[ 'relation' ] ?? null;

if ( empty( $relation ) ) return;

// @@ BLOCK
$block_relation = $relation . 'option_1_block_';
$color_theme    = get_field( $block_relation . 'color_theme' );

$block_relation = $relation . 'faq_block_';
$heading        = get_field( $block_relation . 'heading' );
$items          = get_field( $block_relation . 'items' );

if ( ! is_array( $items ) ) return; ?>

<section class="section-faq bg:section color-theme-section color-theme-swap-trigger" data-color-theme="<?= $color_theme; ?>">
  <div class="pw:wrapper grid">
    <div class="section-faq-grid-item">
      <?php if ( $heading ) : ?>
        <div class="pb-2">
          <h2 class="h2">
            <?= $heading; ?>
          </h2>
        </div>
      <?php endif; ?>
  
      <ul class="accordion">
        <?php for ( $i = 1; $i <= 12; $i++ ) : 
          $prefix = $block_relation . 'sub_field_' . $i . '_';
  
          $question = $items[ $prefix . 'question' ] ?? null;
          $answer   = $items[ $prefix . 'answer' ]   ?? null;
          
          if ( ! $question || ! $answer ) continue; ?>
  
          <li class="accordion__item">
            <div class="accordion__header l2">
              <h4 class="accordion__title">
                <?= $question; ?>
              </h4>
  
              <svg class="accordion__icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M28 12.4924L16 22L4 12.4924L5.18106 11L16 19.5718L26.8189 11L28 12.4924Z" fill="currentColor"/>
              </svg>
            </div>
  
            <div class="accordion__drawer">
              <div class="accordion__content">
                <p class="accordion__text"><?= $answer; ?></p>
              </div>
            </div>
          </li>
        <?php endfor; ?>
      </ul>
    </div>
  </div>
</section>