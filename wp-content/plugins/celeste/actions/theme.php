<?php

add_action( 'init', 'celeste_cleanup_init' );
add_action( 'after_setup_theme', 'celeste_cleanup_after_theme_setup' );

/**
 * Celeste default Theme Setup
 */
function celeste_cleanup_init() {

    // Remove Default Skip Link
    remove_action( 'wp_footer', 'the_block_template_skip_link' );

    // Remove Emoji Styles
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );

    // Remove Generator Meta tag
    remove_action( 'wp_head', 'wp_generator' );

    // Remove RSD Link
    remove_action( 'wp_head', 'rsd_link' );

    // Remove Manifest Link
    remove_action( 'wp_head', 'wlwmanifest_link' );

    // Remove Shortlink
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );

}

function celeste_cleanup_after_theme_setup() {
    // Remove the REST API lines from the HTML Header
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );

    // Turn off oEmbed auto discovery.
    add_filter( 'embed_oembed_discover', '__return_false' );

    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}