<?php

/*
 * Plugin Name: LEADHUB Developer Kit
 * Plugin URI: http://www.leadhub.net/
 * Description: Common utilities used by LEADHUB themes and landing pages.
 * Author: Leadhub
 * Author URI: http://www.leadhub.net/
 * License: GPLv2 or later
 */

add_action('plugins_loaded', function() {
    if(!class_exists('Leadhub\\Common')) {
        include __DIR__ . '/class_leadhub_common.php';
    }
});
