<?php

namespace Celeste\Utilities;

use Celeste\Assets;
use Exception;

class Icon {

    /**
     * Render
     *
     * Render an icon
     *
     * @param string $name
     * @param string $class
     * @return string SVG Markup
     */
    public static function render($name, $class = null) {

        if ( ! self::exists( $name ) ) {
            celeste_warn( sprintf( 'Icon not found: %s', $name ) );
            return '';
        }

        // Get icon SVG
        $filename = sprintf( 'icons/%s.svg', $name );
        $svg = @file_get_contents( Assets::assets_dir($filename, true) );

        // Classname
        if ( $class ) {
            $class_attr = sprintf( 'class="%s"', $class );
            $svg = str_replace('viewBox', $class_attr . ' viewBox', $svg);
        }

        return $svg;
    }

    /**
     * Icon Exists
     *
     * Check if an icon exists
     *
     * @param string $name
     * @return bool
     */
    public static function exists( string $name ) {
        $filename = sprintf( 'icons/%s.svg', $name );
        return is_readable( Assets::assets_dir($filename, true) );
    }

}