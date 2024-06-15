<?php

namespace Celeste;

class Assets {

    private static $instance;

    /**
     * Get Instance
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Add Style
     *
     * Enqueue a new style
     *
     * @param string $id Unique ID
     * @param string $src Path to the file
     * @param array $deps Optional array of dependencies, whch should enqueue before this
     * @param string $tag Optional version tag, appended to the URL, defaults to last modified time
     * @return void
     */
    public static function add_style( string $id, string $src, array $deps = [], $tag = false ) {
        wp_enqueue_style($id, $src, $deps, $tag);
    }

    /**
     * Add Script
     *
     * Enqueue a new script
     *
     * @param string $id Unique ID
     * @param string $src Path to the file
     * @param array $deps Optional array of dependencies, whch should enqueue before this
     * @param string $tag Optional version tag, appended to the URL, defaults to last modified time
     * @return void
     */
    public static function add_script( string $id, string $src, array $deps = [], $tag = false ) {
        wp_enqueue_script($id, $src, $deps, $tag, true);
    }

    /**
     * Component
     *
     * Enqueue a component's assets
     *
     * @param string $name Component name
     * @param string $type Asset type, script or style
     * @param array $deps Optional array of dependencies, whch should enqueue before this
     * @param string $tag Optional version tag, appended to the URL, defaults to last modified time
     * @return void
     */
    public static function component( string $name, string $type = 'style', array $deps = [], $tag = false ) {
        $filename = $type === 'script' ? 'main.js' : 'style.css';
        $path = sprintf( 'dist/components/%s/%s', $name, $filename );
        self::add( $type, sprintf( 'component-%s', $name ), $path, $deps, $tag );
    }

    /**
     * Block
     *
     * Enqueue a block's assets
     *
     * @param string $name Block name
     * @param string $type Asset type, script or style
     * @param array $deps Optional array of dependencies, whch should enqueue before this
     * @param string $tag Optional version tag, appended to the URL, defaults to last modified time
     * @return void
     */
    public static function block( string $name, string $type = 'style', array $deps = [], $tag = false ) {
        $filename = $type === 'script' ? 'main.js' : 'style.css';
        $path = sprintf( 'dist/blocks/%s/%s', $name, $filename );
        self::add( $type, sprintf( 'block-%s', $name ), $path, $deps, $tag );
    }

    /**
     * Inline
     *
     * Enqueue an inline script or style
     *
     * @param string $type script or style
     * @param string $handle Registered asset to attach this to
     * @param string $content The content of the inline script or style
     * @param string $position Optional, 'before' or 'after' the registered asset
     */
    public static function inline( string $type, string $handle, string $content, string $position = 'after' ) {
        if ( $type === 'style' ) {
            wp_add_inline_style($handle, $content, $position);
        }

        if ( $type === 'script' ) {
            wp_add_inline_script($handle, $content, $position);
        }
    }

    /**
     * Add
     *
     * Enqueue a new asset
     *
     * @param string $type script or style
     * @param string $id Unique ID
     * @param string $src Path to the file
     * @param array $deps Optional array of dependencies, whch should enqueue before this
     * @param string $tag Optional version tag, appended to the URL, defaults to last modified time
     */
    public static function add( string $type, string $id, string $src, array $deps = [], $tag = false ) {

        /**
         * Prepend the template directory URI or Path to the $src if it's not
         * already present or the asset is not a remote URL
         */
        if ( strpos($src, 'http') === false && strpos($src, get_template_directory_uri()) === false ) {
            $src_path = get_template_directory() . '/' . $src;
            $src = get_template_directory_uri() . '/' . $src;
        }

        // If no tag is manually set, just grab the last modified time
        if ( !$tag && isset($src_path) && is_readable($src_path) ) {
            $tag = filemtime( $src_path );
        }

        if ( $type === 'style' ) {
            self::add_style($id, $src, $deps, $tag);
        }

        if ( $type === 'script' ) {
            self::add_script($id, $src, $deps, $tag);
        }
    }

    /**
     * Assets dir
     *
     * Returns the full path to the /assets directory
     *
     * @param string $suffix Optional Path to append to the dist directory path
     * @param bool $full_system_path Full system path for server side use
     */
    public static function assets_dir( $suffix = false, bool $full_system_path = false ) {
        $prefix = $full_system_path ? get_template_directory() : get_template_directory_uri();
        $dir = $prefix . '/dist/assets';

        // Suffix, possible path to a file
        if ($suffix) {
            $dir .= '/' . $suffix;
        }

        return $dir;
    }

}