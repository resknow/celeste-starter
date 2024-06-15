<?php

use Celeste\BlockEditor\Blocks;

add_action( 'acf/init', 'celeste_register_blocks' );
add_action( 'acf/init', 'celeste_register_globals_options_page' );

function celeste_register_blocks() {
    Blocks::register_all();
}

function celeste_register_globals_options_page() {
    acf_add_options_page( [
        'page_title' => 'Globals',
        'menu_title' => 'Globals',
        'menu_slug' => 'globals',
        'position' => 40,
        'capability' => 'edit_pages',
        'icon_url'=> 'dashicons-admin-site-alt'
    ] );
}