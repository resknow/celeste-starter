<?php

add_action( 'init', 'celeste_set_update_preferences' );

function celeste_set_update_preferences() {
    /**
     * Disable Nightly (possibly unstable) builds from being included with auto updates
     */
    add_filter( 'allow_dev_auto_core_updates', '__return_false' );

    /**
     * Enable automatic updates for Plugins
     */
    add_filter( 'auto_update_plugin', '__return_true' );
}