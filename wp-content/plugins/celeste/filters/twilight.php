<?php

use Celeste\Context;

add_filter( 'twilight.component', 'celeste_add_context_to_components' );
add_filter( 'twilight.twig.environment', 'celeste_set_twig_cache_dir' );

/**
 * Add additional context to components
 */
function celeste_add_context_to_components( array $context ): array {
    $context['globals'] = Context::get('globals');
    $context['utils'] = Context::get('utils');

    return $context;
}

/**
 * For production environments, enable Twig cache
 */
function celeste_set_twig_cache_dir( array $options ): array {

    $options['cache'] = wp_get_environment_type() === 'production'
        ? WP_CONTENT_DIR . '/.twig-cache'
        : false;

    return $options;
}