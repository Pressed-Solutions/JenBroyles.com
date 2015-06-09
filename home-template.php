<?php
/**
 * Template Name: Home Page Template
 */

// remove default loop and add custotm loops
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'home_loop' );

function home_loop() {
    global $paged;

    echo '<h2 class="primary-header">Recent Blog</h2>';
    echo '<h3 class="secondary-header">Read the latest news from our blog</h3>';
    genesis_custom_loop( array( 'post_type' => 'post' ) );

    the_content();

    echo '<h2 class="primary-header">Testimonials</h2>';
    echo '<h3 class="secondary-header">Praises for Jen</h3>';
    genesis_custom_loop( array( 'post_type' => 'testimonial' ) );
}

// call the rest of the template
genesis();
