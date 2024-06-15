<?php

namespace Celeste\CLI;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;

class PrepareCpanelDeployScript {

    private string $script;

    public function command() {
		$username = text(
			label: 'cPanel Account Username',
			required: true
		);

		$theme_name = text(
			label: 'Theme Name',
            default: 'celeste',
			required: true
		);

        $file = ABSPATH . '.cpanel.yml';
        $should_overwrite = true;

        if ( file_exists( $file ) ) {
            $should_overwrite = $this->confirm_before_overwriting();
        }

        if ( ! $should_overwrite ) {
            return;
        }

        $script = '---
deployment:
  tasks:
    - /bin/cp -R wp-content/themes/{theme_name} /home/{username}/public_html/wp-content/themes/
    - /bin/cp -R wp-content/plugins/celeste /home/{username}/public_html/wp-content/plugins/';

        $script = str_replace( '{username}', $username, $script );
        $script = str_replace( '{theme_name}', $theme_name, $script );

        file_put_contents( $file, $script );

        info('🎉 cPanel Deploy Script updated!');
    }

    private function confirm_before_overwriting() {
        return confirm(
            label: '.cpanel.yml already exists, would you like to overwrite it?',
            default: false,
            yes: 'Yes',
            no: 'No'
        );
    }

}