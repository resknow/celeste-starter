<?php

/**
 * This block required ACF to be installed, so we check for it
 */
if ( ! function_exists( 'get_field' ) ) return;

/**
 * Register the Block
 */
register_block_type( CELESTE_BLOCKS_DIR . '/build/custom-field', [
    /**
     * Renders the `resknow/custom-field` block on the server.
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block default content.
     * @param WP_Block $block      Block instance.
     *
     * @return string Returns the ACF custom field value
     */
    'render_callback' => function( $attributes, $content, $block ) {

        if ( ! isset( $block->context['postId'] ) ) return '';
        if ( ! isset( $attributes['fieldName'] ) ) return '';

        // Check for specific post ID
        $post_id = $block->context['postId'];
        if ( isset( $attributes['postId'] ) ) {
            $post_id = $attributes['postId'];
        }

        $value = get_field( $attributes['fieldName'], $post_id );

        if ( ! $value ) return '';

        // Create wrapper attributes, this is the same as blockProps in React
        $wrapper_attributes = get_block_wrapper_attributes( [
            'class' => 'custom-field',
            'data-field' => $attributes['fieldName'],
            'data-post-id' => $attributes['postId']
        ] );

        return sprintf(
            '<p %1$s>%2$s</p>',
            $wrapper_attributes,
            $value
        );
    }
] );

/**
 * Create an endpoint that returns all registered menus
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'celeste/v1', '/blocks/custom-field', [
        'methods' => 'POST',
        'callback' => function( $request ) {
            $data = $request->get_params();
            $field = get_field( $data['fieldName'], $data['postId'] );
            return [
                'fieldName' => $data['fieldName'],
                'postId' => $data['postId'],
                'fieldValue' => $field ?? null
            ];
        }
    ] );
} );