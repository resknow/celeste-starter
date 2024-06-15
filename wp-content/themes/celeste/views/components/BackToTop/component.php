<?php

add_filter( 'celeste.component.BackToTop', function( $context ) {

    [ $props, $rest ] = props( 'BackToTop', $context, include __DIR__ . '/prop-types.php' );
    $context = array_merge( $context, $props );
    $context['attributes'] = attributes($rest);

    return $context;
} );