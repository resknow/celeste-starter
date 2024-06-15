<?php

use Celeste\Query\Post;

add_filter( 'celeste.component.PostCard', function( $context ) {

    if ( version_compare( $context['utils']['theme_version'], '1.1.0', '>=' ) ) {
        props( 'PostCard', $context, [
            'id' => [ 'type' => 'number' ]
        ] );
    }

    if ( get_post_status( $context['id'] ) === false ) {
        throw new Exception( sprintf( 'Post with ID <code>`%s`</code> does not exist.', $context['id'] ) );
        return $context;
    }

    $context['post'] = Post::id($context['id'])
        ->with_featured_image()
        ->get();

    return $context;
} );