<?php

add_filter( 'acf/settings/save_json', 'celeste_set_acf_json_save_path' );
add_filter( 'acf/settings/load_json', 'celeste_set_acf_json_load_path' );
add_filter( 'acf/field/name=icon', 'celeste_show_icons_in_icon_fields' );

/**
 * Get list of icons for icon fields
 *
 * @return array
 */
function celeste_show_icons_in_icon_fields( $field ) {
    // Bail if it's a native select
    if ( $field['ui'] === true ) {
        $field['choices'] = get_list_of_icons();
    }

    return $field;
}

function celeste_set_acf_json_save_path(): string {
    return get_template_directory() . '/lib/acf-json';
}

function celeste_set_acf_json_load_path(): array {
    return [ get_template_directory() . '/lib/acf-json' ];
}