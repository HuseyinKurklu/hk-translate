<?php

/**
 * Fired during plugin activation
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class HK_Translate_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     */
    public static function activate() {
        // Detect site default language
        $site_locale = get_locale(); // e.g., 'tr_TR', 'en_US', 'de_DE'
        $site_lang_code = substr($site_locale, 0, 2); // Extract first 2 characters
        
        // Load languages class if not already loaded
        if (!class_exists('HK_Translate_Languages')) {
            require_once HK_TRANSLATE_PLUGIN_DIR . 'includes/class-hk-translate-languages.php';
        }
        
        // Validate if detected language is supported
        if (!HK_Translate_Languages::is_valid_language($site_lang_code)) {
            $site_lang_code = 'tr'; // Fallback to Turkish
        }
        
        // Build default enabled languages with site language first
        $default_enabled_languages = array($site_lang_code);
        
        // Add other essential languages (if not already included)
        $essential_languages = array('en', 'de', 'fr', 'it', 'es', 'pt', 'ru', 'ar', 'tr');
        foreach ($essential_languages as $lang) {
            if (!in_array($lang, $default_enabled_languages)) {
                $default_enabled_languages[] = $lang;
            }
        }

        // Set default plugin settings
        $default_settings = array(
            'enabled_languages' => $default_enabled_languages,
            'desktop_bottom' => '20',
            'tablet_bottom' => '15',
            'mobile_bottom' => '10',
            'desktop_size' => '40',
            'tablet_size' => '36',
            'mobile_size' => '32',
            'menu_height' => '250',
            'desktop_position' => 'bottom-right',
            'tablet_position' => 'bottom-right',
            'mobile_position' => 'bottom-right',
            'default_language' => $site_lang_code, // Use detected site language
            'plugin_enabled' => true,
            'auto_detect_language' => false,
            'compact_mode' => false, // Compact mode: Show only flags (no text)
            'language_order' => $default_enabled_languages, // Language display order (same as enabled by default)
            // Visual settings defaults
            'btn_bg_enabled' => true,
            'btn_bg_color' => '#ffffff',
            'btn_border_enabled' => true,
            'btn_border_color' => '#dddddd',
            'btn_border_width' => 2,
            'btn_border_radius' => 50,
            'btn_flag_radius' => 50,
            'btn_hover_color' => '#007cba',
            'menu_bg_color' => '#ffffff',
            'menu_border_enabled' => true,
            'menu_border_color' => '#dddddd',
            'menu_border_width' => 1,
            'menu_border_radius' => 8,
            'menu_width' => 200,
            'menu_text_color' => '#333333',
            'menu_hover_bg_color' => '#0073aa',
            'menu_hover_text_color' => '#ffffff',
            'menu_active_bg_color' => '#e7f3ff',
            'menu_active_text_color' => '#007cba'
        );

        // Only set defaults if settings don't exist
        if (!get_option('hk_translate_settings')) {
            add_option('hk_translate_settings', $default_settings);
        }

        // Set plugin version
        add_option('hk_translate_version', HK_TRANSLATE_VERSION);

        // Create custom database tables for manual translations
        self::create_manual_translation_tables();
    }

    /**
     * Create database tables for manual translations
     */
    private static function create_manual_translation_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Table 1: Manual Translations
        $table_manual_translations = $wpdb->prefix . 'hk_translate_manual_translations';
        
        $sql_manual_translations = "CREATE TABLE $table_manual_translations (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_url varchar(500) NOT NULL,
            source_language varchar(10) NOT NULL DEFAULT 'tr',
            target_language varchar(10) NOT NULL,
            original_text longtext NOT NULL,
            manual_translation longtext,
            text_hash varchar(64) NOT NULL,
            element_selector varchar(255) DEFAULT NULL,
            element_path varchar(1000) DEFAULT NULL,
            is_excluded tinyint(1) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            is_outdated tinyint(1) DEFAULT 0,
            outdated_date datetime DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_date datetime DEFAULT CURRENT_TIMESTAMP,
            updated_date datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_site_target_lang (site_url(191), target_language),
            KEY idx_text_hash_lang (text_hash, target_language),
            KEY idx_source_target_lang (source_language, target_language),
            KEY idx_created_date (created_date),
            KEY idx_outdated (is_outdated, outdated_date),
            UNIQUE KEY unique_translation (text_hash, target_language, site_url(191))
        ) $charset_collate;";

        // Table 2: Edit Sessions (for tracking who edited what)
        $table_edit_sessions = $wpdb->prefix . 'hk_translate_edit_sessions';
        
        $sql_edit_sessions = "CREATE TABLE $table_edit_sessions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            page_url varchar(500) NOT NULL,
            target_language varchar(10) NOT NULL,
            session_start datetime DEFAULT CURRENT_TIMESTAMP,
            session_end datetime DEFAULT NULL,
            total_edits int(11) DEFAULT 0,
            PRIMARY KEY (id),
            KEY idx_user_date (user_id, session_start),
            KEY idx_page_lang (page_url(191), target_language)
        ) $charset_collate;";

        // Table 3: Translation Statistics (optional - for admin insights)
        $table_stats = $wpdb->prefix . 'hk_translate_stats';
        
        $sql_stats = "CREATE TABLE $table_stats (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            page_url varchar(500) NOT NULL,
            language varchar(10) NOT NULL,
            total_texts int(11) DEFAULT 0,
            manual_translations int(11) DEFAULT 0,
            excluded_texts int(11) DEFAULT 0,
            outdated_translations int(11) DEFAULT 0,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_page_lang (page_url(191), language)
        ) $charset_collate;";

        // WordPress dbDelta function for safe table creation
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_manual_translations);
        dbDelta($sql_edit_sessions);
        dbDelta($sql_stats);

        // Add database version for future upgrades
        add_option('hk_translate_db_version', '1.0');
    }
}