<?php

add_filter( 'celeste.component.Input', function( $context ) {

    [ $props, $rest ] = props( 'Input', $context, include __DIR__ . '/prop-types.php' );
    $context = array_merge( $context, $props );
    $context['attributes'] = attributes($rest);

    return $context;
} );