<?php

use Celeste\Assets;
use Celeste\Context;

/**
 * Blocks Allow List
 * @see https://gist.github.com/chrismademe/9eae6465a65b549d43e1d73d14eb88a3
 */
Context::set( 'blocks.allowed', [
    'core/block',
    'core/cover',
    'core/gallery',
    'core/group',
    'core/heading',
    'core/image',
    'core/list-item',
    'core/list',
    'core/media-text',
    'core/missing',
    'core/paragraph',
    'core/post-author',
    'core/post-content',
    'core/post-date',
    'core/post-excerpt',
    'core/post-featured-image',
    'core/post-navigation-link',
    'core/post-template',
    'core/post-terms',
    'core/post-title',
    'core/query-no-results',
    'core/query-pagination-next',
    'core/query-pagination-numbers',
    'core/query-pagination-previous',
    'core/query-pagination',
    'core/query-title',
    'core/query',
    'core/quote',
    'core/social-link',
    'core/social-links',
    'core/spacer'
] );

/**
 * Block Prefix Allow List
 */
Context::set( 'blocks.prefixes', [
    'acf',
    'gravityforms',
    'resknow',
    'celeste'
] );

/**
 * Block Styles
 */
Context::set( 'blocks.styles', [
    [
        'block' => 'resknow/grid-item',
        'name' => 'prose',
        'label' => 'Prose',
    ],
    [
        'block' => 'core/group',
        'name' => 'prose',
        'label' => 'Prose',
    ],
    [
        'block' => 'core/post-content',
        'name' => 'prose',
        'label' => 'Prose',
    ],
    [
        'block' => 'core/gallery',
        'name' => 'square',
        'label' => 'Square Images',
    ],
    [
        'block' => 'core/cover',
        'name' => 'hero',
        'label' => 'Hero',
    ]
] );

/**
 * Block Pattern Categories
 */
Context::set( 'blocks.patterns.categories', [
    'forms' => [ 'label' => 'Forms' ]
] );

/**
 * Utilities
 *
 * Any helper functions that you might need access to in Twig templates
 * Utils are automatically available in Components and Blocks.
 */
Context::set( 'utils', [
    'assets_dir' => Assets::assets_dir(),
    'has_post_thumbnail' => has_post_thumbnail(),
    'year' => date('Y'),
    'user_is_logged_in' => is_user_logged_in(),
    'logout_url' => wp_logout_url( home_url() ),
    'is_front_page' => is_front_page(),
    'privacy_policy_link' => get_privacy_policy_url(),
    'theme_version' => wp_get_theme()->get( 'Version' ),
    'celeste_version' => defined('CELESTE_VERSION') && CELESTE_VERSION,
] );

// Alias 'utils' to 'util' for convenience
Context::alias( 'utils', 'util' );

/**
 * Twig Filters
 */
Context::set( 'twig.filters', [
    'cls'
] );

/**
 * Twig Functions
 *
 * A list of functions that are available in Twig templates
 */
Context::set( 'twig.functions', [
    '__',
    '_n',
    'apply_filters',
    'assets_dir' => ['Celeste\\Assets', 'assets_dir'],
    'block_template_part',
    'body_class',
    'cls',
    'context' => [ 'Celeste\\Context', 'get_instance' ],
    'current_user_can',
    'do_action',
    'dump',
    'esc_url',
    'get_featured_image',
    'get_permalink',
    'get_post_type',
    'get_the_ID',
    'get_the_post_thumbnail_url',
    'get_the_tags',
    'icon_exists' => [ 'Celeste\\Utilities\\Icon', 'exists' ],
    'is_archive',
    'is_front_page',
    'is_page',
    'is_single',
    'is_singular',
    'is_woocommerce',
    'wp_body_open',
    'wp_footer',
    'wp_head',
] );

/**
 * Globals
 * Set in the Globals Option page in the WP Admin
 *
 * Globals are automatically available in Components and Blocks.
 */
add_action( 'init', function() {
    Context::set( 'globals', celeste_has_acf() ? get_fields('options') : [] );
    Context::set( 'globals.menus', get_menus() );
} );