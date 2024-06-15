<?php

use Celeste\Query\Post;

/**
 * Debug Timer Start
 *
 * @param string $id
 */
function debug_timer_start( string $id ): void {
    $full_id = '__celeste__debug_timer_' . $id;
    $GLOBALS[ $full_id ] = microtime(true);
}

/**
 * Debug Timer End
 *
 * @param string $id
 */
function debug_timer_end( string $id ): void {
    $full_id = '__celeste__debug_timer_' . $id;

    // Make sure the timer exists
    if ( !isset( $GLOBALS[ $full_id ] ) ) {
        return;
    }

    $start = $GLOBALS[ $full_id ];
    $end = microtime(true);
    $time = $end - $start;

    if ( wp_get_environment_type() !== 'production' ) {
        printf('<script>console.log(`Timer: %s - %s seconds`);</script>', $id, $time);
    } else {
        error_log( sprintf('Debug Timer: %s - %s seconds', $id, $time) );
    }
}

/**
 * Dump
 *
 * Pretty print data
 *
 * @param mixed $input
 */
function dump( $input ): void {
    printf( '<pre>%s</pre>', print_r($input, true) );
}

/**
 * Dump and Die
 *
 * Pretty print data and stop the script from running further.
 *
 * @param mixed $input
 */
function dd( $input ): void {
    dump($input); exit;
}

/**
 * Format Image Array
 *
 * Takes an attachment ID for an image and returns an array formatted in the
 * same way ACF does.
 *
 * @param int $attachment_id
 * @return array|false
 */
