<?php

namespace Celeste\CLI;

use WP_CLI;
use function Laravel\Prompts\search;

class Commands {

    public function __construct() {
        WP_CLI::add_command( 'celeste add component', [ $this, 'add_component' ] );
        WP_CLI::add_command( 'celeste add block', [ $this, 'add_block' ] );
        WP_CLI::add_command( 'celeste pull component', [ $this, 'pull_component' ] );
        WP_CLI::add_command( 'celeste pull block', [ $this, 'pull_block' ] );
        WP_CLI::add_command( 'celeste update theme', [ $this, 'update_theme' ] );
        WP_CLI::add_command( 'celeste prepare', [ $this, 'prepare_deploy' ] );
    }

    public function add_component() {
        $make_component = new MakeComponent();
        $component = $make_component->command();
        WP_CLI::success( sprintf('Added %s Component', $component) );
    }

    public function add_block() {
        $make_block = new MakeACFBlock();
        $block = $make_block->command();
        WP_CLI::success( sprintf('Added %s Block', $block) );
    }

    public function pull_component( $args ) {
        $pull = new PullFromGithub();

        if ( ! isset( $args[0] ) ) {
            WP_CLI::log('🔎 Getting list of components...');
            $components = $pull->list( 'component' );

            $name = search(
                label: 'Choose a component',
                options: function( $search ) use ( $components ) {
                    return array_filter(
                        $components,
                        fn ($component) => strpos( strtolower($component), $search ) !== false
                    );
                }
            );
        } else {
            $name = $args[0];
        }

        $pull->copy( $name, 'component' );
    }

    public function pull_block( $args ) {
        $pull = new PullFromGithub();

        if ( ! isset( $args[0] ) ) {
            WP_CLI::log('🔎 Getting list of blocks...');
            $blocks = $pull->list( 'block' );

            $name = search(
                label: 'Choose a block',
                options: function( $search ) use ( $blocks ) {
                    return array_filter(
                        $blocks,
                        fn ($block) => strpos( strtolower($block), $search ) !== false
                    );
                }
            );
        } else {
            $name = $args[0];
        }

        $pull->copy( $name, 'block' );
    }

    public function update_theme() {
        $update = new UpdateTheme();
        $update->command();
    }

    public function prepare_deploy() {
        $prepare = new PrepareCpanelDeployScript();
        $prepare->command();
    }

}