<?php

/**
 * Celeste Has ACF
 *
 * Checks that ACF Pro is available
 *
 * @return bool
 */
function celeste_has_acf() {
    return class_exists('acf_pro');
}

/**
 * Is Local
 *
 * Check if the current environment is local and the URL ends with .local
 */
function celeste_is_local() {
    return wp_get_environment_type() === 'local' && str_ends_with( site_url(), '.local' );
}

/**
 * Is Boolean Like
 *
 * Check if a value is boolean-like
 */
function celeste_is_boolean_like( $value ) {
    return is_bool( $value ) || $value === 1 || $value === 0 || $value === '1' || $value === '0';
}