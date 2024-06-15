<?php

use Celeste\Context;

add_action( 'wp_footer', 'celeste_render_warnings', 100 );

/**
 * Render Warnings
 *
 * This function will render any warnings that have been added to the context
 * if the site is running in a local environment.
 */
function celeste_render_warnings() {

    if ( ! celeste_is_local() ) return;

    $warnings = Context::get( 'celeste.warnings', [] );
    if ( ! empty( $warnings ) ) {
        echo '<style>.celeste-warning { background-color: #b91c1c; color: #f3e5f5; position: fixed; bottom: 12px; left: 12px; padding: 6px 12px; border-radius: 4px; font-size: 14px; }</style>';
        echo '<div class="celeste-warning">';
        foreach ( $warnings as $warning ) {
            printf( '<p><strong>Warning:</strong> %s</p>', $warning );
        }
        echo '</div>';
    }

}