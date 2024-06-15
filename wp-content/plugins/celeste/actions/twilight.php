<?php

use Celeste\Context;
use Twig\TwigFunction;
use Twig\TwigFilter;

add_action( 'twilight.twig', 'celeste_register_twig_filters' );
add_action( 'twilight.twig', 'celeste_register_twig_functions' );

/**
 * Register Twig Functions
 *
 * @param \Twig\Environment $twig The Twig Environment instance
 */
function celeste_register_twig_functions( \Twig\Environment $twig ) {
    $functions = Context::get( 'twig.functions' );

    foreach ( $functions as $key => $function ) {
        $name = is_numeric( $key ) ? $function : $key;
        $twig->addFunction( new TwigFunction( $name, $function, [ 'is_safe' => [ 'html' ] ] ) );
    }
}

/**
 * Register Twig Filters
 *
 * @param \Twig\Environment $twig The Twig Environment instance
 */
function celeste_register_twig_filters( \Twig\Environment $twig ) {
    $filters = Context::get( 'twig.filters' );

    foreach ( $filters as $key => $filter ) {
        $name = is_numeric( $key ) ? $filter : $key;
        $twig->addFilter( new TwigFilter( $name, $filter ) );
    }
}