<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'beautiful', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'beautiful' ) );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'Beautiful Pro Theme', 'beautiful' ) );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/beautiful/' );
define( 'CHILD_THEME_VERSION', '1.1' );

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'beautiful_enqueue_scripts_styles' );
function beautiful_enqueue_scripts_styles() {

	wp_enqueue_script( 'beautiful-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700|Raleway:300,500,700|Pacifico', array(), CHILD_THEME_VERSION );

}

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'default-text-color'     => '000000',
	'header-selector'        => '.site-title a',
	'header-text'            => false,
	'height'                 => 120,
	'width'                  => 320,
) );

//* Add support for custom background
add_theme_support( 'custom-background', array(
	'default-color'         => 'ffffff',
	'default-image'         => get_stylesheet_directory_uri() . '/images/header-banner.png',
	'wp-head-callback'      => 'beautiful_background_callback',
) );

//* Add custom background callback
function beautiful_background_callback() {

	$background = get_background_image();
	$color = get_background_color();

	if ( ! $background && ! $color )
		return;

	echo trim( sprintf(
		"<style type='text/css'>.custom-background .site-header-banner { background: %s %s %s %s %s; } </style>",
		$background ? 'url('. $background .')' : '',
		$color ? '#'. $color : 'transparent',
		get_theme_mod( 'background_repeat', 'repeat' ),
		get_theme_mod( 'background_position_x', 'left' ),
		get_theme_mod( 'background_attachment', 'scroll' )
	) );
}

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Add support for after entry widget
add_theme_support( 'genesis-after-entry-widget-area' );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Unregister secondary sidebar
unregister_sidebar( 'sidebar-alt' );

//* Unregister secondary sidebar
add_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

//* Add custom body class to the head
add_filter( 'body_class', 'beautiful_custom_body_class' );
function beautiful_custom_body_class( $classes ) {

	$classes[] = 'beautiful';
	return $classes;

}

//* Hook before header widget area above header
add_action( 'genesis_before_header', 'beautiful_before_header' );
function beautiful_before_header() {

	genesis_widget_area( 'before-header', array(
		'before' => '<div class="before-header" class="widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );

}

//* Hook site header banner after header
add_action( 'genesis_after_header', 'beautiful_site_header_banner' );
function beautiful_site_header_banner() {

	if ( ! get_background_image() )
		return;

	echo '<div class="site-header-banner"></div>';

}

//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_after_header', 'genesis_do_subnav', 15 );


//* Hook welcome message widget area before content
add_action( 'genesis_before_loop', 'beautiful_welcome_message' );
function beautiful_welcome_message() {

	if ( ! is_front_page() || get_query_var( 'paged' ) >= 2 )
		return;

	genesis_widget_area( 'welcome-message', array(
		'before' => '<div class="welcome-message" class="widget-area">',
		'after'  => '</div>',
	) );

}

//* Modify the WordPress read more link
add_filter( 'the_content_more_link', 'beautiful_read_more' );
function beautiful_read_more() {

	return '<a class="more-link" href="' . get_permalink() . '">' . __( 'Continue Reading', 'beautiful' ) . '</a>';

}

//* Modify the content limit read more link
add_action( 'genesis_before_loop', 'beautiful_more' );
function beautiful_more() {

	add_filter( 'get_the_content_more_link', 'beautiful_read_more' );

}

add_action( 'genesis_after_loop', 'beautiful_remove_more' );
function beautiful_remove_more() {

	remove_filter( 'get_the_content_more_link', 'beautiful_read_more' );

}

//* Remove entry meta in entry footer
add_action( 'genesis_before_entry', 'beautiful_remove_entry_meta' );
function beautiful_remove_entry_meta() {

	//* Remove if not single post
	if ( ! is_single() ) {
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
	}

}

//* Modify the size of the Gravatar in the author box
add_filter( 'genesis_author_box_gravatar_size', 'beautiful_author_box_gravatar' );
function beautiful_author_box_gravatar( $size ) {

	return 180;

}

//* Modify the size of the Gravatar in the entry comments
add_filter( 'genesis_comment_list_args', 'beautiful_comments_gravatar' );
function beautiful_comments_gravatar( $args ) {

	$args['avatar_size'] = 100;
	return $args;

}

//* Hook split sidebar and bottom sidebar widget areas below primary sidebar
add_action( 'genesis_after_sidebar_widget_area', 'beautiful_extra_sidebars' );
function beautiful_extra_sidebars() {

	if ( is_active_sidebar( 'split-sidebar-left' ) || is_active_sidebar( 'split-sidebar-right' ) ) {

		echo '<div class="split-sidebars">';

			genesis_widget_area( 'split-sidebar-left', array(
				'before' => '<div class="split-sidebar-left" class="widget-area">',
				'after'  => '</div>',
			) );
			genesis_widget_area( 'split-sidebar-right', array(
				'before' => '<div class="split-sidebar-right" class="widget-area">',
				'after'  => '</div>',
			) );

		echo '</div>';

	}

	genesis_widget_area( 'bottom-sidebar', array(
		'before' => '<div class="bottom-sidebar" class="widget-area">',
		'after'  => '</div>',
	) );

}

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'beautiful_remove_comment_form_allowed_tags' );
function beautiful_remove_comment_form_allowed_tags( $defaults ) {

	$defaults['comment_notes_after'] = '';
	return $defaults;

}

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'before-header',
	'name'        => __( 'Before Header', 'beautiful' ),
	'description' => __( 'This is the before header widget area.', 'beautiful' ),
) );
genesis_register_sidebar( array(
	'id'          => 'welcome-message',
	'name'        => __( 'Welcome Message', 'beautiful' ),
	'description' => __( 'This is the welcome message widget area.', 'beautiful' ),
) );
genesis_register_sidebar( array(
	'id'          => 'split-sidebar-left',
	'name'        => __( 'Split Sidebar Left', 'beautiful' ),
	'description' => __( 'This is the left split sidebar widget area.', 'beautiful' ),
) );
genesis_register_sidebar( array(
	'id'          => 'split-sidebar-right',
	'name'        => __( 'Split Sidebar Right', 'beautiful' ),
	'description' => __( 'This is the right split sidebar widget area.', 'beautiful' ),
) );
genesis_register_sidebar( array(
	'id'          => 'bottom-sidebar',
	'name'        => __( 'Bottom Sidebar', 'beautiful' ),
	'description' => __( 'This is the bottom sidebar widget area.', 'beautiful' ),
) );

