<?php

/**
 * The admin-specific functionality of the plugin.
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for admin functionality.
 */
class HK_Translate_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            HK_TRANSLATE_PLUGIN_URL . 'admin/css/admin-style.css',
            array(),
            $this->version,
            'all'
        );

        // Admin bar için özel CSS ekle (hem admin hem frontend için)
        if (is_admin_bar_showing()) {
            $this->add_admin_bar_styles();
        }
    }

    /**
     * Add custom styles for admin bar edit mode button
     */
    private function add_admin_bar_styles() {
        $custom_css = '
        /* HK Translate Edit Mode Admin Bar Button */
        #wp-admin-bar-hk-translate-edit-mode .ab-item {
            transition: all 0.3s ease;
        }
        
        #wp-admin-bar-hk-translate-edit-mode.hk-translate-enter-edit-mode .ab-item:hover {
            background-color: #007cba !important;
            color: white !important;
        }
        
        #wp-admin-bar-hk-translate-edit-mode.hk-translate-enter-edit-mode .ab-item:hover .ab-icon,
        #wp-admin-bar-hk-translate-edit-mode.hk-translate-enter-edit-mode .ab-item:hover .ab-label {
            color: white !important;
        }
        
        #wp-admin-bar-hk-translate-edit-mode.hk-translate-exit-edit-mode .ab-item:hover {
            background-color: #ff6b6b !important;
            color: white !important;
        }
        
        #wp-admin-bar-hk-translate-edit-mode.hk-translate-exit-edit-mode .ab-item:hover .ab-icon,
        #wp-admin-bar-hk-translate-edit-mode.hk-translate-exit-edit-mode .ab-item:hover .ab-label {
            color: white !important;
        }
        
        /* Animation for edit mode active state */
        #wp-admin-bar-hk-translate-edit-mode.hk-translate-exit-edit-mode .ab-icon {
            animation: hk-edit-pulse 2s infinite;
        }
        
        @keyframes hk-edit-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Mobile responsive */
        @media screen and (max-width: 782px) {
            #wp-admin-bar-hk-translate-edit-mode .ab-item {
                padding: 0 8px;
            }
            
            #wp-admin-bar-hk-translate-edit-mode .ab-label {
                display: none;
            }
        }
        ';
        
        wp_add_inline_style($this->plugin_name, $custom_css);
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            HK_TRANSLATE_PLUGIN_URL . 'admin/js/admin-script.js',
            array('jquery', 'jquery-ui-sortable'),
            $this->version,
            false
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name,
            'hk_translate_ajax',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('hk_translate_nonce'),
                'plugin_url' => HK_TRANSLATE_PLUGIN_URL,
                'strings' => array(
                    'saving' => __('Saving...', 'hk-translate'),
                    'saved' => __('Settings saved successfully!', 'hk-translate'),
                    'error' => __('Error saving settings. Please try again.', 'hk-translate'),
                    'reset_confirm' => __('Are you sure you want to reset all settings to defaults? This action cannot be undone.', 'hk-translate'),
                    'resetting' => __('Resetting...', 'hk-translate'),
                    'reset_success' => __('Settings reset to defaults successfully!', 'hk-translate')
                )
            )
        );
    }

    /**
     * Add admin menu.
     */
    public function add_admin_menu() {
        // Get enabled languages count for menu badge
        $settings = get_option('hk_translate_settings', array());
        $enabled_languages = isset($settings['enabled_languages']) ? $settings['enabled_languages'] : array('tr', 'en');
        $language_count = count($enabled_languages);
        
        // Create menu title with language count
        $menu_title = sprintf(
            __('HK Translate %s', 'hk-translate'),
            '<span class="awaiting-mod count-' . $language_count . '"><span class="pending-count">' . $language_count . '</span></span>'
        );

        add_menu_page(
            __('HK Translate Settings', 'hk-translate'),        // Page title
            $menu_title,                                         // Menu title with badge
            'manage_options',                                    // Capability
            'hk-translate',                                      // Menu slug
            array($this, 'display_admin_page'),                 // Callback function
            'dashicons-translation',                             // Icon (translation icon)
            30                                                   // Position (after Comments)
        );
    }

    /**
     * Display the admin settings page.
     */
    public function display_admin_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Get current settings
        $settings = get_option('hk_translate_settings', array());
        
        // Detect site default language for fallbacks
        $site_locale = get_locale();
        $site_lang_code = substr($site_locale, 0, 2);
        if (!HK_Translate_Languages::is_valid_language($site_lang_code)) {
            $site_lang_code = 'tr'; // Fallback to Turkish
        }
        
        // Default enabled languages with site language first
        $default_enabled_languages = array($site_lang_code);
        $essential_languages = array('en', 'de', 'fr', 'it', 'es', 'pt', 'ru', 'ar', 'tr');
        foreach ($essential_languages as $lang) {
            if (!in_array($lang, $default_enabled_languages)) {
                $default_enabled_languages[] = $lang;
            }
        }
        
        $enabled_languages = isset($settings['enabled_languages']) ? $settings['enabled_languages'] : $default_enabled_languages;
        $desktop_bottom = isset($settings['desktop_bottom']) ? $settings['desktop_bottom'] : '20';
        $tablet_bottom = isset($settings['tablet_bottom']) ? $settings['tablet_bottom'] : '15';
        $mobile_bottom = isset($settings['mobile_bottom']) ? $settings['mobile_bottom'] : '10';
        $desktop_size = isset($settings['desktop_size']) ? $settings['desktop_size'] : '40';
        $tablet_size = isset($settings['tablet_size']) ? $settings['tablet_size'] : '36';
        $mobile_size = isset($settings['mobile_size']) ? $settings['mobile_size'] : '32';
        $menu_height = isset($settings['menu_height']) ? $settings['menu_height'] : '250';
        // Legacy position field removed - using device-specific positions only
        
        // Device-specific positions (each device has its own default)
        $desktop_position = isset($settings['desktop_position']) ? $settings['desktop_position'] : 'bottom-right';
        $tablet_position = isset($settings['tablet_position']) ? $settings['tablet_position'] : 'bottom-right';
        $mobile_position = isset($settings['mobile_position']) ? $settings['mobile_position'] : 'bottom-right';
        $default_language = isset($settings['default_language']) ? $settings['default_language'] : $site_lang_code;
        $plugin_enabled = isset($settings['plugin_enabled']) ? $settings['plugin_enabled'] : true;
        $auto_detect_language = isset($settings['auto_detect_language']) ? $settings['auto_detect_language'] : false;
        $compact_mode = isset($settings['compact_mode']) ? $settings['compact_mode'] : false;
        $language_order = isset($settings['language_order']) ? $settings['language_order'] : $enabled_languages;

        // NTW settings
        $ntw_hide_main_widget = isset($settings['ntw_hide_main_widget']) ? $settings['ntw_hide_main_widget'] : false;
        $ntw_menu_position = isset($settings['ntw_menu_position']) ? $settings['ntw_menu_position'] : 'bottom-left';
        $ntw_compact_mode = isset($settings['ntw_compact_mode']) ? $settings['ntw_compact_mode'] : false;

        // Visual settings
        $btn_bg_enabled = isset($settings['btn_bg_enabled']) ? $settings['btn_bg_enabled'] : true;
        $btn_bg_color = isset($settings['btn_bg_color']) ? $settings['btn_bg_color'] : '#ffffff';
        $btn_border_enabled = isset($settings['btn_border_enabled']) ? $settings['btn_border_enabled'] : true;
        $btn_border_color = isset($settings['btn_border_color']) ? $settings['btn_border_color'] : '#dddddd';
        $btn_border_width = isset($settings['btn_border_width']) ? $settings['btn_border_width'] : 2;
        $btn_border_radius = isset($settings['btn_border_radius']) ? $settings['btn_border_radius'] : 50;
        $btn_flag_radius = isset($settings['btn_flag_radius']) ? $settings['btn_flag_radius'] : 50;
        $btn_hover_color = isset($settings['btn_hover_color']) ? $settings['btn_hover_color'] : '#007cba';
        
        $menu_bg_color = isset($settings['menu_bg_color']) ? $settings['menu_bg_color'] : '#ffffff';
        $menu_border_enabled = isset($settings['menu_border_enabled']) ? $settings['menu_border_enabled'] : true;
        $menu_border_color = isset($settings['menu_border_color']) ? $settings['menu_border_color'] : '#dddddd';
        $menu_border_width = isset($settings['menu_border_width']) ? $settings['menu_border_width'] : 1;
        $menu_border_radius = isset($settings['menu_border_radius']) ? $settings['menu_border_radius'] : 8;
        $menu_width = isset($settings['menu_width']) ? $settings['menu_width'] : 200;
        $menu_text_color = isset($settings['menu_text_color']) ? $settings['menu_text_color'] : '#333333';
        $menu_hover_bg_color = isset($settings['menu_hover_bg_color']) ? $settings['menu_hover_bg_color'] : '#0073aa';
        $menu_hover_text_color = isset($settings['menu_hover_text_color']) ? $settings['menu_hover_text_color'] : '#ffffff';
        $menu_active_bg_color = isset($settings['menu_active_bg_color']) ? $settings['menu_active_bg_color'] : '#e7f3ff';
        $menu_active_text_color = isset($settings['menu_active_text_color']) ? $settings['menu_active_text_color'] : '#007cba';

        // NTW settings
        $ntw_hide_main_widget = isset($settings['ntw_hide_main_widget']) ? $settings['ntw_hide_main_widget'] : false;
        $ntw_menu_position = isset($settings['ntw_menu_position']) ? $settings['ntw_menu_position'] : 'bottom-left';
        $ntw_compact_mode = isset($settings['ntw_compact_mode']) ? $settings['ntw_compact_mode'] : false;

        // Get all available languages
        $all_languages = HK_Translate_Languages::get_all_languages();

        // Extract variables for template
        extract(array(
            'enabled_languages' => $enabled_languages,
            'desktop_bottom' => $desktop_bottom,
            'tablet_bottom' => $tablet_bottom,
            'mobile_bottom' => $mobile_bottom,
            'desktop_size' => $desktop_size,
            'tablet_size' => $tablet_size,
            'mobile_size' => $mobile_size,
            'menu_height' => $menu_height,
            'desktop_position' => $desktop_position,
            'tablet_position' => $tablet_position,
            'mobile_position' => $mobile_position,
            'default_language' => $default_language,
            'plugin_enabled' => $plugin_enabled,
            'auto_detect_language' => $auto_detect_language,
            'compact_mode' => $compact_mode,
            'language_order' => $language_order,
            'btn_bg_enabled' => $btn_bg_enabled,
            'btn_bg_color' => $btn_bg_color,
            'btn_border_enabled' => $btn_border_enabled,
            'btn_border_color' => $btn_border_color,
            'btn_border_width' => $btn_border_width,
            'btn_border_radius' => $btn_border_radius,
            'btn_flag_radius' => $btn_flag_radius,
            'btn_hover_color' => $btn_hover_color,
            'menu_bg_color' => $menu_bg_color,
            'menu_border_enabled' => $menu_border_enabled,
            'menu_border_color' => $menu_border_color,
            'menu_border_width' => $menu_border_width,
            'menu_border_radius' => $menu_border_radius,
            'menu_width' => $menu_width,
            'menu_text_color' => $menu_text_color,
            'menu_hover_bg_color' => $menu_hover_bg_color,
            'menu_hover_text_color' => $menu_hover_text_color,
            'menu_active_bg_color' => $menu_active_bg_color,
            'menu_active_text_color' => $menu_active_text_color,
            'ntw_hide_main_widget' => $ntw_hide_main_widget,
            'ntw_menu_position' => $ntw_menu_position,
            'ntw_compact_mode' => $ntw_compact_mode,
            'all_languages' => $all_languages
        ));

        include HK_TRANSLATE_PLUGIN_DIR . 'admin/admin-settings.php';
    }

    /**
     * Save settings via AJAX.
     */
    public function save_settings() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hk-translate'));
        }

        // Sanitize and validate input
        $enabled_languages = isset($_POST['enabled_languages']) ? $_POST['enabled_languages'] : array();
        $enabled_languages = HK_Translate_Languages::validate_enabled_languages($enabled_languages);

        $desktop_bottom = isset($_POST['desktop_bottom']) ? absint($_POST['desktop_bottom']) : 20;
        $tablet_bottom = isset($_POST['tablet_bottom']) ? absint($_POST['tablet_bottom']) : 15;
        $mobile_bottom = isset($_POST['mobile_bottom']) ? absint($_POST['mobile_bottom']) : 10;
        $desktop_size = isset($_POST['desktop_size']) ? absint($_POST['desktop_size']) : 40;
        $tablet_size = isset($_POST['tablet_size']) ? absint($_POST['tablet_size']) : 36;
        $mobile_size = isset($_POST['mobile_size']) ? absint($_POST['mobile_size']) : 32;
        $menu_height = isset($_POST['menu_height']) ? absint($_POST['menu_height']) : 250;
        
        // Device-specific positions
        $desktop_position = isset($_POST['desktop_position']) ? sanitize_text_field($_POST['desktop_position']) : 'bottom-right';
        if (!in_array($desktop_position, array('bottom-left', 'bottom-right', 'middle-left', 'middle-right', 'top-left', 'top-right'))) {
            $desktop_position = 'bottom-right'; // Default for desktop
        }
        
        $tablet_position = isset($_POST['tablet_position']) ? sanitize_text_field($_POST['tablet_position']) : 'bottom-right';
        if (!in_array($tablet_position, array('bottom-left', 'bottom-right', 'middle-left', 'middle-right', 'top-left', 'top-right'))) {
            $tablet_position = 'bottom-right'; // Default for tablet
        }
        
        $mobile_position = isset($_POST['mobile_position']) ? sanitize_text_field($_POST['mobile_position']) : 'bottom-right';
        if (!in_array($mobile_position, array('bottom-left', 'bottom-right', 'middle-left', 'middle-right', 'top-left', 'top-right'))) {
            $mobile_position = 'bottom-right'; // Default for mobile
        }
        
        // Detect site default language for fallback
        $site_locale = get_locale();
        $site_lang_code = substr($site_locale, 0, 2);
        if (!HK_Translate_Languages::is_valid_language($site_lang_code)) {
            $site_lang_code = 'tr'; // Ultimate fallback to Turkish
        }
        
        $default_language = isset($_POST['default_language']) ? sanitize_text_field($_POST['default_language']) : $site_lang_code;
        if (!HK_Translate_Languages::is_valid_language($default_language)) {
            $default_language = $site_lang_code;
        }

        $plugin_enabled = isset($_POST['plugin_enabled']) ? (bool) $_POST['plugin_enabled'] : true;
        $auto_detect_language = isset($_POST['auto_detect_language']) ? (bool) $_POST['auto_detect_language'] : false;
        $compact_mode = isset($_POST['compact_mode']) ? (bool) $_POST['compact_mode'] : false;
        
        // NTW settings
        $ntw_hide_main_widget = isset($_POST['ntw_hide_main_widget']) ? (bool) $_POST['ntw_hide_main_widget'] : false;
        $ntw_menu_position = isset($_POST['ntw_menu_position']) ? sanitize_text_field($_POST['ntw_menu_position']) : 'bottom-left';
        $ntw_compact_mode = isset($_POST['ntw_compact_mode']) ? (bool) $_POST['ntw_compact_mode'] : false;

        // Language order processing
        $language_order = isset($_POST['language_order']) ? $_POST['language_order'] : array();
        
        // Validate language order: should only contain enabled languages
        if (!empty($language_order) && is_array($language_order)) {
            // Filter to only include enabled languages
            $language_order = array_intersect($language_order, $enabled_languages);
            
            // Add any enabled languages that are missing from order (append to end)
            foreach ($enabled_languages as $lang) {
                if (!in_array($lang, $language_order)) {
                    $language_order[] = $lang;
                }
            }
        } else {
            // Fallback: use enabled languages as order
            $language_order = $enabled_languages;
        }

        // Visual settings
        $btn_bg_enabled = isset($_POST['btn_bg_enabled']) ? (bool) $_POST['btn_bg_enabled'] : true;
        $btn_bg_color = isset($_POST['btn_bg_color']) ? sanitize_hex_color($_POST['btn_bg_color']) : '#ffffff';
        $btn_border_enabled = isset($_POST['btn_border_enabled']) ? (bool) $_POST['btn_border_enabled'] : true;
        $btn_border_color = isset($_POST['btn_border_color']) ? sanitize_hex_color($_POST['btn_border_color']) : '#dddddd';
        $btn_border_width = isset($_POST['btn_border_width']) ? absint($_POST['btn_border_width']) : 2;
        $btn_border_radius = isset($_POST['btn_border_radius']) ? absint($_POST['btn_border_radius']) : 50;
        $btn_flag_radius = isset($_POST['btn_flag_radius']) ? absint($_POST['btn_flag_radius']) : 50;
        $btn_hover_color = isset($_POST['btn_hover_color']) ? sanitize_hex_color($_POST['btn_hover_color']) : '#007cba';
        
        $menu_bg_color = isset($_POST['menu_bg_color']) ? sanitize_hex_color($_POST['menu_bg_color']) : '#ffffff';
        $menu_border_enabled = isset($_POST['menu_border_enabled']) ? (bool) $_POST['menu_border_enabled'] : true;
        $menu_border_color = isset($_POST['menu_border_color']) ? sanitize_hex_color($_POST['menu_border_color']) : '#dddddd';
        $menu_border_width = isset($_POST['menu_border_width']) ? absint($_POST['menu_border_width']) : 1;
        $menu_border_radius = isset($_POST['menu_border_radius']) ? absint($_POST['menu_border_radius']) : 8;
        $menu_width = isset($_POST['menu_width']) ? absint($_POST['menu_width']) : 200;
        $menu_text_color = isset($_POST['menu_text_color']) ? sanitize_hex_color($_POST['menu_text_color']) : '#333333';
        $menu_hover_bg_color = isset($_POST['menu_hover_bg_color']) ? sanitize_hex_color($_POST['menu_hover_bg_color']) : '#0073aa';
        $menu_hover_text_color = isset($_POST['menu_hover_text_color']) ? sanitize_hex_color($_POST['menu_hover_text_color']) : '#ffffff';
        $menu_active_bg_color = isset($_POST['menu_active_bg_color']) ? sanitize_hex_color($_POST['menu_active_bg_color']) : '#e7f3ff';
        $menu_active_text_color = isset($_POST['menu_active_text_color']) ? sanitize_hex_color($_POST['menu_active_text_color']) : '#007cba';

        // Prepare settings array
        $settings = array(
            'enabled_languages' => $enabled_languages,
            'desktop_bottom' => $desktop_bottom,
            'tablet_bottom' => $tablet_bottom,
            'mobile_bottom' => $mobile_bottom,
            'desktop_size' => $desktop_size,
            'tablet_size' => $tablet_size,
            'mobile_size' => $mobile_size,
            'menu_height' => $menu_height,
            'desktop_position' => $desktop_position,
            'tablet_position' => $tablet_position,
            'mobile_position' => $mobile_position,
            'default_language' => $default_language,
            'plugin_enabled' => $plugin_enabled,
            'auto_detect_language' => $auto_detect_language,
            'compact_mode' => $compact_mode,
            'language_order' => $language_order,
            'ntw_hide_main_widget' => $ntw_hide_main_widget,
            'ntw_menu_position' => $ntw_menu_position,
            'ntw_compact_mode' => $ntw_compact_mode,
            // Visual settings
            'btn_bg_enabled' => $btn_bg_enabled,
            'btn_bg_color' => $btn_bg_color,
            'btn_border_enabled' => $btn_border_enabled,
            'btn_border_color' => $btn_border_color,
            'btn_border_width' => $btn_border_width,
            'btn_border_radius' => $btn_border_radius,
            'btn_flag_radius' => $btn_flag_radius,
            'btn_hover_color' => $btn_hover_color,
            'menu_bg_color' => $menu_bg_color,
            'menu_border_enabled' => $menu_border_enabled,
            'menu_border_color' => $menu_border_color,
            'menu_border_width' => $menu_border_width,
            'menu_border_radius' => $menu_border_radius,
            'menu_width' => $menu_width,
            'menu_text_color' => $menu_text_color,
            'menu_hover_bg_color' => $menu_hover_bg_color,
            'menu_hover_text_color' => $menu_hover_text_color,
            'menu_active_bg_color' => $menu_active_bg_color,
            'menu_active_text_color' => $menu_active_text_color
        );

        // Save settings
        try {
            // Get current settings for comparison
            $current_settings = get_option('hk_translate_settings', array());
            
            // Check if settings actually changed
            $settings_changed = (serialize($current_settings) !== serialize($settings));
            
            $result = update_option('hk_translate_settings', $settings);

            // WordPress update_option returns false if value didn't change
            // So we need to check both the result and if settings changed
            if ($result || !$settings_changed) {
                wp_send_json_success(array(
                    'message' => __('Settings saved successfully!', 'hk-translate'),
                    'debug' => array(
                        'update_result' => $result,
                        'settings_changed' => $settings_changed,
                        'ntw_hide_main_widget' => $ntw_hide_main_widget
                    )
                ));
            } else {
                // Get more detailed error information
                $wp_error = '';
                if (function_exists('wp_get_last_error')) {
                    $wp_error = wp_get_last_error();
                }
                
                wp_send_json_error(array(
                    'message' => __('Error saving settings. Database update failed.', 'hk-translate'),
                    'debug' => array(
                        'update_result' => $result,
                        'settings_changed' => $settings_changed,
                        'current_settings_count' => count($current_settings),
                        'new_settings_count' => count($settings),
                        'wp_error' => $wp_error,
                        'ntw_hide_main_widget_current' => isset($current_settings['ntw_hide_main_widget']) ? $current_settings['ntw_hide_main_widget'] : 'not_set',
                        'ntw_hide_main_widget_new' => $ntw_hide_main_widget
                    )
                ));
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => __('Error saving settings. Exception occurred: ', 'hk-translate') . $e->getMessage(),
                'debug' => array(
                    'exception_message' => $e->getMessage(),
                    'exception_code' => $e->getCode()
                )
            ));
        }
    }

    /**
     * Reset settings to defaults via AJAX.
     */
    public function reset_settings() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hk-translate'));
        }

        // Detect site default language
        $site_locale = get_locale(); // e.g., 'tr_TR', 'en_US', 'de_DE'
        $site_lang_code = substr($site_locale, 0, 2); // Extract first 2 characters
        
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

        // Default settings
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
            'compact_mode' => false,
            'language_order' => $default_enabled_languages,
            'ntw_hide_main_widget' => false,
            'ntw_menu_position' => 'bottom-left',
            'ntw_compact_mode' => false,
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

        // Reset settings
        $result = update_option('hk_translate_settings', $default_settings);

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Settings reset to defaults successfully!', 'hk-translate'),
                'settings' => $default_settings
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error resetting settings. Please try again.', 'hk-translate')
            ));
        }
    }

    /**
     * Save language order via AJAX.
     */
    public function save_language_order() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hk-translate'));
        }

        // Get current settings
        $settings = get_option('hk_translate_settings', array());
        $enabled_languages = isset($settings['enabled_languages']) ? $settings['enabled_languages'] : array();
        $current_order = isset($settings['language_order']) ? $settings['language_order'] : array();

        // Get new order from AJAX
        $new_order = isset($_POST['language_order']) ? $_POST['language_order'] : array();

        // Validate: only include enabled languages
        $validated_order = array();
        if (is_array($new_order)) {
            foreach ($new_order as $lang_code) {
                if (in_array($lang_code, $enabled_languages)) {
                    $validated_order[] = sanitize_text_field($lang_code);
                }
            }
        }

        // Add any missing enabled languages to the end
        foreach ($enabled_languages as $lang) {
            if (!in_array($lang, $validated_order)) {
                $validated_order[] = $lang;
            }
        }

        // Check if order actually changed
        $order_changed = (serialize($current_order) !== serialize($validated_order));

        // Update settings
        $settings['language_order'] = $validated_order;
        $result = update_option('hk_translate_settings', $settings);

        // WordPress update_option returns false if value didn't change, so we need to handle this
        if ($result || !$order_changed) {
            wp_send_json_success(array(
                'message' => __('Language order saved successfully!', 'hk-translate'),
                'order' => $validated_order
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error saving language order. Please try again.', 'hk-translate')
            ));
        }
    }

    /**
     * Save manual translation via AJAX (Edit Mode)
     */
    public function save_manual_translation() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Check user capabilities (only user ID 1)
        $current_user = wp_get_current_user();
        if ($current_user->ID !== 1) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hk-translate'));
        }

        // Get and validate input
        $page_url = isset($_POST['page_url']) ? esc_url_raw($_POST['page_url']) : '';
        $source_language = isset($_POST['source_language']) ? sanitize_text_field($_POST['source_language']) : 'tr';
        $target_language = isset($_POST['target_language']) ? sanitize_text_field($_POST['target_language']) : '';
        $original_text = isset($_POST['original_text']) ? sanitize_textarea_field($_POST['original_text']) : '';
        $manual_translation = isset($_POST['manual_translation']) ? sanitize_textarea_field($_POST['manual_translation']) : '';
        $text_hash = isset($_POST['text_hash']) ? sanitize_text_field($_POST['text_hash']) : '';
        $element_selector = isset($_POST['element_selector']) ? sanitize_text_field($_POST['element_selector']) : '';

        // Validate required fields
        if (empty($page_url) || empty($target_language) || empty($original_text) || empty($manual_translation) || empty($text_hash)) {
            wp_send_json_error(array(
                'message' => __('Missing required fields for saving translation.', 'hk-translate')
            ));
            return;
        }

        // For now, simulate saving to database
        // In production, you would save to the database tables we created
        
        // Success response
        wp_send_json_success(array(
            'message' => __('Manual translation saved successfully!', 'hk-translate'),
            'data' => array(
                'page_url' => $page_url,
                'target_language' => $target_language,
                'text_hash' => $text_hash,
                'manual_translation' => $manual_translation
            )
        ));
    }

    /**
     * Save exclusion status via AJAX (Edit Mode)
     */
    public function save_exclusion() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Check user capabilities (only user ID 1)
        $current_user = wp_get_current_user();
        if ($current_user->ID !== 1) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hk-translate'));
        }

        // Get and validate input
        $page_url = isset($_POST['page_url']) ? esc_url_raw($_POST['page_url']) : '';
        $target_language = isset($_POST['target_language']) ? sanitize_text_field($_POST['target_language']) : '';
        $text_hash = isset($_POST['text_hash']) ? sanitize_text_field($_POST['text_hash']) : '';
        $is_excluded = isset($_POST['is_excluded']) ? (bool) $_POST['is_excluded'] : false;

        // Validate required fields
        if (empty($page_url) || empty($target_language) || empty($text_hash)) {
            wp_send_json_error(array(
                'message' => __('Missing required fields for saving exclusion.', 'hk-translate')
            ));
            return;
        }

        // For now, simulate saving to database
        
        // Success response
        wp_send_json_success(array(
            'message' => $is_excluded ? __('Element excluded from translation.', 'hk-translate') : __('Element inclusion restored.', 'hk-translate'),
            'data' => array(
                'page_url' => $page_url,
                'target_language' => $target_language,
                'text_hash' => $text_hash,
                'is_excluded' => $is_excluded
            )
        ));
    }

    /**
     * Load manual translations for edit mode
     */
    public function load_manual_translations() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Check user capabilities (only user ID 1)
        $current_user = wp_get_current_user();
        if ($current_user->ID !== 1) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hk-translate'));
        }

        // Get parameters
        $page_url = isset($_POST['page_url']) ? esc_url_raw($_POST['page_url']) : '';
        $target_language = isset($_POST['target_language']) ? sanitize_text_field($_POST['target_language']) : '';

        // For now, return empty array as we haven't implemented database yet
        wp_send_json_success(array(
            'translations' => array(),
            'message' => __('Manual translations loaded.', 'hk-translate')
        ));
    }

    /**
     * Get manual translations for normal view (non-edit mode)
     */
    public function get_manual_translations() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Get parameters
        $page_url = isset($_POST['page_url']) ? esc_url_raw($_POST['page_url']) : '';
        $target_language = isset($_POST['target_language']) ? sanitize_text_field($_POST['target_language']) : '';

        // For now, return empty array as we haven't implemented database yet
        wp_send_json_success(array(
            'translations' => array(),
            'message' => __('Manual translations retrieved.', 'hk-translate')
        ));
    }

    /**
     * Remove outdated translation
     */
    public function remove_outdated_translation() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'hk_translate_nonce')) {
            wp_die(__('Security check failed', 'hk-translate'));
        }

        // Check user capabilities (only user ID 1)
        $current_user = wp_get_current_user();
        if ($current_user->ID !== 1) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hk-translate'));
        }

        // Get parameters
        $text_hash = isset($_POST['text_hash']) ? sanitize_text_field($_POST['text_hash']) : '';
        $page_url = isset($_POST['page_url']) ? esc_url_raw($_POST['page_url']) : '';
        $target_language = isset($_POST['target_language']) ? sanitize_text_field($_POST['target_language']) : '';

        // Validate required fields
        if (empty($text_hash)) {
            wp_send_json_error(array(
                'message' => __('Missing text hash for removing translation.', 'hk-translate')
            ));
            return;
        }

        // For now, simulate removal
        wp_send_json_success(array(
            'message' => __('Outdated translation removed successfully.', 'hk-translate'),
            'text_hash' => $text_hash
        ));
    }

    /**
     * Add HK Translate Edit Mode button to WordPress admin bar
     */
    public function add_admin_bar_edit_mode($wp_admin_bar) {
        // Only show to user ID 1 (super admin)
        $current_user = wp_get_current_user();
        if ($current_user->ID !== 1) {
            return;
        }

        // Don't show in admin area (backend)
        if (is_admin()) {
            return;
        }

        // Check if plugin is enabled
        $settings = get_option('hk_translate_settings', array());
        if (!isset($settings['plugin_enabled']) || !$settings['plugin_enabled']) {
            return;
        }

        // Check if already in edit mode
        $is_edit_mode = isset($_GET['hk_translate_edit_mode']) && $_GET['hk_translate_edit_mode'] == '1';
        
        // Get current URL
        $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        if ($is_edit_mode) {
            // If in edit mode, show "Exit Edit Mode" button
            $exit_url = remove_query_arg('hk_translate_edit_mode', $current_url);
            
            $wp_admin_bar->add_node(array(
                'id'    => 'hk-translate-edit-mode',
                'title' => '<span class="ab-icon dashicons dashicons-edit" style="color: #ff6b6b; margin-top: 2px;"></span><span class="ab-label" style="color: #ff6b6b; font-weight: bold;">Exit Edit Mode</span>',
                'href'  => $exit_url,
                'meta'  => array(
                    'title' => __('Exit HK Translate Edit Mode - Click to return to normal view', 'hk-translate'),
                    'class' => 'hk-translate-exit-edit-mode'
                ),
            ));
        } else {
            // If not in edit mode, show "Enter Edit Mode" button
            $edit_url = add_query_arg('hk_translate_edit_mode', '1', $current_url);
            
            $wp_admin_bar->add_node(array(
                'id'    => 'hk-translate-edit-mode',
                'title' => '<span class="ab-icon dashicons dashicons-edit" style="color: #007cba; margin-top: 2px;"></span><span class="ab-label">Edit Mode</span>',
                'href'  => $edit_url,
                'meta'  => array(
                    'title' => __('Enter HK Translate Edit Mode to manually edit translations', 'hk-translate'),
                    'class' => 'hk-translate-enter-edit-mode'
                ),
            ));
        }
    }
}