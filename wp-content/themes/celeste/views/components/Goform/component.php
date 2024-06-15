<?php

use Celeste\Assets;

add_filter( 'celeste.component.Goform.present', function( $context ) {
    Assets::add( 'script', 'goform', 'https://goform.app/wc.js' );
    return $context;
} );

add_filter( 'celeste.component.Goform', function( $context ) {
    [ $props, $rest ] = props( 'Goform', $context, include __DIR__ . '/prop-types.php' );
    $context = array_merge( $context, $props );
    $context['attributes'] = attributes($rest);

    return $context;
} );