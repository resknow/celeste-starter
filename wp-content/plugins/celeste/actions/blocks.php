<?php

use Celeste\Context;

add_action( 'init', 'celeste_register_block_styles' );
add_action( 'init', 'celeste_register_native_blocks' );
add_action( 'init', 'celeste_register_block_pattern_categories' );
add_action( 'wp_enqueue_scripts', 'celeste_enqueue_block_assets', 100 );
add_action( 'after_setup_theme', 'celeste_enqueue_editor_styles' );
add_action( 'init', 'celeste_register_block_bindings_for_globals' );

/**
 * Register Celeste Blocks
 */
function celeste_register_native_blocks() {
    $blocks = glob( CELESTE_BLOCKS_DIR . '/src/**/block.php' );
    if ( empty( $blocks ) ) return;

    foreach ( $blocks as $block ) {
        require_once $block;
    }
}

/**
 * Custom Block Categories
 *
 * You can register block patterns from your theme in lib/context.php
 */
function celeste_register_block_pattern_categories() {
    if ( $block_pattern_categories = Context::get( 'blocks.patterns.categories' ) ) {
        foreach ( $block_pattern_categories as $name => $category ) {
            register_block_pattern_category( $name, $category );
        }
    }
}

/**
 * Custom Block Styles
 *
 * You can register block styles in lib/context.php
 * Also see https://developer.wordpress.org/reference/functions/register_block_style/
 * for the required array format
 */
function celeste_register_block_styles() {
    if ( $block_styles = Context::get( 'blocks.styles' ) ) {
        foreach ( $block_styles as $style ) {
            $block = $style['block'];
            unset( $style['block'] );
            register_block_style( $block, $style );
        }
    }
}

/**
 * Globals Block Bindings
 */
function celeste_register_block_bindings_for_globals() {
    $globals = Context::get( 'globals' );

    if ( ! $globals ) return;

    foreach ( $globals as $key => $value ) {

        if ( ! is_string( $value ) ) continue;

        register_block_bindings_source( 'celeste/' . $key, [
            'label' => ucwords ( str_replace( '_', ' ', $key ) ),
            'get_value_callback' => function() use ( $key, $value ) {
                return $value;
            }
        ] );
    }
}