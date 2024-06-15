<?php

/**
 * Trigger Reload
 *
 * Trigger a reload on the local development server.
 *
 * @param array $data
 */
function celeste_trigger_reload( array $data ) {

    $port = @file_get_contents( get_template_directory() . '/lib/.celeste/port.txt' ) ?? 7354;

    if ( wp_get_environment_type() !== 'local' ) return;

    wp_remote_post( sprintf( 'http://localhost:%s/trigger-reload', $port ), [
        'body' => $data,
        'timeout' => 5, // Timeout in seconds
        'redirection' => 5,
        'blocking' => false, // Set to false to make the request non-blocking
        'httpversion' => '1.0',
        'sslverify' => false,
    ] );

}