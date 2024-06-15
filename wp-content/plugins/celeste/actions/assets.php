<?php

add_action( 'wp_enqueue_scripts', 'celeste_remove_jquery' );

function celeste_remove_jquery() {
    if ( ! class_exists( 'Woocommerce' ) && ! class_exists( 'QM_PHP' ) ) {
        wp_deregister_script( 'jquery' );
    }
}