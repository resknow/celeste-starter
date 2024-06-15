<?php

use Celeste\Assets;

add_filter( 'celeste.icon', 'theme_login_icon' );
add_filter( 'admin_footer_text', 'theme_admin_footer_text' );

add_action( 'after_setup_theme', 'theme_setup' );
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts', 100 );
add_action( 'after_setup_theme', 'theme_enqueue_editor_style' );
add_action( 'enqueue_block_editor_assets', 'theme_enqueue_block_editor_assets' );

/**
 * Frontend Styles
 */
function theme_enqueue_styles() {
    Assets::add( 'style', 'global', 'dist/css/global.css' );
}

/**
 * Frontend Scripts
 */
function theme_enqueue_scripts() {
    Assets::add( 'script', 'global', 'dist/js/global.js' );
}

/**
 * Block Editor Styles
 */
function theme_enqueue_editor_style() {
    add_editor_style( get_template_directory_uri() . '/dist/css/global.css' );
}

/**
 * Block Editor Assets
 */
function theme_enqueue_block_editor_assets() {
    Assets::add( 'script', 'theme-editor', 'editor.js', [ 'wp-blocks', 'wp-dom' ] );
}

/**
 * Theme Setup
 */
function theme_setup() {

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Editor Styles
    add_theme_support( 'editor-styles' );

    // Block Editor Support
    add_theme_support( 'align-wide' );
    remove_theme_support( 'core-block-patterns' );

    // Add Featured Image Support for posts
    add_theme_support( 'post-thumbnails' );

    // Add HTML5 Support
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ] );

    // Set a better Thumbnail Size
    update_option( 'thumbnail_size_w', 240 );
    update_option( 'thumbnail_size_h', 240 );

    // Register Menu
    register_nav_menu( 'main-menu', __('Main Menu') );

}

/**
 * Custom Login Icon
 */
function theme_login_icon( string $icon ) {
    $path = get_template_directory() . '/login-icon.png';
    $url = get_template_directory_uri() . '/login-icon.png';
    return file_exists( $path ) ? $url : $icon;
}

/**
 * Edit the footer text
 */
function theme_admin_footer_text() {
    return 'Created by <a href="https://www.resknow.co.uk"><em>Resknow</em></a>, with <a href="https://wordpress.org"><em>WordPress</em></a>.';
}