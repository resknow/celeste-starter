<?php

namespace Celeste\CLI;

use function Laravel\Prompts\text;
use function Laravel\Prompts\select;

class MakeComponent {

	private $component_path;

	public function __construct() {
		$this->component_path = apply_filters(
			'celeste.cli.component_path',
			get_template_directory() . '/views/components/'
		);
	}

	public function command() {
		$name = text(
			label: 'Name',
			placeholder: 'e.g. OpeningTimes',
			required: true,
			validate: fn (string $value) => match(true) {
				! ctype_upper($value[0]) => 'Component names must start with a capital letter',
				default => null
			}
		);

		$stylesheet = select(
			label: 'Include a style.css file?',
			options: [ 'No', 'Yes' ],
			default: 'No'
		);

		$script = select(
			label: 'Include a main.js file?',
			options: [ 'No', 'Yes' ],
			default: 'No'
		);

		$component_file = select(
			label: 'Include a component.php file?',
			options: [ 'No', 'Yes' ],
			default: 'No'
		);

		return $this->make( $name, $stylesheet === 'Yes', $component_file === 'Yes', $script === 'Yes');
	}

	public function make( $name, $with_stylesheet = false, $with_component_file = false, $with_script = false ) {
		$files = [
			'template.twig' => sprintf( '<div class="%s"></div>', $name ),
			'component.json' => json_encode( [ 'name' => $name ], JSON_PRETTY_PRINT )
		];

		if ( $with_stylesheet ) {
			$files['style.css'] = sprintf( '/* .%s {} */', $name );
		}

		if ( $with_script ) {
			$files['main.js'] = sprintf( '// %s component', $name );
		}

		if ( $with_component_file ) {
			$files['component.php'] = '<?php

// Do something before the page is rendered, like enqueueing assets
add_filter( \'celeste.component.' . $name . '.present\', function( $context ) {

    // Note this is the parent template context, NOT the component context
    return $context;
} );

// Do something before the component is rendered, like manipulating the context
add_filter( \'celeste.component.' . $name . '\', function( $context ) {
    [ $props, $rest ] = props( \'' . $name . '\', $context, [] );
    $context = array_merge( $context, $props );
    $context[\'attributes\'] = attributes($rest);

	return $context;
} );';
		}

		mkdir($this->component_path . '/' . $name, 0777, true);

		foreach ( $files as $file => $content ) {
			$filename = sprintf( '%s/%s/%s', $this->component_path, $name, $file );
			file_put_contents( $filename, $content );
		}

		return $name;
	}

}