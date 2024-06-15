<?php

namespace Celeste\CLI;

use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use WP_CLI;

/**
 * Command to fetch and copy GitHub components.
 *
 * TODO Handle Sub Components
 */
class PullFromGithub {

    const REPO = 'resknow/celeste-components';
    const BRANCH = 'main';

    private array $directories;
    private string $trigger = '';
    private array $will_install = [];
    private array $just_installed = [];
    private string $view_path;
    private string $github_token;

    public function __construct() {
		$this->view_path = apply_filters(
			'celeste.cli.view_path',
			get_template_directory() . '/views'
		);

        $this->directories = [
            'component' => 'components',
            'block' => 'blocks'
        ];

        $this->github_token = get_option( 'github_access_token' );
    }

    /**
     * Copies components from GitHub repo to local directory.
     *
     * @param string $name The name of the component to copy.
     * @param string $type The type of component to copy.
     */
    public function copy( string $name, string $type = 'component' ) {

        // If no trigger has been set, that means this one is the trigger
        if ( empty( $this->trigger ) ) {
            $this->trigger = $name;
        }

        $dir = sprintf( '%s/%s/%s', $this->view_path, $this->directories[ $type ], $name );
        $component_exists = false;

        if ( file_exists($dir) ) {
            $component_exists = true;
            WP_CLI::warning( sprintf( '😮 The %s %s already exists locally.', $name, $type ) );

            $continue = select(
                label: sprintf(
                    '%s already exists in your project, would you like to overwrite it?',
                    $name
                ),
                options: [ 'No', 'Yes', 'Rename' ],
                default: 'No'
            );

            if ( $continue === 'No' ) {
                WP_CLI::log('😮‍💨 Cancelled, no files have been changed.');
                return;
            }

            if ( $continue === 'Yes' ) {
                celeste_cli_remove_dir( $dir ); // Remove the existing component directory
            }

            if ( $continue === 'Rename' ) {
                $new_name = text(
                    label: 'Enter a new name for the component:'
                );

                $component_exists = false;
                $dir = sprintf( '%s/%s/%s', $this->view_path, $this->directories[ $type ], $new_name );
            }
        }

        $url = sprintf(
            'https://api.github.com/repos/%s/contents/%s/%s?ref=%s',
            self::REPO,
            $this->directories[ $type ],
            $name,
            self::BRANCH
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: token ' . $this->github_token ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Celeste');
        $response = curl_exec($ch);

        if ( curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200 ) {
            $files = json_decode($response, true);

            // Get Metadata
            $meta = $this->get_metadata( $files, $type );

            if ( ! $meta ) {
                WP_CLI::error( sprintf( '🫣 Failed to fetch the metadata for %s.', $name ) );
            }

            // Start Install
            $this->setup_install( $meta, $type );
            $this->install( $dir, $files, $meta );
        } else {
            WP_CLI::error( sprintf( '😔 Failed to fetch the %s %s from GitHub.', $name, $type ) );
        }

        WP_CLI::success( sprintf(
            '👍 %s the %s %s %s your project!',
            $component_exists ? 'Updated' : 'Added',
            isset( $new_name ) ? $new_name : $name,
            $type,
            $component_exists ? 'in' : 'to'
        ) );

        curl_close($ch);
    }

