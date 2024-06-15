<?php

namespace Celeste\AdminUI;

class OptionScreen {

    public function __construct() {
        add_action( 'admin_menu', [$this, 'add_menu'] );
        add_action( 'admin_init', [$this, 'register_settings'] );
    }

    public function add_menu() {
        add_menu_page(
            'Celeste',
            'Celeste',
            'manage_options',
            'celeste-options',
            [ $this, 'render' ],
            'dashicons-image-filter',
            80
        );
    }

    public function register_settings() {
        register_setting( 'celeste_options', 'github_access_token' );
        add_settings_section( 'celeste_options', 'Celeste Options', null, 'celeste_options' );
        add_settings_field( 'github_access_token', 'GitHub Access Token', [$this, 'github_access_token_field'], 'celeste_options', 'celeste_options' );
    }

    public function render() {
        echo '<div class="wrap">';
        $this->github_access_token_form();
        echo '</div>';
    }

    /**
     * Form to take a Github access token
     */
    public function github_access_token_form() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'celeste_options' ); ?>
            <?php do_settings_sections( 'celeste_options' ); ?>
            <?php submit_button(); ?>
        </form>
        <?php

    }

    public function github_access_token_field() {
        $value = get_option( 'github_access_token' );
        echo '<input type="text" name="github_access_token" value="' . esc_attr( $value ) . '" />';
    }

}