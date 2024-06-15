<?php

use Celeste\Utilities\Icon;

add_filter( 'celeste.component.Icon', function( $context ) {

    [ $props, $rest ] = props( 'Icon', $context, include __DIR__ . '/prop-types.php' );
    $context = array_merge( $context, $props );
    $attributes = attributes($rest);

    $context['svg'] = Icon::render( $context['name'] );
    $context['svg'] = str_replace( '<svg ', sprintf( '<svg %s', $attributes ), $context['svg'] );

    return $context;
} );