<?php

/**
 * Plugin Name: Celeste
 * Plugin URI: https://celeste.resknow.co.uk
 * Description: The toolkit for modern WordPress
 * Version: 0.2.0
 * Author: Chris Galbraith
 * Author URI: https://resknow.co.uk
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: celeste
 * Requires PHP: 8.2
 * Requires at least: 6.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Celeste Version
 *
 * @since 0.1.0
 */
define( 'CELESTE_VERSION', '0.2.0' );

/**
 * Celeste Path
 *
 * @since 0.1.0
 */
define( 'CELESTE_PLUGIN_PATH', plugin_dir_path(__FILE__) );

/**
 * Celeste URL
 *
 * @since 0.1.0
 */
define( 'CELESTE_PLUGIN_URL', plugin_dir_url(__FILE__) );

/**
 * Include dependencies
 *
 * @since 0.1.0
 */
require_once CELESTE_PLUGIN_PATH . '/vendor/autoload.php';

/**
 * Update Check
 *
 * @since 0.1.0
 */
new Celeste\Plugin\UpdateCheck;

/**
 * Theme Check
 *
 * @since 0.1.0
 */
require_once CELESTE_PLUGIN_PATH . '/actions/env-check.php';

/**
 * Blocks Dir
 *
 * @since 0.1.0
 */
define( 'CELESTE_BLOCKS_DIR', CELESTE_PLUGIN_PATH . '/blocks' );

/**
 * Include functions
 *
 * @since 0.1.0
 */
require_once CELESTE_PLUGIN_PATH . '/functions/utilities.php';
require_once CELESTE_PLUGIN_PATH . '/functions/blocks.php';
require_once CELESTE_PLUGIN_PATH . '/functions/cli.php';
require_once CELESTE_PLUGIN_PATH . '/functions/frontend.php';
require_once CELESTE_PLUGIN_PATH . '/functions/helpers.php';
require_once CELESTE_PLUGIN_PATH . '/functions/twilight.php';
require_once CELESTE_PLUGIN_PATH . '/functions/assets.php';

/**
 * Include actions
 *
 * @since 0.1.0
 */
require_once CELESTE_PLUGIN_PATH . '/actions/acf.php';
require_once CELESTE_PLUGIN_PATH . '/actions/admin.php';
require_once CELESTE_PLUGIN_PATH . '/actions/assets.php';
require_once CELESTE_PLUGIN_PATH . '/actions/blocks.php';
require_once CELESTE_PLUGIN_PATH . '/actions/frontend.php';
require_once CELESTE_PLUGIN_PATH . '/actions/login.php';
require_once CELESTE_PLUGIN_PATH . '/actions/theme.php';
require_once CELESTE_PLUGIN_PATH . '/actions/twilight.php';
require_once CELESTE_PLUGIN_PATH . '/actions/updates.php';
require_once CELESTE_PLUGIN_PATH . '/actions/user.php';
require_once CELESTE_PLUGIN_PATH . '/actions/warnings.php';

/**
 * Include filters
 *
 * @since 0.1.0
 */
require_once CELESTE_PLUGIN_PATH . '/filters/acf.php';
require_once CELESTE_PLUGIN_PATH . '/filters/assets.php';
require_once CELESTE_PLUGIN_PATH . '/filters/blocks.php';
require_once CELESTE_PLUGIN_PATH . '/filters/login.php';
require_once CELESTE_PLUGIN_PATH . '/filters/twilight.php';

/**
 * Setup Options Page
 */
if ( is_admin() ) {
    new Celeste\AdminUI\OptionScreen;
}

/**
 * Initialize CLI
 *
 * @since 0.1.0
 */
if ( defined('WP_CLI') && WP_CLI === true ) {
    new Celeste\CLI\Commands;
}

/**
 * Include component PHP files
 *
 * @since 0.1.0
 */
celeste_include_component_php();