<?php

namespace Celeste\Twilight\Directives;

use Celeste\Twilight\Component;

class Verbatim implements DirectiveInterface {

    public function __construct( private string $key, private string $value ) {}

    public function before( string $twig, Component $component ) : string {

        // Convert markup to Twig syntax
        $markup = '{% verbatim %}';

        // Clean up the prop
        $component->remove_prop( $this->key );

        return $markup;
    }

    public function after( string $twig, Component $component ) : string {
        return '{% endverbatim %}';
    }

}