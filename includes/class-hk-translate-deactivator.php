<?php

/**
 * Fired during plugin deactivation
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class HK_Translate_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     */
    public static function deactivate() {
        // Clean up any temporary data, caches, etc.
        // Note: We don't delete settings on deactivation, only on uninstall
        
        // Clear any cached data
        wp_cache_delete('hk_translate_languages', 'hk_translate');
        wp_cache_delete('hk_translate_settings', 'hk_translate');
    }
}