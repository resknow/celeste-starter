<?php

add_filter( 'celeste.component.SkipLink', function( $context ) {

    [ $props, $rest ] = props( 'SkipLink', $context, include __DIR__ . '/prop-types.php' );
    $context = array_merge( $context, $props );
    $context['attributes'] = attributes($rest);

    return $context;
} );