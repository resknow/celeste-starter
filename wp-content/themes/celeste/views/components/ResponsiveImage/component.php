<?php

add_filter( 'celeste.component.ResponsiveImage', function( $context ) {

    // If we have an ID, we need the image data
    if ( is_numeric( $context['image'] ) ) {
        $context['image'] = format_image_array( $context['image'] );
    }

    $available_sizes = get_intermediate_image_sizes();
    $max_width = ($context['maxWidth'] ?? 99999) * 2;
    $sizes = [];

    // Take the smallest as the default
    $default = array_shift( $available_sizes );
    $context['default'] = $context['image']['sizes'][ $default ];

    foreach ( $available_sizes as $size ) {
        if ( $context['image']['sizes'][ $size . '-width' ] < $max_width ) {
            $sizes[ $size ] = sprintf(
                '%s %sw',
                $context['image']['sizes'][ $size ],
                $context['image']['sizes'][ $size . '-width' ] / 2
            );
        }
    }

    // Get the largest size and grab the width and height
    $largest = array_key_last( $sizes );
    $context['width'] = $context['image']['sizes'][ $largest . '-width' ];
    $context['height'] = $context['image']['sizes'][ $largest . '-height' ];

    // Store the sizes we want to use
    $context['sizes'] = $sizes;
    $context['srcset'] = implode( ', ', $sizes );

    return $context;
} );