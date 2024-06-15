<?php

namespace Celeste\Twilight;

use Twilight\Compiler;
use Twilight\Directives;
use Twilight\Tokenizer;
use Twilight\NodeTree;
use Twilight\Directives\AttributesDirective;
use Twilight\Directives\IfDirective;
use Twilight\Directives\ForDirective;
use Twilight\Directives\HtmlDirective;
use Twilight\Directives\TextDirective;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Twig {

	private $options = [];
	private $instance;
	private $loader;
	private $template;

	/**
	 * Constructor
	 *
	 * @param string $dir Path to the Twig templates directory.
	 */
	public function __construct( private string $dir, array $options = [] ) {

		// Set Options
		$this->options = array_merge( [ 'dry_run' => false ], $options );

		// Twig Environment Options.
		$options = apply_filters( 'twilight.twig.environment', [ 'cache' => false ] );
		$options = apply_filters( 'celeste.twig.environment', $options );

		$this->loader = new FilesystemLoader( $this->dir );
		$this->instance = new Environment( $this->loader, $options );

		// Register Functions.
		$this->register_functions();

		// Expose the Twig instance through a hook for theme authors.
		do_action( 'twilight.twig', $this->instance ); // Deprecated
		do_action( 'celeste.twig', $this->instance );

	}

	/**
	 * Render a Twig Template
	 *
	 * @param string $template Template name or raw template string.
	 * @param array $context Context to pass to the template.
	 * @param string $type Type of template to render. Either 'file' or 'string'.
	 * @return string Rendered template.
	 */
	public function render( string $template, array $context = [], string $type = 'file' ) {

		$this->template = $template;

		// Load the template
		if ($type === 'file') {
			$template_path = $this->loader->getSourceContext( $template )->getPath();
			$template_raw = file_get_contents( $template_path );
		} else {
			$template_raw = $template;
		}

		// Parse and Render Components
		$template_with_components = $this->parse_components( $template_raw, $context );

		// Render the view.
		return $this->instance->createTemplate($template_with_components)->render( $context );

	}

	/**
	 * Render a Component
	 *
	 * @param string $name Component name.
	 * @param array $context Context to pass to the component.
	 * @return void
	 */
	public function render_component( string $name, array $context = [] ) {

		try {
			$context = apply_filters( 'twilight.component', $context ); // Deprecated
			$context = apply_filters( 'twilight.component.' . $name, $context ); // Deprecated

			$context = apply_filters( 'celeste.component', $context );
			$context = apply_filters( 'celeste.component.' . $name, $context );

			return $this->render( 'components/' . $name . '/template.twig', $context );
		} catch ( \Exception $e ) {
			return $this->render( 'components/Error/template.twig', [
				'error' => $e,
				'component' => $name,
				'context' => $context,
				'env' => wp_get_environment_type(),
				'root' => ABSPATH,
			] );
		}

	}

	/**
	 * Parse Components
	 *
	 * @param string $string
	 * @return string Rendered component HTML
	 */
	public function parse_components( string $string ) {
		// $parser = new Parser;
		// return $parser->parse( $string );

		$directives = new Directives;
		$directives->register('attributes', AttributesDirective::class);
		$directives->register('if', IfDirective::class);
		$directives->register('for', ForDirective::class);
		$directives->register('html', HtmlDirective::class);
		$directives->register('text', TextDirective::class);

		$tokenizer = new Tokenizer($string, ['ignore' => ['InnerBlocks']]);
		$tree = new NodeTree($tokenizer->tokenize(), $directives);
		$elements = $tree->create();

		$compiler = new Compiler();
		$template = $compiler->compile($elements);

		$name = str_replace( ['blocks/', 'components/'], '', $this->template );
		$name = str_replace( '/template', '', $name );
		file_put_contents( CELESTE_PLUGIN_PATH . '/.twig/' . $name, $template );

		return $template;
	}

	/**
	 * Register Functions
	 *
	 * @return void
	 */
	private function register_functions() {
		$this->instance->addFunction(
			new TwigFunction( 'render_component', [$this, 'render_component'], [ 'is_safe' => [ 'html' ] ] )
		);
	}

	/**
	 * Get Option
	 *
	 * @param string $key
	 * @return mixed
	 */
	private function get_option( string $key ) {
		return $this->options[$key] ?? null;
	}

}