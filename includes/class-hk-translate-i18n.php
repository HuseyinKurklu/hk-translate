<?php

/**
 * Define the internationalization functionality
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class HK_Translate_i18n {

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'hk-translate',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}