<?php

add_action( 'admin_head', 'celeste_cleanup_appearance_menu' );
add_action( 'init', 'celeste_allow_editor_to_access_appearance_menu' );

/**
 * Allow Editor role to Access Appearance Menu
 */
function celeste_allow_editor_to_access_appearance_menu() {
    $editor_role = get_role( 'editor' );
    $editor_role->add_cap( 'edit_theme_options' );
}

/**
 * Cleanup Appearance Menu for Editor role
 *
 * - Hide Theme Selection Page
 * - Hide Widgets Page
 */
function celeste_cleanup_appearance_menu() {

    // Bail if admin user
    if ( current_user_can( 'activate_plugins' ) ) return;

    // Hide theme selection page
    remove_submenu_page( 'themes.php', 'themes.php' );

    // Hide widgets page
    remove_submenu_page( 'themes.php', 'widgets.php' );

    // Hide customize page
    global $submenu;
    unset( $submenu['themes.php'][6] );

}