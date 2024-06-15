<?php

use Celeste\Context;

add_filter( 'allowed_block_types_all', 'celeste_allowed_block_types' );
add_filter( 'block_editor_settings_all', 'celeste_prevent_admin_users_locking_blocks' );

/**
 * Allowed Blocks
 *
 * You can manage the allowed blocks and prefixes from your theme in lib/context.php
 */
function celeste_allowed_block_types($allowed_blocks) {
    $registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
    $registered_block_keys = array_keys( $registered_blocks );
    $is_site_editor = get_current_screen()->is_block_editor && get_current_screen()->base === 'site-editor';

    // Allowed Prefixes
    $allowed_prefixes = Context::get( 'blocks.prefixes' );

    // Only keep allowed prefixes Blocks
    foreach ( $registered_block_keys as $block ) {
        foreach( $allowed_prefixes as $prefix ) {
            if ( strpos( $block, $prefix . '/' ) !== false ) $acf_blocks[] = $block;
        }
    }

    $allowed_blocks = Context::get( 'blocks.allowed' );

    // Set Site Editor allowed blocks
    if ( $is_site_editor ) {
        $allowed_blocks = array_merge( $allowed_blocks, Context::get( 'blocks.allowed_in_site_editor', [] ) );
    }

    return array_merge( $acf_blocks, $allowed_blocks );
}

/**
 * Stop non-admin users from locking or unlocking blocks
 */
function celeste_prevent_admin_users_locking_blocks( $settings ) {
    $settings['canLockBlocks'] = current_user_can( 'create_user' );
    return $settings;
}