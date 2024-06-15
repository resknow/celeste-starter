<?php

use Celeste\Assets;

add_action( 'wp_enqueue_scripts', function() {
    Assets::component( 'Error', 'style' );
} );

add_filter( 'celeste.component.Error/TraceLine', function( $context ) {

    if ( isset( $context['root'] ) ) {
        $context['trace']['path'] = str_replace( $context['root'], '', $context['trace']['file'] );
    }

    return $context;
} );