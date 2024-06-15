<?php

namespace Celeste;

class Context {

	/**
	 * Singleton Instance
	 */
	private static $instance;

	/**
	 * Context array
	 */
	private $context = [];

	/**
	 * Aliases
	 */
	private $aliases = [];

	/**
	 * Get a value from the context array
	 * If not key is set, return the entire context array
	 *
	 * @param string $key
	 * @param mixed $fallback
	 * @return mixed|boolean
	 */
	public static function get( string $key = null, $fallback = null ) {
		$instance = self::get_instance();

        // Return everything
        if ( is_null($key) ) {
            return $instance->context;
        }

        $parsed = explode('.', $key);

        $variable = $instance->context;

        while ($parsed) {
            $next = array_shift($parsed);

			if (is_array($variable) && isset($variable[$next])) {
				$variable = $variable[$next];
			} elseif (is_object($variable) && isset($variable->$next)) {
				$variable = $variable->$next;
			} else {
				$variable = null;
			}
        }

        return (is_null($variable) ? $fallback : $variable);

	}

	/**
	 * Returns the whole context with additional
	 * values merged.
	 *
	 * @param array $value
	 * @return array
	 */
	public static function with( array $value ) {
		$context = self::get();
		return array_merge( $context, $value );
	}

	public static function set( string $key, $value ) {
		$instance = self::get_instance();

        if ( is_array($key) ) {

            foreach ( $key as $var => $val ) {
                self::set($var, $val);
            }

        } elseif ( $value !== false ) {

			$parsed = explode('.', $key);

			$var = &$instance->context;

			while ( count($parsed) > 1 ) {
				$next = array_shift($parsed);

				if ( !isset($var[$next]) || !is_array($var[$next]) ) {
					$var[$next] = [];
				}

				$var = &$var[$next];
			}

			$var[ array_shift($parsed) ] = $value;

        }

	}

	/**
	 * Delete an item from the Context array
	 *
	 * @param string $key
	 * @return void
	 */
	public static function delete( string $key ) {
		$instance = self::get_instance();

		$context = $instance->context;

        if ( is_array($key) ) {
            foreach ( $key as $var ) {
                $instance->delete($var);
            }
        } else {
            if ( array_key_exists($key, $instance->context) ) {
                unset( $instance->context[$key]);
            }
        }

	}

	/**
	 * Add an alias to the context array
	 *
	 * Aliases are used to reference a key by a different name
	 *
	 * @param string $key
	 * @param string $alias
	 * @return void
	 */
	public static function alias( string $key, string $alias ) {
		$instance = self::get_instance();
		$instance->aliases[$alias] = $key;
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}