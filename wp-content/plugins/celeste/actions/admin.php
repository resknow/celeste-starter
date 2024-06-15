<?php

use Celeste\Assets;

add_action( 'admin_init', 'celeste_register_admin_theme' );
add_action( 'admin_enqueue_scripts', 'celeste_enqueue_admin_style' );
add_action( 'wp_enqueue_scripts', 'celeste_enqueue_admin_bar_style' );
add_action( 'after_setup_theme', 'celeste_enqueue_block_editor_style' );

function celeste_register_admin_theme() {
    wp_admin_css_color( 'celeste', __( 'Celeste' ),
        CELESTE_PLUGIN_URL . '/assets/css/admin-theme.css',
        [ '#1a1b1e', '#fff', '#ea2e85' , '#2d42c1' ]
    );
}

function celeste_enqueue_admin_bar_style() {
    if ( !is_user_logged_in() ) return;
    wp_enqueue_style( 'celeste-admin-bar-theme', CELESTE_PLUGIN_URL . '/assets/css/admin-bar.css' );
}

/**
 * Admin CSS
 */
function celeste_enqueue_admin_style() {
    wp_enqueue_style( 'celeste-admin-css', CELESTE_PLUGIN_URL . '/assets/css/admin.css' );
}

function celeste_enqueue_block_editor_style() {
    add_editor_style( CELESTE_PLUGIN_URL . '/assets/css/block-editor.css' );
}