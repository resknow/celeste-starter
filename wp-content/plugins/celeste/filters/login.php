<?php

add_filter( 'login_headerurl', 'celeste_login_set_header_url' );

function celeste_login_set_header_url( string $url ): string {
    return home_url();
}