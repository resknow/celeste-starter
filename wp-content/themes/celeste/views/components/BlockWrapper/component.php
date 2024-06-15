<?php

add_filter( 'celeste.component.BlockWrapper', function( $context ) {

    [ $props, $rest ] = props( 'BlockWrapper', $context, include __DIR__ . '/prop-types.php' );
    $context = array_merge( $context, $props );

	return $context;
} );