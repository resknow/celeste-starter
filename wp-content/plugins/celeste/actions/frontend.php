<?php

add_action( 'wp_footer', 'celeste_render_reload_script', 100 );
add_action( 'save_post', 'celeste_trigger_reload_on_save_post', 100, 3 );
add_action( 'current_screen', 'celeste_sync_localhost_with_current_screen', 100 );
add_action( 'acf/options_page/save', 'celeste_trigger_reload_on_acf_options_save', 100 );

/**
 * Render reload script in the footer when in a local environment
 */
function celeste_render_reload_script() {
    if ( ! celeste_is_local() ) return;
    $port = @file_get_contents( get_template_directory() . '/lib/.celeste/port.txt' ) ?? 7354;
    $script = file_get_contents( CELESTE_PLUGIN_PATH . '/assets/html/reload.html' );
    echo str_replace( '{{ port }}', $port, $script );
}

/**
 * Trigger a reload on the local development server when a post is saved
 */
function celeste_trigger_reload_on_save_post( int $post_id, WP_Post $post, bool $update ) {
    if ( ! celeste_is_local() ) return;

    // Check if it's a real post save, not a revision or auto-save
    if ( wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) ) {
        return;
    }

    celeste_trigger_reload( [
        'post_id' => $post_id,
        'type' => 'post'
    ] );
}

/**
 * Trigger a reload on the local development server when ACF
 * options are saved
 */
function celeste_trigger_reload_on_acf_options_save( $post_id ) {
    if ( ! celeste_is_local() ) return;
    celeste_trigger_reload( [
        'post_id' => $post_id,
        'type' => 'acf'
    ] );
}

/**
 * Navigate to the current page on the local development server when
 * the current screen is an edit screen
 */
function celeste_sync_localhost_with_current_screen( WP_Screen $screen ) {
    if ( ! celeste_is_local() ) return;
    if ( $screen->base == 'post' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
        // Verify we are on an edit screen
        if ( isset( $_GET['post'] ) ) {
            $post_id = $_GET['post'];
            // Ensure the $post_id is an integer to avoid any unexpected results
            $post_id = intval($post_id);

            $post_types_to_ignore = [
                'attachment',
                'revision',
                'nav_menu_item',
                'custom_css',
                'customize_changeset',
                'oembed_cache',
                'user_request',
                'wp_block',
                'acf-field-group',
                'acf-field',
                'wpcf7_contact_form',
            ];

            if ( ! in_array( get_post_type($post_id), $post_types_to_ignore ) ) {
                celeste_trigger_reload( [
                    'post_id' => $post_id,
                    'type' => 'post',
                    'action' => 'navigate',
                    'destination' => get_permalink($post_id)
                ] );
            }
        }
    }
}