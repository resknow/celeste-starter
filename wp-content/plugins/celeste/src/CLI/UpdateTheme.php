<?php

namespace Celeste\CLI;

use WP_CLI;
use function Laravel\Prompts\alert;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\warning;

class UpdateTheme {

    private array $files;
    private string $theme_path;
    private bool $should_update_package_json = false;

    public function __construct() {
        $this->theme_path = get_template_directory();
    }

    public function command() {
        $this->files = [
            '.nvmrc' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/.nvmrc' ),
            'editor.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/editor.js' ),
            'functions.php' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/functions.php' ),
            'lib/.celeste/build.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/build.js' ),
            'lib/.celeste/compile-js.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/compile-js.js' ),
            'lib/.celeste/compile-postcss.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/compile-postcss.js' ),
            'lib/.celeste/compile-scss.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/compile-scss.js' ),
            'lib/.celeste/copy-assets.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/copy-assets.js' ),
            'lib/.celeste/reload.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/reload.js' ),
            'lib/.celeste/utils.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/utils.js' ),
            'lib/.celeste/watch.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/bundler/watch.js' ),
            'postcss.config.cjs' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/postcss.config.cjs' ),
            'style.css' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/style.css' ),
            'tailwind.config.cjs' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/tailwind.config.cjs' ),
            'prettier.config.js' => file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/prettier.config.js' ),
        ];

        if ( $this->confirm_before_copying() ) {
            $this->clear_dir( $this->theme_path . '/lib/.celeste' );
            $this->maybe_update_package_json();
            $this->copy_stubs();

            info('🎉 Theme updated!');

            $this->maybe_show_packages_to_update_or_install();

            return;
        }

        error( '🚫 Update cancelled.' );
    }

    /**
     * Copy the stubs to the theme directory
     */
    private function copy_stubs() {
        if ( !is_array( $this->files ) || empty( $this->files ) ) return;

        note( sprintf('Updating %s files...', count($this->files)) );
        note( join( ', ', array_keys($this->files) ) );

        foreach ( $this->files as $file => $content ) {
            $path = sprintf( '%s/%s', $this->theme_path, $file );
            file_put_contents( $path, $content );
        }
    }

    /**
     * Check if the user wants to continue with the update
     */
    private function confirm_before_copying() {
        return confirm(
            label: 'Are you sure you want to update the theme?',
            default: false,
            yes: 'Yes',
            no: 'No',
            hint: 'Warning, updating theme files is strongly discouraged if your site not in active development.'
        );
    }

    /**
     * Clear the directory before copying the new files
     *
     * @param string $dir
     */
    private function clear_dir( string $dir ) {
        celeste_cli_remove_dir( $dir );
        mkdir($dir, 0777, true);
    }

    /**
     * Maybe update the package.json file
     */
    private function maybe_update_package_json() {
        $should_update_package_json = confirm(
            label: 'Would you like to update the package.json file?',
            default: false,
            yes: 'Yes',
            no: 'No',
            hint: 'Warning: This will merge the package.json files and may overwrite your existing configuration.'
        );

        if ( $should_update_package_json ) {
            $this->should_update_package_json = true;
            $old_package = json_decode( file_get_contents( $this->theme_path . '/package.json' ), true );
            $new_package = json_decode( file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/package.json' ), true );
            $this->files['package.json'] = json_encode( $this->merge_package_json( $old_package, $new_package ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
            return;
        }
    }

    /**
     * Merge package.json files
     *
     * @param array $old
     * @param array $new
     * @return array
     */
    private function merge_package_json( array $old, array $new ) {
        return celeste_cli_array_deep_merge( $old, $new );
    }

    /**
     * Diff package.json depdenencies
     *
     * Returns the updates in dependencies and devDependencies compared
     * to the current theme's package.json file.
     */
    private function diff_package_json_dependencies() {
        $old_package = json_decode( file_get_contents( $this->theme_path . '/package.json' ), true );
        $new_package = json_decode( file_get_contents( CELESTE_PLUGIN_PATH . 'assets/stubs/package.json' ), true );

        $diff = [
            'dependencies' => array_diff_assoc( $new_package['dependencies'], $old_package['dependencies'] ?? [] ),
            'devDependencies' => array_diff_assoc( $new_package['devDependencies'], $old_package['devDependencies'] ?? [] ),
        ];

        return $diff;
    }

    /**
     * Show packages that need to be installed or updated
     *
     * @param array $diff
     * @return string
     */
    private function show_packages_to_update_or_install( array $diff ) {
        $dependencies = '';
        $dev_dependencies = '';

        if ( !empty( $diff['dependencies'] ) ) {
            $dependencies .= 'Dependencies to install: ' . PHP_EOL . 'npm install --save ';
            foreach ( $diff['dependencies'] as $package => $version ) {
                $dependencies .= sprintf( '%s@%s', $package, $version ) . PHP_EOL;
            }
        }

        if ( !empty( $diff['devDependencies'] ) ) {
            $dev_dependencies .= 'Dev Dependencies to install: ' . PHP_EOL . 'npm install --save-dev ';
            foreach ( $diff['devDependencies'] as $package => $version ) {
                $dev_dependencies .= sprintf( '%s@%s', $package, $version ) . PHP_EOL;
            }
        }

        // If there are no dependencies to install, return early
        if ( empty( $dependencies ) && empty( $dev_dependencies ) ) return;

        return $dependencies . PHP_EOL . $dev_dependencies;
    }

    /**
     * Show packages that need to be installed or updated
     */
    private function maybe_show_packages_to_update_or_install() {
        if ( $this->should_update_package_json ) return;

        $diff = $this->diff_package_json_dependencies();

        if ( empty($diff['dependencies']) && empty($diff['devDependencies']) ) return;

        warning( 'It\'s recommended that you update the following NPM packages:' );
        note( $this->show_packages_to_update_or_install( $diff ) );
    }

}