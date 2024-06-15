<?php

namespace Celeste\Models;

use Carbon\Carbon;
use WP_Post;

#[\AllowDynamicProperties]
class Post {

    /**
     * Constructor
     *
     * Create a new Post instance and assign properties
     */
    public function __construct( WP_Post $post ) {
        $properties = get_object_vars($post);

        foreach ( $properties as $key => $value ) {
            $this->$key = $value;
        }
    }

    /**
     * Set
     *
     * Backwards Compatibility
     *
     * If a property is accessed with the post_ prefix,
     * we'll return the property without the prefix.
     */
    public function __get( string $name ) {
        if ( str_starts_with( $name, 'post_' ) ) {
            return $this->{ substr( $name, 5 ) };
        }

        return $this->$name;
    }

    /**
     * Set
     *
     * Backwards Compatibility
     *
     * If a property is set with the post_ prefix,
     * we'll set the property without the prefix.
     */
    public function __set( string $name, $value ) {
        if ( str_starts_with( $name, 'post_' ) ) {
            $this->{ substr( $name, 5 ) } = $value;
            return;
        }

        $this->$name = $value;
    }

    /**
     * Get Date
     *
     * Returns a formatted post date, and accepts an optional format
     *
     * @param string $format
     * @return string
     */
    public function date( string $format = null ): string {
        if ( ! property_exists( $this, 'date' ) ) return '';

        $date = new Carbon( $this->date );
        return $format ? $date->format( $format ) : $date->toFormattedDateString();
    }

}