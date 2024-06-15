<?php

/**
 * Remove a directory and all of its contents
 */
function celeste_cli_remove_dir( string $src ) {
    if ( ! is_dir($src) ) return;

    $dir = opendir($src);
    while( false !== ( $file = readdir($dir) ) ) {
        if ( ( $file != '.' ) && ( $file != '..' ) ) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                celeste_cli_remove_dir($full);
            }
            else {
                unlink($full);
            }
        }
    }

    closedir($dir);
    rmdir($src);
}

/**
 * Deep Merge two arrays
 */
function celeste_cli_array_deep_merge(array ...$arrays): array {
    $base = array_shift($arrays);

    foreach ( $arrays as $array ) {
        foreach ( $array as $key => $value ) {
            if ( is_array($value) && isset($base[ $key ]) && is_array($base[ $key ]) ) {
                $base[ $key ] = celeste_cli_array_deep_merge($base[ $key ], $value);
            } else {
                $base[ $key ] = $value;
            }
        }
    }

    return $base;
}