    public function list( string $type ) {
        $url = sprintf(
            'https://api.github.com/repos/%s/contents/%s?ref=%s',
            self::REPO,
            $this->directories[ $type ],
            self::BRANCH
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: token ' . $this->github_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Celeste');
        $response = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            $directories = json_decode($response, true);
            $results = [];

            foreach ( $directories as $directory ) {
                if ( $directory['type'] == 'dir' ) {
                    $results[] = basename($directory['path']);
                }
            }

            return $results;
        } else {
            WP_CLI::error( sprintf( '😕 Failed to fetch the %s from GitHub.', $type ) );
        }
    }

    /**
     * Install
     */
    private function install( string $dir, array $files, array $meta ) {
        $this->copy_files( $dir, $files );

        // Just Installed
        $this->just_installed( $meta['name'], $meta['type'] );

        // Check if the component has dependencies
        if (
            isset( $meta['requires'] )
            || isset( $meta['celeste'] )
            && isset( $meta['celeste']['requires'] )
        ) {
            $requires = $meta['requires'] ?? $meta['celeste']['requires'];

            foreach ( $requires as $require ) {

                // If the trigger is the same as the required component, skip it
                if ( $this->trigger === $require['name'] ) {
                    continue;
                }

                // If the component was just installed, skip it
                if ( $this->did_just_install( $require['name'], $require['type'] ) ) {
                    continue;
                }

                WP_CLI::log( sprintf( '🛠️ Installing required %s: %s...', $require['type'], $require['name'] ) );
                $this->copy( $require['name'], $require['type'] );
            }
        }

    }

    /**
     * Copy Files
     */
    private function copy_files( string $dir, array $files ) {
        if ( ! is_dir($dir) ) {
            mkdir($dir, 0755, true);
        }

        foreach ( $files as $file ) {
            if ( $file['type'] == 'file' ) {
                $content = file_get_contents($file['download_url']);
                file_put_contents(
                    sprintf( '%s/%s', $dir, basename($file['path']) ),
                    $content
                );
                WP_CLI::log( sprintf( '🚚 Copying %s...', basename($file['path']) ) );
            }
        }
    }

    /**
     * Get the metadata for a component.
     *
     * @param array $files The files to search for metadata.
     * @param string $type The type of component to search for.
     * @return array|null
     */
    private function get_metadata( array $files, string $type ) {
        $json = array_filter( $files, function($file) use($type) {
            return $file['name'] === sprintf('%s.json', $type);
        } );

        if ( empty( $json ) ) {
            return null;
        }

        $json = array_values($json);

        $meta = file_get_contents( $json[0]['download_url'] );
        $meta = json_decode($meta, true);
        $meta['type'] = $type;

        return $meta;
    }

    /**
     * Start Install
     *
     * @param array $meta The metadata for the component.
     * @param string $type The type of component to install.
     */
    private function setup_install( array $meta, string $type ) {
        $this->will_install( $meta['name'], $type, $meta['version'] );

        // Check if the component has dependencies
        if ( $type === 'component' && isset( $meta['requires'] ) ) {
            foreach ( $meta['requires'] as $require ) {
                $this->will_install( $require['name'], $require['type'], $require['version'] );
            }
        }

        if ( $type === 'block' && isset( $meta['celeste'] ) && isset( $meta['celeste']['requires'] ) ) {
            foreach ( $meta['celeste']['requires'] as $require ) {
                $this->will_install( $require['name'], $require['type'], $require['version'] );
            }
        }
    }

    /**
     * Will Install
     *
     * @param string $name The name of the component to install.
     * @param string $type The type of component to install.
     */
    private function will_install( string $name, string $type, string $version = null ) {
        // Check if the component is already in the list
        if ( isset( $this->will_install[ $name ] ) ) {
            return;
        }

        $this->will_install[ $name ] = [
            'name' => $name,
            'type' => $type,
            'version' => $version
        ];
    }

    /**
     * Just Installed
     *
     * Keep track of components and blocks we just installed and don't prompt to install them again.
     *
     * @param string $name The name of the component to install.
     * @param string $type The type of component to install.
     */
    private function just_installed( string $name, string $type ) {
        $this->just_installed[ $name ] = $type;
    }

    /**
     * Did Just Install?
     *
     * Check if a component was just installed.
     *
     * @param string $name The name of the component to check.
     * @param string $type The type of component to check.
     * @return bool
     */
    private function did_just_install( string $name, string $type ) {
        return isset( $this->just_installed[ $name ] ) && $this->just_installed[ $name ] === $type;
    }
}