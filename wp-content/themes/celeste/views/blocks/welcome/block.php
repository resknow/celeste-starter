<?php

add_filter( 'celeste.block.welcome', function( $context ) {
    $changelog = sprintf( '%s/changelog.md', CELESTE_PLUGIN_PATH );

    if ( file_exists( $changelog ) ) {
        $parsedown = new Parsedown();
        $markdown = file_get_contents( $changelog );
        $markdown = str_replace( [ '[', ']' ], '**', $markdown );
        $context['changelog'] = $parsedown->text( $markdown );
    }

    return $context;
} );