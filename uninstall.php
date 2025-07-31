<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('hk_translate_settings');
delete_option('hk_translate_version');

// Clear any cached data
wp_cache_delete('hk_translate_languages', 'hk_translate');
wp_cache_delete('hk_translate_settings', 'hk_translate');