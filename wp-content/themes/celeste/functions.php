<?php

/**
 * 👋
 *
 * This is your themes "functions.php", which is a place you'll often
 * see mentioned in tutorials or answers to questions about how to
 * add or edit functionality in WordPress.
 *
 * Please don't paste things in to this file, it can get very messy and
 * hard to reason about. Instead, place your snippet under the "lib/*"
 * directory in a relevantly named PHP file. If you can't find one
 * that already makes sense, create a new one and it'll be
 * included automatically for you.
 */

defined('ABSPATH') || exit;

if ( ! in_array( 'celeste/celeste.php', get_option('active_plugins') ) ) {
    throw new Error('Celeste theme requires the Celeste plugin to be active');
}

require_once __DIR__ . '/lib/context.php';
require_once __DIR__ . '/lib/theme.php';