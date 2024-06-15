<?php

/**
 * TODO *
 * - Document block.json
 * - Document block.php
 * - Document template.twig
 */

namespace Celeste\BlockEditor;

use Celeste\Context;
use WP_Block_Type_Registry;

class Blocks {

    /**
     * Register All
     *
     * Scans the Blocks Directory in this theme for a block.json file
     * and will auto register the block
     */
    public static function register_all() {
        $block_dir = get_template_directory() . '/views/blocks';
        $block_definitions = glob( $block_dir . '/**/block.json' );
        $block_logic = glob( $block_dir . '/**/block.php' );
        if ( empty( $block_definitions ) ) return;

        // Include Block Specific Logic
        if ( !empty( $block_logic ) ) {
            foreach ( $block_logic as $file ) {
                require_once $file;
            }
        }

        foreach ( $block_definitions as $file ) {
            self::register($file);
        }
    }

    /**
     * Register a block with ACF & the Block Editor
     */
    public static function register($file) {
        /**
         * Load the block.json file
         */
        $block = json_decode( file_get_contents($file), true );

        /**
         * Register with WordPress
         */
        register_block_type($file);

        /**
         * Normalise block name to lowercase
         * Non-lowercase names cause ACF not to render field inputs in the editor
         */
        if ( ctype_upper( substr( $block['name'], 0, 1) ) ) {
            $block['name'] = strtolower($block['name']);
            trigger_error( sprintf('block/acf/%s/block.json: Block names must be lowercase', $block['name']), E_USER_NOTICE );
        }

        /**
         * Get the field definitions from the block.json file and register
         * them with ACF.
         */
        self::register_custom_fields($block);

        /**
         * If this block replaces core blocks, unregister them
         */
        if ( array_key_exists( 'replaces', $block ) ) {
            self::unregister_core_blocks($block['replaces']);
        }
    }

    /**
     * Register Custom Fields
     *
     * @param array $block Block data
     */
    public static function register_custom_fields( array $block ) {
        if ( ! array_key_exists('fields', $block) || empty($block['fields']) ) return;

        // Setup Fields
        $fields = array_map( function($field) use ($block) {
            $field['key'] = $field['key'] ?? sprintf( 'field_%s-%s', $block['name'], $field['name'] );
            return $field;
        }, $block['fields'] );

        /**
         * Register the fields with ACF
         */
        acf_add_local_field_group([
            'key' => sprintf( 'group_%s', $block['name'] ),
            'title' => sprintf( 'Block: %s', $block['title'] ),
            'fields' => $fields,
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => sprintf( 'acf/%s', $block['name'] )
                    ]
                ]
            ]
        ]);
    }

    /**
     * Render
     * This is the callback that displays the block.
     *
     * @param   array  $block      The block settings and attributes.
     * @param   string $content    The block content (empty string).
     * @param   bool   $is_preview True during editor preview.
     */
    public static function render( array $block, string $content = '', bool $is_preview = false, int $post_id = 0 ) {
        echo self::compile($block, $content, $is_preview, $post_id);
    }

    /**
     * Compile
     *
     * @param array $block
     * @param string $content
     * @param bool $is_preview
     * @param int $post_id
     */
    public static function compile( array $block, string $content = '', bool $is_preview = false, int $post_id = 0 ) {
        $context = [];
        $context['block'] = $block;
        $context['post_id'] = $post_id;
        $context['fields'] = get_fields();
        $context['util'] = Context::get('utils');
        $context['globals'] = Context::get('globals');
        $context['is_preview'] = $is_preview;
        $context['attributes'] = $is_preview ? null : get_block_wrapper_attributes();

        $name = str_replace( 'acf/', '', $block['name'] );
        $template = sprintf( 'blocks/%s/template.twig', $name );

        /**
         * Apply filters to the context
         *
         * @deprecated The theme prefix will be removed in the future
         */
        $context = apply_filters( 'theme.block', $context ); // Applies to every block
        $context = apply_filters( sprintf( 'theme.block.%s', $name ), $context ); // Block specific

        /**
         * Apply filters to the context
         */
        $context = apply_filters( 'celeste.block', $context ); // Applies to every block
        $context = apply_filters( sprintf( 'celeste.block.%s', $name ), $context ); // Block specific

        return compile( $template, $context );
    }

    /**
     * Unregister Core Blocks
     *
     * @param string|array $blocks Core blocks to unregister
     */
    public static function unregister_core_blocks( $blocks ) {

        // Convert to an array if we get a single string
        if ( is_string($blocks) ) {
            $blocks = [$blocks];
        }

        foreach ( $blocks as $block ) {
            unregister_block_type( $block );
        }

    }

}