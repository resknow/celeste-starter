<?php

// Do something before the component is rendered, like manipulating the context
add_filter( 'celeste.component.Flow', function( $context ) {
    [ $props, $rest ] = props( 'Flow', $context, [
        'space' => [
            'type' => 'enum',
            'values' => [ 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl' ],
        ]
    ] );
    $context = array_merge( $context, $props );
    $context['attributes'] = attributes($rest);

    $sizes = [
        'xs' => 'flow-space-xs',
        'sm' => 'flow-space-sm',
        'md' => 'flow-space-md',
        'lg' => 'flow-space-lg',
        'xl' => 'flow-space-xl',
        '2xl' => 'flow-space-2xl',
        '3xl' => 'flow-space-3xl',
    ];

    $context['spaceClass'] = isset( $context['space'] )
        ? $sizes[ $context['space'] ]
        : '';

	return $context;
} );