<?php

use Celeste\Assets;
use Celeste\Utilities\FindBlocks;

/**
 * Enqueue Block Assets
 *
 * This function must be used inside a wp_enqueue_scripts action
 * to ensure that the global $post is available.
 *
 * This function will search the post content for blocks and
 * enqueue the corresponding block assets.
 */
function celeste_enqueue_block_assets() {
    // Block Assets
    $find_blocks = new FindBlocks( get_the_content() );
    $found_items = $find_blocks->find( $find_blocks->parse() );
    $blocks = $found_items['blocks'] ?? [];
    $components = $found_items['components'] ?? [];

    if ( ! empty( $blocks ) ) {

        foreach ( $blocks as $name ) {
            $name = str_replace( 'acf/', '', $name );

            if ( file_exists( get_template_directory() . '/dist/blocks/' . $name . '/style.css' ) ) {
                Assets::block( $name, 'style' );
            }

            if ( file_exists( get_template_directory() . '/dist/blocks/' . $name . '/main.js' ) ) {
                Assets::block( $name, 'script' );
            }
        }
    }

    /**
     * Enqueue Components found inside blocks
     */
    if ( ! empty( $components ) ) {
        celeste_enqueue_component_assets( $components );
    }
}

/**
 * Enqueue Component Assets
 *
 * This function must be used inside a twilight.components.present filter
 * and be passed the component list
 *
 * @param array $components
 */
function celeste_enqueue_component_assets( array $components ) {
    if ( empty($components) ) {
        return;
    }

    foreach ( $components as $name ) {
        $name = str_replace( '.', '/', $name );

        if ( file_exists( get_template_directory() . '/dist/components/' . $name . '/style.css' ) ) {
            Assets::component( $name, 'style' );
        }

        if ( file_exists( get_template_directory() . '/dist/components/' . $name . '/main.js' ) ) {
            Assets::component( $name, 'script' );
        }

    }
}

/**
 * Enqueue Editor Block & Component Styles
 *
 * ! Warning: This function will enqueue assets for every block
 * and component in your theme, in the editor.
 */
function celeste_enqueue_editor_styles() {
    $styles = glob(get_template_directory() . '/dist/{blocks,components}/**/style.css', GLOB_BRACE);

    foreach ( $styles as $style ) {
        add_editor_style( str_replace( get_template_directory() . '/', '', $style ) );
    }
}