// Add Woocommerce cart count widget
class WoocommerceCartCount extends WP_Widget {
    function WoocommerceCartCount() {
        // Instantiate the parent object
        parent::__construct( false, 'Woocommerce Cart Count' );
    }

    function widget( $args, $instance ) {
        echo '<section class="widget woocommerce-cart-count">
        <div class="widget_wrap">';
        echo '<a class="cart-contents" href="' . WC()->cart->get_cart_url() . '" title="View your shopping cart"><span class="dashicons dashicons-cart"></span>' . sprintf (_n( '<span class="count">%d</span> item', '<span class="count">%d</span> items', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ). '</a>';
        echo '</div>
        </section>';
    }
}
add_action( 'widgets_init', function(){
    register_widget( 'WoocommerceCartCount' );
});

// Move primary nav into header
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Add custom header images (requires ACF field)
add_action( 'genesis_after_header', 'custom_header_banner' );
function custom_header_banner() {
    global $post;

    // show different image on blog and individual posts
    if ( is_home() OR is_single() ) {
        echo '<div class="site-header-banner">';
        echo '<div class="site-header-banner-image" style="';
        echo 'background-image: url(\'' . get_field( 'banner_image', 9 ) . '\');';
        echo '"></div>';
        echo '<div class="signup-form">';
            genesis_widget_area( 'banner_widget_area' );
        echo '</div>';
        echo '</div>';
    }
    // do not show on posts or archives
    elseif ( ( ! is_single() ) AND ( ! is_archive() ) ) {
        echo '<div class="site-header-banner">';
        echo '<div class="site-header-banner-image" style="';
        if ( get_field( 'banner_image', $post->ID ) ) {
            // if there is an image set, use it
            echo 'background-image: url(\'' . get_field( 'banner_image', $post->ID ) . '\');';
        } else {
            // otherwise, default to the home page’s image
            echo 'background-image: url(\'' . get_field( 'banner_image', 8 ) . '\');';
        }
        echo '"></div>';
        echo '<div class="signup-form">';
            genesis_widget_area( 'banner_widget_area' );
        echo '</div>';
        echo '</div>';
    }
}

// Add banner widget area
genesis_register_sidebar( array(
    'name'          => 'Banner Widget Area',
    'id'            => 'banner_widget_area',
));

// Register Custom Post Type Testimonials
function testimonial_post_type() {

	$labels = array(
		'name'                => _x( 'Testimonials', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Testimonial', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Testimonial', 'text_domain' ),
		'name_admin_bar'      => __( 'Testimonial', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Testimonial:', 'text_domain' ),
		'all_items'           => __( 'All Testimonials', 'text_domain' ),
		'add_new_item'        => __( 'Add New Testimonial', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Testimonial', 'text_domain' ),
		'edit_item'           => __( 'Edit Testimonial', 'text_domain' ),
		'update_item'         => __( 'Update Testimonial', 'text_domain' ),
		'view_item'           => __( 'View Testimonial', 'text_domain' ),
		'search_items'        => __( 'Search Testimonial', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'testimonial', 'text_domain' ),
		'description'         => __( 'Testimonials', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'revisions', ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-testimonial',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
        'rewrite'             => array(
            'slug'          => 'praise',
            'with_front'    => 'false',
        ),
	);
	register_post_type( 'testimonial', $args );

}

// Hook into the 'init' action
add_action( 'init', 'testimonial_post_type', 0 );

// Add custom thumbnail size for posts
add_image_size( 'home_featured', 708, 428, true );
add_image_size( 'blog_archive', 1570, 770, true );

// Add menu to footer
function add_footer_menu() {
    wp_nav_menu( array( 'name' => 'main-menu' ) );
    echo '<p>&copy;' . date( 'Y' ) . ' Jen Broyles. All rights reserved. | <a href="/disclaimer/">Disclaimer</a> | <a href="/ftc-disclosure/">Disclosure</a></p>';
}
remove_action( 'genesis_footer', 'genesis_do_footer' );
add_action( 'genesis_footer', 'add_footer_menu' );

// Add “Last Testimonial” widget
class LastTestimonialWidget extends WP_Widget {
    function LastTestimonialWidget() {
        // Instantiate the parent object
        parent::__construct( false, 'Last Testimonial' );
    }

    function widget( $args, $instance ) {
        echo '<section class="widget testimonial-wrapper">
        <div class="widget_wrap testimonial">';
        echo '<h2 class="primary-header">Testimonials</h2>';
        echo '<h3 class="secondary-header">Praises for Jen</h3>';
        // custom loop
        $blog_args = ( array(
            'post_type'         => 'testimonial',
            'posts_per_page'    => 1,
        ));
        $blog_query = new WP_Query( $blog_args );
        while ( $blog_query->have_posts() ) {
            $blog_query->the_post();

            echo '<article id="' . get_the_ID() . '">' . "\n";
                echo '<p>' . get_the_content() . '</p>' . "\n";
                echo '<h3 class="entry-title" itemprop="headline">' . get_the_title() . '</h3>' . "\n";
            echo '</article>' . "\n";
        }
        echo '</section>';
        wp_reset_postdata();
        echo '</div>';
    }
}
add_action( 'widgets_init', function(){
    register_widget( 'LastTestimonialWidget' );
});

// Add sidebar specifically for blog page
genesis_register_sidebar( array(
    'id'            => 'blog-sidebar',
    'name'          => 'Blog sidebar',
    'description'   => 'This is shown on the blog and post pages',
));

// Display on blog and single views
add_action( 'get_header', 'blog_sidebar_logic' );
function blog_sidebar_logic() {
    if ( is_home() || is_archive() || is_single() ) {
        remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
        add_action( 'genesis_after_content', 'blog_get_sidebar' );
    }
}
function blog_get_sidebar() {
    get_sidebar( 'blog' );
}

// Add “read more” links to excerpts on archive page
add_action( 'the_excerpt', 'add_read_more_link' );
function add_read_more_link( $output ) {
    global $post;
    return $output . '<a href="' . get_permalink( $post->ID ) . '" class="read-more" title="continue reading">continue reading</a>';
}

// check "create user account" by default
add_filter( 'woocommerce_create_account_default_checked', function( $isChecked) { return true; } );