function format_image_array( int $attachment_id ) {
    // Get the image array
    $full_image = wp_get_attachment_image_src($attachment_id, 'full');
    if ( !$full_image ) return false;

    // Add it to the post object in the same format as ACF
    $formatted_image['url'] = $full_image[0];

    // Get the ALT text
    $formatted_image['alt'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

    // Get image sizes
    $image_sizes = get_intermediate_image_sizes();

    // Get the image for each size
    foreach ($image_sizes as $size) {

        // Get the image array
        $image = wp_get_attachment_image_src($attachment_id, $size);

        // Add it to the post object in the same format as ACF
        $formatted_image['sizes'][$size] = $image[0];
        $formatted_image['sizes'][$size . '-width'] = $image[1];
        $formatted_image['sizes'][$size . '-height'] = $image[2];

    }

    return $formatted_image;
}

/**
 * Get Featured Image
 *
 * Takes a WP_Post object and adds an array with the featured image in all
 * available sizes
 *
 * @param int $post_id
 * @return array|false
 */
function get_featured_image( int $post_id ) {

    // Bail if we don't get a valid $post_id
    if ( is_null($post_id) ) return false;

    // Create Array
    $featured_image = [];

    // Get image attachment ID
    $thumbnail_id = get_post_thumbnail_id($post_id);

    // Defaults to a placeholder for products
    if ( get_post_type($post_id) === 'product' ) {
        $thumbnail_id = $thumbnail_id > 0
            ? $thumbnail_id
            : get_option( 'woocommerce_placeholder_image' );
    }

    // Add it to the post object in the same format as ACF
    $featured_image = format_image_array($thumbnail_id);

    // Done!
    return $featured_image;
}

/**
 * Get Full Post
 *
 * Gets post object and all fields, featured image etc.
 *
 * @param WP_Post|int $id Post Object or Post ID
 * @return WP_Post|null WP_Post|null
 */
function get_full_post( $id = null ) {
    global $post;

    // If no post, get the current post
    if ( is_null( $id ) ) {
        $post_id = $post->ID;

    // Get post by ID
    } elseif ( $id instanceof WP_Post ) {
        $post_id = $id->ID;
    } else {
        $post_id = $id;
    }

    return Post::id($post_id)
        ->with_excerpt()
        ->with_featured_image()
        ->get();
}

/**
 * Get Post with Fields
 *
 * Returns a post with it's ACF fields attached to it
 *
 * NOTE We don't use $post as a variable inside here so we don't mess with the
 * WordPress global variable
 *
 * @param int $id Post ID
 * @return WP_Post|bool
 * @deprecated Use Celeste\Query\Post::id()->get() instead
 */
function get_post_with_fields( int $id ) {
    return Post::id($id)
        ->with_fields()
        ->with_excerpt()
        ->with_featured_image()
        ->get();
}

/**
 * Get Posts with Fields
 *
 * Returns an array of posts with their ACF fields attached
 *
 * NOTE We don't use $post as a variable inside here so we don't mess with the
 * WordPress global variable
 *
 * @param array $args get_posts() args
 * @return array|bool
 * @deprecated Use Celeste\Query\Post::query() instead
 */
function get_posts_with_fields( array $args = [] ) {

    // Get Posts
    if ( ! $the_posts = get_posts(array_merge(['fields' => 'ids', $args])) ) {
        return false;
    }

    // Add fields
    foreach ( $the_posts as $key => $p ) {
        $id = $p instanceof WP_Post ? $p->ID : $p;

        $p = Post::id($id)
            ->with_fields()
            ->with_excerpt()
            ->with_featured_image()
            ->get();

        $the_posts[ $key ] = $p;
    }

    // Done!
    return $the_posts;
}

/**
 * Get Product Images
 *
 * Returns an array of product images. For the main image, use get_featured_image.
 *
 * @param int $post_id
 * @return array|void
 */
function get_product_images( int $post_id ) {

    // Check post is a product
    if ( get_post_type($post_id) !== 'product' || ! function_exists('wc_get_product') ) return;

    // Get WooCommerce Product Object
    $product = wc_get_product($post_id);

    return array_map( function ($image_id) {
        return format_image_array($image_id);
    }, $product->get_gallery_image_ids() );

}

/**
 * Get Category Image
 *
 * Returns a WooCommerce Category image
 *
 * @param int $term_id
 * @return array
 */
function get_category_image( int $term_id ): array {
    $thumb_id = get_term_meta($term_id, 'thumbnail_id', true);
    return format_image_array($thumb_id);
}

/**
 * Create Post Excerpt
 *
 * Creates a post excerpt with an ellipsis if needed.
 *
 * @param string $content
 * @param int $length
 * @return string
 */
function create_post_excerpt( string $content, int $length = 150 ): string {
    $content = strip_tags($content);
    $excerpt = substr($content, 0, $length);
    $ellipsis = (strlen($excerpt) > ($length - 3) ? '...' : '');
    return $excerpt . $ellipsis;
}

/**
 * Get Menu Children
 *
 * Returns an array of menu items that are children of the given menu item.
 *
 * @param array $menu_items
 * @param WP_Post $menu_item
 * @return array
 */
function get_menu_children( array $menu_items, WP_Post $menu_item ): array {
    $children = [];

    if ( ! empty($menu_items) ) {
        foreach ( $menu_items as $key => $item ) {
            if ( $item->menu_item_parent == $menu_item->ID ) {
                $children[$item->ID] = [];
                $children[$item->ID]['ID'] = $item->ID;
                $children[$item->ID]['title'] = $item->title;
                $children[$item->ID]['url'] = $item->url;
                $children[$item->ID]['post_ID'] = $item->object_id;
                $children[$item->ID]['class'] = is_array($item->classes) ? join(' ', $item->classes) : null;

                if ( function_exists('get_fields') ) {
                    $children[$item->ID]['fields'] = get_fields($item->ID);
                }

                unset($menu_items[$key]);
                $children[$item->ID]['children'] = get_menu_children($menu_items, $item);
            }
        }
    }

    return $children;
}

/**
 * Get Menu
 *
 * @param string $name Menu name
 * @return array Menu array
 * @return WP_Nav_Menu_Item[]|false
 */
function get_menu( string $name ) {

    // Get Menu
    if ( !$menu_items = wp_get_nav_menu_items($name) ) {
        return false;
    }

    $menu = [];

    foreach ( $menu_items as $item ) {
        if ( empty($item->menu_item_parent) ) {
            $menu[$item->ID] = [];
            $menu[$item->ID]['ID'] = $item->ID;
            $menu[$item->ID]['title'] = $item->title;
            $menu[$item->ID]['url'] = $item->url;
            $menu[$item->ID]['post_ID'] = $item->object_id;
            $menu[$item->ID]['class'] = is_array($item->classes) ? join(' ', $item->classes) : null;
            $menu[$item->ID]['children'] = get_menu_children($menu_items, $item);

            if ( function_exists('get_fields') ) {
                $menu[$item->ID]['fields'] = get_fields($item->ID);
            }

            $menu[$item->ID] = apply_filters( 'celeste.menu.item', $menu[$item->ID], $name );
        }
    }

    $menu = apply_filters( 'celeste.menu', $menu, $name );

    return $menu;
}

/**
 * Get Menus
 *
 * @return array
 */
function get_menus() {
    $menus = get_terms( 'nav_menu' );
    $menu_names = wp_list_pluck( $menus, 'name' );
    $the_menus = [];

    if ( !empty ($menus) ) {
        foreach ( $menu_names as $menu_name ) {
            $menu_key = sanitize_title($menu_name);
            $the_menus[$menu_key] = get_menu($menu_name);
        }
    }

    return $the_menus;
}

/**
 * Classnames
 *
 * Conditionally returns a string of classnames based on the given array.
 * Works very similarly to the classnames npm package.
 *
 * @param array $classnames
 * @return string
 */
function cls( array $classes ): string {
    $classes_to_render = [];

    foreach ( $classes as $class => $condition ) {
        if ( ! is_int($class) && $condition !== false && $condition !== null ) {
            $classes_to_render[] = $class;
            continue;
        }

        /**
         * If the condition is a string, then it's actually just a class without
         * a condition, so include it
         */
        if ( is_string($condition) ) {
            $classes_to_render[] = $condition;
        }
    }

    return implode( ' ', $classes_to_render );
}

/**
 * Has Block but it actually works
 *
 * @param string Block name
 * @param int Post ID
 *
 * @return bool
 */
function contains_block( string $block_name, int $post_id = 0 ): bool {
    $post_id = $post_id > 0 ? $post_id : get_the_ID();
    $the_post = get_post($post_id);

    if ( !$the_post ) {
        return false;
    }

    $post_content = $the_post->post_content;
    return has_block($block_name, $post_content);
}

/**
 * Celeste Warn
 *
 * Adds a warning to the warnings array if the site is running locally.
 *
 * @param string $warning
 */
function celeste_warn( string $warning ): void {
    if ( str_ends_with( site_url(), '.local' ) ) {
        $parsedown = new Parsedown;
        $warnings = Celeste\Context::get( 'celeste.warnings', [] );
        $warnings[] = $parsedown->text($warning);
        Celeste\Context::set( 'celeste.warnings', $warnings );
    } else {
        error_log( $warning );
    }
}

/**
 * Get Icons from directory as key => value pairs
 * Compatible with ACF $field['choices]
 *
 * @param string $dir Directory to look in
 * @param string $suffix File extension/suffix
 * @param callable $filter Optional function to filter the returned values
 * @param array $default Default values
 * @return array
 */
function get_files_from_directory( string $dir, string $suffix, callable $filter, array $default = [] ): array {

    $items = $default;
    $available_files = glob( sprintf( '%s*%s', $dir, $suffix ) );

    if ( !empty( $available_files ) ) {
        foreach ( $available_files as $file ) {
            $filename = str_replace( $dir, '', $file );
            $key = str_replace( $suffix, '', $filename );
            $file = @file_get_contents( $file );

            if ( is_callable( $filter ) ) {
                $file = call_user_func( $filter, $file, $key );
            }

            $items[$key] = $file;
        }
    }

    return $items;

}

/**
 * Get List of Icons
 *
 * @return array key => value array of icons
 */
function get_list_of_icons(): array {
    $dir = get_template_directory() . '/dist/assets/icons/';
    $suffix = '.svg';
    return get_files_from_directory( $dir, $suffix, function($file, $name) {
        $file = str_replace( '<svg', '<svg style="width: 1.2em; height: 1.2em;"', $file );
        return sprintf( '<div style="display: flex; align-items: center; gap: 4px">%s <span>%s</span></div>', $file, $name );
    }, [
        'none' => 'No icon'
    ] );
}