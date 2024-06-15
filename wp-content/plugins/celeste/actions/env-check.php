<?php

add_action( 'admin_notices', 'celeste_acf_check' );
add_action( 'admin_notices', 'celeste_theme_check' );

/**
 * Check if ACF Pro is installed and if not, display a notice
 */
function celeste_acf_check() {
    if ( ! class_exists( 'acf_pro' ) ) {
        $message = __( 'The Celeste plugin requires the latest version of ACF Pro to be installed and activated!', 'celeste' );
        printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
    }
}

/**
 * Check if the theme is Celeste and if not, display a notice
 * in the admin area
 */
function celeste_theme_check() {
    if ( wp_get_theme()->get( 'TextDomain' ) !== 'celeste' ) {
        $message = __( 'The Celeste plugin is designed for the Celeste theme, activating it with another theme may result in unexpected side effects!', 'celeste' );
        printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
    }
}