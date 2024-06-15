<?php

add_action( 'login_head', 'celeste_enqueue_login_style' );

function celeste_enqueue_login_style() {
    $icon = apply_filters( 'celeste.icon', CELESTE_PLUGIN_URL . '/assets/images/rainbow-wp.png' );
    printf(
        '<style>:root { --icon-url: url("%s") }</style>',
        $icon
    );
    printf( '<link rel="stylesheet" href="%s">', CELESTE_PLUGIN_URL . '/assets/css/admin-login.css' );
}
