<?php

use Celeste\Context;
use Celeste\Utilities\FindComponents;
use Celeste\Twilight\Twig;
use Twig\TwigFunction;

/**
 * Compile a Twig template.
 *
 * Compiles a Twig template via Twilight.
 *
 * @param string $template  The template to render.
 * @param array $context    The context to pass to the template.
 * @param string $type      The type of template to render.
 */
function compile( string $template, array $context, string $type = 'file' ): string {
    $twig = new Twig( get_template_directory() . '/views' );

    $find_components = new FindComponents;
    $components = $find_components->find( $template );

    /**
     * Fire an action for each component on this page that
     * can be used to
     */
    if ( is_array( $components ) && ! empty( $components ) ) {
        $context = apply_filters( 'twilight.components.present', $context, $template, $components );

        foreach ( $components as $name ) {
            $context = apply_filters( 'twilight.component.' . $name . '.present', $context, $template );
        }
    }

    return $twig->render( $template, $context, $type );
}

/**
 * Render a Twig template.
 *
 * Renders a Twig template via Twilight.
 *
 * @param string $template  The template to render.
 * @param array $context    The context to pass to the template.
 * @param string $type      The type of template to render.
 */
function render( string $template, array $context, string $type = 'file' ): void {
    echo compile( $template, $context, $type );
}

/**
 * Component
 *
 * Render a component in isolation
 *
 * @param string $name
 * @param array $context
 * @return string Rendered Component Markup
 */
function component( string $name, array $context ): string {
    $twig = new Twig( get_template_directory() . '/views' );
    return $twig->render_component( $name, $context );
}

/**
 * Props
 *
 * Takes in an array of Component prop names and an array of props and
 * returns a tuple, one with array with the defined props and one
 * with all the rest.
 *
 * @param string $component Component Name
 * @param array $props All Props (the $context array)
 * @param array $defined Defined Props
 */
function props( string $component, array $props, array $defined ): array {
    $defined_props = [];
    $rest = [];

    foreach ( $props as $key => $value ) {
        if ( array_key_exists( $key, $defined ) ) {
            $defined_props[ $key ] = $value;
        } else {

            // Skip globals, utils and children
            if ( $key === 'globals' || $key === 'utils' || $key === 'children' || $key === 'slots' ) {
                continue;
            }

            $rest[ $key ] = $value;
        }
    }

    // Validate Props
    $defined_props = validate_props( $component, $defined_props, $defined );

    return [ $defined_props, $rest ];
}

/**
 * Validate Props
 *
 * Validates defined props fpr the correct type,
 * whether it's required and sets a default value
 * if provided.
 *
 * @param string $component
 * @param array $props
 * @param array $defined
 */
function validate_props( string $component, array $props, array $defined ): array {

    // Warn about reserved `use` prop
    if ( array_key_exists( 'use', $defined ) ) {
        celeste_warn( sprintf( 'Prop <code>`use`</code> is defined as a prop type in <strong>%s</strong> but is a reserved name.', $component ) );
    }

    foreach ( $defined as $key => $value ) {
        if ( ! array_key_exists( $key, $props ) ) {
            if ( array_key_exists( 'default', $value ) ) {
                $props[ $key ] = $value['default'];
            } else {
                if ( array_key_exists( 'required', $value ) && $value['required'] === true ) {
                    celeste_warn( sprintf( 'Prop `%s` is required in **%s**.', $key, $component ) );
                }
            }
        }

        /**
         * Validate the prop using a custom validator
         */
        if (
            array_key_exists( 'validator', $value )
            && is_callable( $value['validator'] )
            && array_key_exists( $key, $props )
        ) {
            $result = call_user_func( $value['validator'], $props[ $key ], $key );
            if ( $result === false ) {
                $warning = sprintf(
                    'Prop `%s` failed validation in **%s**.',
                    $key,
                    $component
                );
                celeste_warn( $warning );
            }
        }

        if ( array_key_exists( 'type', $value ) && array_key_exists( $key, $props ) ) {
            if ( validate_prop_type( $props[ $key ], $value ) === false ) {

                if ( $value['type'] === 'enum' ) {
                    $warning = sprintf(
                        'Prop `%s` must be one of the following values: `%s` in **%s**.',
                        $key,
                        implode( ', ', $value['values'] ),
                        $component
                    );
                    celeste_warn( $warning );
                    continue;
                }

                if ( $value['type'] === 'instanceof' ) {
                    $warning = sprintf(
                        'Prop `%s` must be an instance of `%s` in **%s**.',
                        $key,
                        $value['instanceof'],
                        $component
                    );
                    celeste_warn( $warning );
                    continue;
                }

                $warning = sprintf(
                    'Prop `%s` is not of type `%s` in **%s**.',
                    $key,
                    $value['type'],
                    $component
                );
                celeste_warn( $warning );
            }
        }
    }

    return $props;
}

/**
 * Validate Prop Type
 *
 * Validates a prop type.
 *
 * @param mixed $prop
 * @param string $type
 */
function validate_prop_type( $value, array $type ): bool {

    if ( $type['type'] === 'string' && is_string( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'number' && is_numeric( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'array' && is_array( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'enum' && in_array( $value, $type['values'] ) ) {
        return true;
    }

    if ( $type['type'] === 'bool' && celeste_is_boolean_like( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'boolean' && celeste_is_boolean_like( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'int' && is_int( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'float' && is_float( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'object' && is_object( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'callable' && is_callable( $value ) ) {
        return true;
    }

    if ( $type['type'] === 'instanceof' && $value instanceof $type['instanceof'] ) {
        return true;
    }

    return false;

}

/**
 * Attributes
 *
 * Convert an array of attributes to a string.
 *
 * @param array $attributes
 * @return string
 */
function attributes( array $attributes ): string {
    $output = '';

    if ( ! empty( $attributes ) ) {
        foreach ( $attributes as $key => $value ) {
            $output .= sprintf( ' %s="%s"', $key, $value );
        }
    }

    return $output;
}

/**
 * Include Component PHP
 */
function celeste_include_component_php() {
    $should_include_component_php = apply_filters( 'celeste.include_component_php', true );
    if ( ! $should_include_component_php ) return;

    $component_files = glob( get_template_directory() . '/views/components/**/component.php' );
    foreach ( $component_files as $file ) {
        require_once $file;
    }
}