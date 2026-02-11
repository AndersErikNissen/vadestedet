<?php
get_header();

if ( have_posts() ) {

  while ( have_posts() ) {
    the_post();
    
    /**
     * this pages handles: home.php , archive.php
     * note: don't have sections on home.php - that way we can handle home and archive the same way
     * 
     * options:
     * title
     * description
     * 
     * should we the values based on the post_type?
     */

    
  }
  
}

get_footer();

