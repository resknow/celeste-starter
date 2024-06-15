<?php

namespace Celeste\Twilight;

use Celeste\Twilight\Directives\Apply;
use Celeste\Twilight\Directives\ForLoop;
use Celeste\Twilight\Directives\IfCondition;
use Celeste\Twilight\Directives\ElseCondition;
use Celeste\Twilight\Directives\Verbatim;

class Convert {

	private $directives;
	private $parser;
	private $render_ref;

	public function __construct( private Component $component ) {
		$this->parser = new Parser([
			'ignore' => ['InnerBlocks'],
			'parse_slots' => true
		]);

		/**
		 * Setup a render_ref for Children
		 */
		$this->render_ref = sprintf( '%s_%s', $this->component->name(), $this->component->ref() );
	}

	/**
	 * Convert
	 *
	 * Convert a Component to valid Twig syntax
	 *
	 * @return string
	 */
	public function convert() {

		/**
		 * If this component has directives, convert them
		 */
		$this->set_directives();

		$twig = '';

		/**
		 * If this component has Children, let's pull the content
		 * up into a variable so we can pass it to the render function
		 */
		if ( $this->component->has_children() ) {
			$twig .= sprintf(
				'{%% set %s %%}%s{%% endset %%}%s',
				$this->render_ref,
				$this->parser->parse($this->component->children()),
				PHP_EOL
			);
		}

		/**
		 * If this component has Slots, let's pull the content
		 * up into a variable so we can pass it to the render function
		 */
		if ( $this->component->has_slots() ) {
			foreach ( $this->component->slots() as $name => $content ) {
				$twig .= sprintf(
					'{%% set %s_slot_%s %%}%s{%% endset %%}%s',
					$this->render_ref,
					$name,
					$content,
					PHP_EOL
				);
			}
		}

		/**
		 * Open the render_component function
		 */
		$twig .= sprintf( '<!-- %s -->%s', str_replace('/', '.', $this->component->name()), PHP_EOL );
		$twig .= $this->convert_directives( 'before', $twig );
		$twig .= sprintf( '{{ render_component("%s"', $this->component->name() );

		/**
		 * If this component has props, let's convert those into a Twig
		 * hash and pass them to the render function
		 */
		$slots = $this->convert_slots();
		$twig .= $this->convert_props($slots);

		/**
		 * Close the render_component function
		 */
		$twig .= ') }}' . PHP_EOL;
		$twig .= $this->convert_directives( 'after', $twig );
		$twig .= sprintf( '<!-- /%s -->%s', str_replace('/', '.', $this->component->name()), PHP_EOL );

		return $twig;
	}

	/**
	 * Convert Slots
	 *
	 * Convert slots into an array that we can pass as props
	 *
	 * @return array
	 */
	private function convert_slots() {
		$slots = [];

		if ( $this->component->has_slots() ) {
			foreach ( $this->component->slots() as $key => $value ) {
				$slots[] = sprintf(
					'"%s": %s',
					$key,
					$this->render_ref . '_slot_' . $key
				);
			}
		}

		return $slots;
	}

	/**
	 * Convert Props
	 *
	 * Convert props into a Twig hash that we can pass as props
	 *
	 * @param array $slots
	 * @return string
	 */
	private function convert_props( array $slots = [] ) {
		$props = [];

		if ( $this->component->has_props() ) {
			foreach ( $this->component->props() as $key => $value ) {

				/**
				 * Unless this prop is dynamic, we need to wrap the value in
				 * quotes so Twig will treat it as a string.
				 */
				if ( ! $this->component->is_prop_dynamic($key) ) {
					$value = sprintf( '"%s"', $value );
				}

				/**
				 * Remove the : from the dynamic prop key
				 */
				if ( $this->component->is_prop_dynamic($key) ) {
					$key = str_replace( ':', '', $key );
				}

				$props[] = sprintf(
					'"%s": %s',
					$key,
					$value
				);
			}
		}

		/**
		 * Add the render_ref for children if it exists
		 */
		if ( $this->component->has_children() ) {
			$props[] = sprintf(
				'"children": %s',
				$this->render_ref
			);
		}

		/**
		 * Add the render_ref for slots if they exist
		 */
		if ( ! empty($slots) ) {
			$props[] = sprintf(
				'"slots": { %s }',
				implode( ', ', $slots )
			);
		}

		return empty($props)
			? ''
			: sprintf( ', { %s }', implode( ', ', $props ) );
	}

	/**
	 * Set Directives
	 *
	 * Directives are special attributes that are converted into standard
	 * Twig control structure, like if and for loops.
	 */
	private function set_directives() {

		if ( ! $this->component->has_props() ) {
			return;
		}

		foreach ( $this->component->props() as $key => $value ) {

			// If this prop is not a directive, skip it
			if ( ! $this->component->is_prop_directive( $key ) ) {
				continue;
			}

			if ( $key === '@apply' ) {
				$directive = new Apply( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 50,
				];
			}

			if ( $key === '@for' ) {
				$directive = new ForLoop( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 50,
				];
			}

			if ( $key === '@if' ) {
				$directive = new IfCondition( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 50,
				];
			}

			if ( $key === '@else' ) {
				$directive = new ElseCondition( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 10,
				];
			}

			if ( $key === '@verbatim' ) {
				$directive = new Verbatim( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 50,
				];
			}

			/**
			 * Sort the directives by weight, lightest to heaviest
			 *
			 * This is to ensure that the directives are output in the correct order
			 */
			if ( !empty($this->directives) ) {
				uasort( $this->directives, function( $a, $b ) {
					return $a['weight'] <=> $b['weight'];
				});
			}

		}

	}

	/**
	 * Convert Directives
	 *
	 * Convert directive markup to Twig syntax
	 *
	 * @param string $method before|after
	 * @param string $twig
	 * @return string
	 */
	private function convert_directives( string $method, string $twig ) : string {
		if ( empty($this->directives) ) {
			return '';
		}

		$markup = '';

		foreach ( $this->directives as $key => $directive ) {
			$markup .= call_user_func( $directive[ $method ], $twig, $this->component );
		}

		return $markup;
	}

}