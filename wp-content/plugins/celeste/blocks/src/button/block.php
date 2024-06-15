<?php

use Celeste\Utilities\Icon;

register_block_type( CELESTE_BLOCKS_DIR . '/build/button' );

/**
 * Create an endpoint that returns a list of icons for the Editor
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'celeste/v1', '/theme/icons', [
        'methods' => 'GET',
        'callback' => function() {
            $icons = [];
            $files = glob( get_template_directory() . '/dist/assets/icons/*.svg' );
            foreach ( $files as $file ) {
                $name = basename( $file, '.svg' );
                $label = ucwords( str_replace( '-', ' ', $name ) );
                $path = str_replace( get_template_directory(), get_template_directory_uri(), $file );

                $icons[] = [
                    'name' => $name,
                    'label' => $label,
                    'url' => $path,
                    'svg' => file_get_contents( $file ),
                ];
            }
            return $icons;
        }
    ] );
} );

/**
 * Filter the output of the block content and add the icon
 *
 * @param string $block_content The block content.
 * @param array $block The full block, including name and attributes.
 * @param WP_Block $instance The block instance.
 *
 * @return string $block_content The block content.
 */
add_filter( 'render_block_resknow/button', function( $block_content, $block, $instance ) {
    if ( ! empty( $block['attrs']['icon'] ) ) {
        $icon = $block['attrs']['icon'];
        $icon_html = Icon::render($icon);
        $block_content = str_replace('[icon]', $icon_html, $block_content);
    }

    return $block_content;
}, 10, 3 );