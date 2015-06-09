<?php
/**
 * Template Name: Home Page Template
 */

// remove default loop and add custotm loops
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'home_loop' );

function home_loop() {
    global $paged;

    echo '<h2 class="primary-header">Recent Blog</h2>' . "\n";
    echo '<h3 class="secondary-header">Read the latest news from our blog</h3>' . "\n";
    echo '<section class="home-blog-wrapper">' . "\n";
    // custom loop
    $blog_args = ( array(
        'post_type'         => 'post',
        'posts_per_page'    => 3,
    ));
    $blog_query = new WP_Query( $blog_args );
    while ( $blog_query->have_posts() ) {
        $blog_query->the_post();

        echo '<article id="' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">' . "\n";
            echo '<a href="' . get_permalink() . '" title="' . esc_attr( $blog_query->post->post_title ) . '">' . get_the_post_thumbnail( $blog_query->post->ID, 'home_featured' ) . '</a>' . "\n";
            echo '<h2 class="entry-title" itemprop="headline">' . "\n";
                echo '<a href="' . get_permalink() . '" title="' . esc_attr( $blog_query->post->post_title ) . '">' . get_the_title() . '</a>' . "\n";
            echo '</h2>' . "\n";
            echo '<p>' . get_the_excerpt() . '</p>' . "\n";
            echo '<p><a class="read-more" href="' . get_permalink() . '">continue reading &gt;</a></p>';
        echo '</article>' . "\n";
    }
    wp_reset_postdata();
    echo '</section>' . "\n";

    echo '<section class="home-content-wrapper">';
    the_content();
    echo '</section>';

    echo '<h2 class="primary-header">Testimonials</h2>';
    echo '<h3 class="secondary-header">Praises for Jen</h3>';
    echo '<section class="home-testimonial-wrapper">';
    // custom loop
    $blog_args = ( array(
        'post_type'         => 'testimonial',
        'posts_per_page'    => 2,
    ));
    $blog_query = new WP_Query( $blog_args );
    while ( $blog_query->have_posts() ) {
        $blog_query->the_post();

        echo '<article id="' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">' . "\n";
            echo '<p>' . get_the_content() . '</p>' . "\n";
            echo '<h3 class="entry-title" itemprop="headline">' . get_the_title() . '</h3>' . "\n";
        echo '</article>' . "\n";
    }
    echo '</section>';
    wp_reset_postdata();
}

// call the rest of the template
genesis();
