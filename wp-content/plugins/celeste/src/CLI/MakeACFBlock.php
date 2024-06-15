<?php

namespace Celeste\CLI;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\search;
use function Laravel\Prompts\note;

class MakeACFBlock {

	private $block = [];
	private $icons = [];

	private $block_path;
	private $icon_path;

	public function __construct() {
		$this->block_path = apply_filters(
			'celeste.cli.block_path',
			get_template_directory() . '/views/blocks'
		);

		$this->icon_path = apply_filters(
			'celeste.cli.icon_path',
			get_template_directory() . '/src/assets/icons'
		);

		// Get icons from the theme
		$this->icons = array_map(
			fn ($icon) => str_replace('.svg', '', $icon),
			array_filter(
				scandir( $this->icon_path ),
				fn ($icon) => ! in_array($icon, ['.', '..'])
			)
		);
	}

	public function command() {

		$this->block['title'] = text(
			label: 'Name',
			placeholder: 'e.g. Product Grid',
			required: true
		);

		$this->block['name'] = strtolower( str_replace( ' ', '-', $this->block['title'] ) );

		$this->block['icon'] = search(
            label: 'Icon',
            options: function( $search ) {
                return array_filter(
                    $this->icons,
                    fn ($icon) => strpos( $icon, $search ) !== false
                );
            }
        );

		$this->block['stylesheet'] = confirm(
			label: 'Include a style.css file?',
			default: false
		);

		$this->block['file'] = confirm(
			label: 'Include a block.php file?',
			default: false
		);

		$add_fields = confirm(
			label: 'Add fields?',
			default: false
		);

		if ( $add_fields ) {
			$add_fields = new AddFields();
			$this->block['fields'] = $add_fields->prompts();
		}

		return $this->make();
	}

	public function make() {

		if ( $this->block['icon'] ) {
			$icon_path = sprintf( '%s/%s.svg', $this->icon_path, $this->icons[ $this->block['icon'] ] );
			if ( !file_exists($icon_path) ) return;
			$icon = file_get_contents( $icon_path );
		}

		$blockJSON = [
			'name' => $this->block['name'],
			'title' => $this->block['title'],
			'description' => '',
			'category' => 'theme',
			'icon' => isset($icon) ? $icon : 'admin-site',
			'keywords' => [],
			'supports' => [
				'jsx' => true
			],
			'fields' => $this->block['fields'] ?? [],
			'acf' => [
				'mode' => 'preview',
				'renderCallback' => 'render_acf_block'
			]
		];

		$files = [
			'block.json' => json_encode( $blockJSON, JSON_PRETTY_PRINT ),
			'template.twig' => '<BlockWrapper :attributes="attributes" :block="block" :preview="is_preview">

</BlockWrapper>'
		];

		if ( $this->block['stylesheet'] ) {
			$files['style.css'] = sprintf( '/* .wp-block-acf-%s {} */', $this->block['name'] );
		}

		if ( $this->block['file'] ) {
			$files['block.php'] = '<?php

add_filter( \'celeste.block.' . $this->block['name'] . '\', function( $context ) {
	return $context;
} );';
		}

		mkdir($this->block_path . '/' . $this->block['name'], 0777, true);

		foreach ( $files as $file => $content ) {
			$filename = sprintf( $this->block_path . '/%s/%s', $this->block['name'], $file );
			file_put_contents( $filename, $content );
		}

		return $this->block['title'];

	}

}