<?php

/**
 * The public-facing functionality of the plugin.
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for public functionality.
 */
class HK_Translate_Public {

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
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        // Only enqueue if plugin is enabled (or in preview mode)
        $settings = get_option('hk_translate_settings', array());
        $is_preview = isset($_GET['hk_preview_mode']) && $_GET['hk_preview_mode'] == '1';
        
        if (!$is_preview && (!isset($settings['plugin_enabled']) || !$settings['plugin_enabled'])) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            HK_TRANSLATE_PLUGIN_URL . 'public/css/hk-translate-style.css',
            array(),
            $this->version,
            'all'
        );

        // Always enqueue NTW styles for menu widgets
        wp_enqueue_style(
            $this->plugin_name . '-ntw',
            HK_TRANSLATE_PLUGIN_URL . 'public/css/hk-translate-ntw-style.css',
            array(),
            $this->version,
            'all'
        );

        // Add preview mode CSS if needed
        if ($is_preview) {
            wp_add_inline_style($this->plugin_name, '
                body { overflow: hidden !important; }
                html, body { margin: 0 !important; padding: 0 !important; }
                .hk-translate-dropdown { z-index: 999999 !important; }
            ');
        }

        // Add dynamic CSS for positioning
        $this->add_dynamic_css();
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        // Only enqueue if plugin is enabled (or in preview mode)
        $settings = get_option('hk_translate_settings', array());
        $is_preview = isset($_GET['hk_preview_mode']) && $_GET['hk_preview_mode'] == '1';
        
        if (!$is_preview && (!isset($settings['plugin_enabled']) || !$settings['plugin_enabled'])) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name,
            HK_TRANSLATE_PLUGIN_URL . 'public/js/hk-translate-script.js',
            array('jquery'),
            $this->version,
            true
        );

        
        // Enqueue NTW script for menu widgets - ALWAYS load to fix conflicts
        wp_enqueue_script(
            $this->plugin_name . '-ntw',
            HK_TRANSLATE_PLUGIN_URL . 'public/js/hk-translate-ntw-script.js',
            array($this->plugin_name), // Depends on main script
            $this->version,
            true
        );

        // Enqueue edit mode script if edit mode is active
        $is_edit_mode = isset($_GET['hk_translate_edit_mode']) && $_GET['hk_translate_edit_mode'] == '1';
        $current_user = wp_get_current_user();
        if ($is_edit_mode && $current_user->ID === 1) {
            wp_enqueue_script(
                $this->plugin_name . '-edit-mode',
                HK_TRANSLATE_PLUGIN_URL . 'public/js/hk-translate-edit-mode.js',
                array('jquery'),
                $this->version,
                true
            );

            // Localize edit mode script with settings
            wp_localize_script(
                $this->plugin_name . '-edit-mode',
                'hk_translate_settings',
                array(
                    'enabled_languages' => HK_Translate_Languages::get_enabled_languages(),
                    'default_language' => isset($settings['default_language']) ? $settings['default_language'] : 'tr',
                    'auto_detect_language' => isset($settings['auto_detect_language']) ? $settings['auto_detect_language'] : false,
                    'compact_mode' => isset($settings['compact_mode']) ? $settings['compact_mode'] : false,
                    'desktop_position' => isset($settings['desktop_position']) ? $settings['desktop_position'] : 'bottom-right',
                    'tablet_position' => isset($settings['tablet_position']) ? $settings['tablet_position'] : 'bottom-right',
                    'mobile_position' => isset($settings['mobile_position']) ? $settings['mobile_position'] : 'bottom-right',
                    'plugin_url' => HK_TRANSLATE_PLUGIN_URL,
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'is_preview' => $is_preview,
                    'user_can_edit' => true, // Always true for edit mode since we already checked user ID
                    'nonce' => wp_create_nonce('hk_translate_nonce')
                )
            );
        }

        // Add preview mode communication script
        if ($is_preview) {
            wp_add_inline_script($this->plugin_name, '
                // Listen for messages from admin iframe parent
                window.addEventListener("message", function(event) {
                    if (event.data.type === "hk_preview_settings_update") {
                        // Update settings and re-render widget
                        if (window.updateHKTranslateSettings) {
                            // Use device-specific positions instead of legacy position
                            window.updateHKTranslateSettings(event.data.settings, {
                                desktop_position: event.data.desktop_position,
                                tablet_position: event.data.tablet_position,
                                mobile_position: event.data.mobile_position
                            });
                        }
                    }
                });
            ');
        }

        // Detect site default language for fallback
        $site_locale = get_locale();
        $site_lang_code = substr($site_locale, 0, 2);
        if (!HK_Translate_Languages::is_valid_language($site_lang_code)) {
            $site_lang_code = 'tr'; // Ultimate fallback to Turkish
        }

        // Get current user for edit permission check
        $current_user = wp_get_current_user();

        // Localize script with settings
        wp_localize_script(
            $this->plugin_name,
            'hk_translate_settings',
            array(
                'enabled_languages' => HK_Translate_Languages::get_enabled_languages(),
                'default_language' => isset($settings['default_language']) ? $settings['default_language'] : $site_lang_code,
                'auto_detect_language' => isset($settings['auto_detect_language']) ? $settings['auto_detect_language'] : false,
                'compact_mode' => isset($settings['compact_mode']) ? $settings['compact_mode'] : false,
                'desktop_position' => isset($settings['desktop_position']) ? $settings['desktop_position'] : 'bottom-right',
                'tablet_position' => isset($settings['tablet_position']) ? $settings['tablet_position'] : 'bottom-right',
                'mobile_position' => isset($settings['mobile_position']) ? $settings['mobile_position'] : 'bottom-right',
                // 'position' => removed - using device-specific positioning only
                'plugin_url' => HK_TRANSLATE_PLUGIN_URL,
                'ajax_url' => admin_url('admin-ajax.php'),
                'is_preview' => $is_preview,
                'user_can_edit' => ($current_user->ID === 1),
                'nonce' => wp_create_nonce('hk_translate_nonce')
            )
        );
    }

    /**
     * Add dynamic CSS for positioning and visual settings.
     */
    private function add_dynamic_css() {
        $settings = get_option('hk_translate_settings', array());
        
        // Position and size settings
        $desktop_bottom = isset($settings['desktop_bottom']) ? $settings['desktop_bottom'] : '20';
        $tablet_bottom = isset($settings['tablet_bottom']) ? $settings['tablet_bottom'] : '15';
        $mobile_bottom = isset($settings['mobile_bottom']) ? $settings['mobile_bottom'] : '10';
        $desktop_size = isset($settings['desktop_size']) ? $settings['desktop_size'] : '40';
        $tablet_size = isset($settings['tablet_size']) ? $settings['tablet_size'] : '36';
        $mobile_size = isset($settings['mobile_size']) ? $settings['mobile_size'] : '32';
        $menu_height = isset($settings['menu_height']) ? $settings['menu_height'] : '250';

        // Device-specific positions (each has its own default)
        // Legacy 'position' field removed - using device-specific only
        $desktop_position = isset($settings['desktop_position']) ? $settings['desktop_position'] : 'bottom-right';
        $tablet_position = isset($settings['tablet_position']) ? $settings['tablet_position'] : 'bottom-right';
        $mobile_position = isset($settings['mobile_position']) ? $settings['mobile_position'] : 'bottom-right';

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
        
        // Active menu colors (missing variables)
        $menu_active_bg_color = isset($settings['menu_active_bg_color']) ? $settings['menu_active_bg_color'] : '#e7f3ff';
        $menu_active_text_color = isset($settings['menu_active_text_color']) ? $settings['menu_active_text_color'] : '#007cba';

        // Build button styles
        $btn_bg = $btn_bg_enabled ? $btn_bg_color : 'transparent';
        $btn_border = $btn_border_enabled ? "{$btn_border_width}px solid {$btn_border_color}" : 'none';
        $btn_radius = $btn_border_radius . 'px';

        // Build menu styles
        $menu_border = $menu_border_enabled ? "{$menu_border_width}px solid {$menu_border_color}" : 'none';

        $custom_css = "
        :root {
            --hk-translate-desktop-bottom: {$desktop_bottom}px;
            --hk-translate-tablet-bottom: {$tablet_bottom}px;
            --hk-translate-mobile-bottom: {$mobile_bottom}px;
            --hk-translate-desktop-size: {$desktop_size}px;
            --hk-translate-tablet-size: {$tablet_size}px;
            --hk-translate-mobile-size: {$mobile_size}px;
            --hk-translate-menu-height: {$menu_height}px;
            --hk-translate-btn-bg: {$btn_bg};
            --hk-translate-btn-border: {$btn_border};
            --hk-translate-btn-radius: {$btn_radius};
            --hk-translate-btn-flag-radius: {$btn_flag_radius}px;
            --hk-translate-btn-hover-color: {$btn_hover_color};
            --hk-translate-menu-bg: {$menu_bg_color};
            --hk-translate-menu-border: {$menu_border};
            --hk-translate-menu-radius: {$menu_border_radius}px;
            --hk-translate-menu-width: {$menu_width}px;
            --hk-translate-menu-text: {$menu_text_color};
            --hk-translate-menu-hover-bg: {$menu_hover_bg_color};
            --hk-translate-menu-hover-text: {$menu_hover_text_color};
            --hk-translate-menu-active-bg: {$menu_active_bg_color};
            --hk-translate-menu-active-text: {$menu_active_text_color};
        }
        
        /* Device-specific positioning - TÜM POZİSYONLAR İÇİN CSS */
        @media (min-width: 1025px) {
            .hk-translate-dropdown { position: fixed !important; }
            .hk-translate-dropdown.desktop-bottom-left { {$this->get_position_css('bottom-left', $desktop_bottom)} }
            .hk-translate-dropdown.desktop-bottom-right { {$this->get_position_css('bottom-right', $desktop_bottom)} }
            .hk-translate-dropdown.desktop-middle-left { {$this->get_position_css('middle-left', $desktop_bottom)} }
            .hk-translate-dropdown.desktop-middle-right { {$this->get_position_css('middle-right', $desktop_bottom)} }
            .hk-translate-dropdown.desktop-top-left { {$this->get_position_css('top-left', $desktop_bottom)} }
            .hk-translate-dropdown.desktop-top-right { {$this->get_position_css('top-right', $desktop_bottom)} }
            
            /* Menu positions for desktop */
            .hk-translate-dropdown.desktop-bottom-left .hk-translate-menu { bottom: calc(var(--hk-translate-desktop-size, 40px) + 10px); left: 0; }
            .hk-translate-dropdown.desktop-bottom-right .hk-translate-menu { bottom: calc(var(--hk-translate-desktop-size, 40px) + 10px); right: 0; }
            .hk-translate-dropdown.desktop-middle-left .hk-translate-menu { top: 50%; left: calc(var(--hk-translate-desktop-size, 40px) + 10px); transform: translateY(-50%); }
            .hk-translate-dropdown.desktop-middle-right .hk-translate-menu { top: 50%; right: calc(var(--hk-translate-desktop-size, 40px) + 10px); transform: translateY(-50%); }
            .hk-translate-dropdown.desktop-top-left .hk-translate-menu { top: calc(var(--hk-translate-desktop-size, 40px) + 10px); left: 0; }
            .hk-translate-dropdown.desktop-top-right .hk-translate-menu { top: calc(var(--hk-translate-desktop-size, 40px) + 10px); right: 0; }
            
            /* Menu açık durumda transform koru */
            .hk-translate-dropdown.desktop-middle-left.open .hk-translate-menu, 
            .hk-translate-dropdown.desktop-middle-right.open .hk-translate-menu { transform: translateY(-50%) !important; }
        }
        
        @media (min-width: 768px) and (max-width: 1024px) {
            .hk-translate-dropdown { position: fixed !important; }
            .hk-translate-dropdown.tablet-bottom-left { {$this->get_position_css('bottom-left', $tablet_bottom)} }
            .hk-translate-dropdown.tablet-bottom-right { {$this->get_position_css('bottom-right', $tablet_bottom)} }
            .hk-translate-dropdown.tablet-middle-left { {$this->get_position_css('middle-left', $tablet_bottom)} }
            .hk-translate-dropdown.tablet-middle-right { {$this->get_position_css('middle-right', $tablet_bottom)} }
            .hk-translate-dropdown.tablet-top-left { {$this->get_position_css('top-left', $tablet_bottom)} }
            .hk-translate-dropdown.tablet-top-right { {$this->get_position_css('top-right', $tablet_bottom)} }
            
            /* Menu positions for tablet */
            .hk-translate-dropdown.tablet-bottom-left .hk-translate-menu { bottom: calc(var(--hk-translate-tablet-size, 36px) + 9px); left: 0; }
            .hk-translate-dropdown.tablet-bottom-right .hk-translate-menu { bottom: calc(var(--hk-translate-tablet-size, 36px) + 9px); right: 0; }
            .hk-translate-dropdown.tablet-middle-left .hk-translate-menu { top: 50%; left: calc(var(--hk-translate-tablet-size, 36px) + 9px); transform: translateY(-50%); }
            .hk-translate-dropdown.tablet-middle-right .hk-translate-menu { top: 50%; right: calc(var(--hk-translate-tablet-size, 36px) + 9px); transform: translateY(-50%); }
            .hk-translate-dropdown.tablet-top-left .hk-translate-menu { top: calc(var(--hk-translate-tablet-size, 36px) + 9px); left: 0; }
            .hk-translate-dropdown.tablet-top-right .hk-translate-menu { top: calc(var(--hk-translate-tablet-size, 36px) + 9px); right: 0; }
            
            /* Menu açık durumda transform koru */
            .hk-translate-dropdown.tablet-middle-left.open .hk-translate-menu,
            .hk-translate-dropdown.tablet-middle-right.open .hk-translate-menu { transform: translateY(-50%) !important; }
        }
        
        @media (max-width: 767px) {
            .hk-translate-dropdown { position: fixed !important; }
            .hk-translate-dropdown.mobile-bottom-left { {$this->get_position_css('bottom-left', $mobile_bottom)} }
            .hk-translate-dropdown.mobile-bottom-right { {$this->get_position_css('bottom-right', $mobile_bottom)} }
            .hk-translate-dropdown.mobile-middle-left { {$this->get_position_css('middle-left', $mobile_bottom)} }
            .hk-translate-dropdown.mobile-middle-right { {$this->get_position_css('middle-right', $mobile_bottom)} }
            .hk-translate-dropdown.mobile-top-left { {$this->get_position_css('top-left', $mobile_bottom)} }
            .hk-translate-dropdown.mobile-top-right { {$this->get_position_css('top-right', $mobile_bottom)} }
            
            /* Menu positions for mobile */
            .hk-translate-dropdown.mobile-bottom-left .hk-translate-menu { bottom: calc(var(--hk-translate-mobile-size, 32px) + 8px); left: 0; }
            .hk-translate-dropdown.mobile-bottom-right .hk-translate-menu { bottom: calc(var(--hk-translate-mobile-size, 32px) + 8px); right: 0; }
            .hk-translate-dropdown.mobile-middle-left .hk-translate-menu { top: 50%; left: calc(var(--hk-translate-mobile-size, 32px) + 8px); transform: translateY(-50%); }
            .hk-translate-dropdown.mobile-middle-right .hk-translate-menu { top: 50%; right: calc(var(--hk-translate-mobile-size, 32px) + 8px); transform: translateY(-50%); }
            .hk-translate-dropdown.mobile-top-left .hk-translate-menu { top: calc(var(--hk-translate-mobile-size, 32px) + 8px); left: 0; }
            .hk-translate-dropdown.mobile-top-right .hk-translate-menu { top: calc(var(--hk-translate-mobile-size, 32px) + 8px); right: 0; }
            
            /* Menu açık durumda transform koru */
            .hk-translate-dropdown.mobile-middle-left.open .hk-translate-menu,
            .hk-translate-dropdown.mobile-middle-right.open .hk-translate-menu { transform: translateY(-50%) !important; }
        }
        ";

        wp_add_inline_style($this->plugin_name, $custom_css);
    }

    /**
     * Get CSS for specific position
     */
    private function get_position_css($position, $distance) {
        switch ($position) {
            case 'bottom-left':
                return "bottom: {$distance}px; left: 20px; top: auto; right: auto;";
            case 'bottom-right':
                return "bottom: {$distance}px; right: 20px; top: auto;";
            case 'middle-left':
                return "top: 50%; left: 20px; bottom: auto; right: auto; transform: translateY(-50%);";
            case 'middle-right':
                return "top: 50%; right: 20px; bottom: auto; transform: translateY(-50%);";
            case 'top-left':
                return "top: {$distance}px; left: 20px; bottom: auto; right: auto;";
            case 'top-right':
                return "top: {$distance}px; right: 20px; bottom: auto;";
            default:
                return "bottom: {$distance}px; right: 20px; top: auto;";
        }
    }

    /**
     * Render the translation widget in the footer.
     */
    public function render_translate_widget() {
        // Only render if plugin is enabled (or in preview mode)
        $settings = get_option('hk_translate_settings', array());
        $is_preview = isset($_GET['hk_preview_mode']) && $_GET['hk_preview_mode'] == '1';
        
        if (!$is_preview && (!isset($settings['plugin_enabled']) || !$settings['plugin_enabled'])) {
            return;
        }

        // Check if main widget should be hidden when NTW is active
        $ntw_hide_main_widget = isset($settings['ntw_hide_main_widget']) ? $settings['ntw_hide_main_widget'] : false;
        
        // If NTW hide option is enabled, check if we're in a menu context
        if ($ntw_hide_main_widget && $this->is_ntw_menu_active()) {
            return; // Don't render main widget if NTW is active in menu
        }

        $enabled_languages = HK_Translate_Languages::get_enabled_languages();
        
        // Detect site default language for fallback
        $site_locale = get_locale();
        $site_lang_code = substr($site_locale, 0, 2);
        if (!HK_Translate_Languages::is_valid_language($site_lang_code)) {
            $site_lang_code = 'tr'; // Ultimate fallback to Turkish
        }
        
        $default_language = isset($settings['default_language']) ? $settings['default_language'] : $site_lang_code;
        $compact_mode = isset($settings['compact_mode']) ? $settings['compact_mode'] : false;
        // Legacy 'position' field removed - using device-specific positioning only

        // Device-specific positions (backward compatibility)
        $desktop_position = isset($settings['desktop_position']) ? $settings['desktop_position'] : 'bottom-right';
        $tablet_position = isset($settings['tablet_position']) ? $settings['tablet_position'] : 'bottom-right';
        $mobile_position = isset($settings['mobile_position']) ? $settings['mobile_position'] : 'bottom-right';
        
        // Build device classes - ALL DEVICES SEPARATELY + Compact Mode
        $device_classes = "desktop-{$desktop_position} tablet-{$tablet_position} mobile-{$mobile_position}";
        if ($compact_mode) {
            $device_classes .= " hk-translate-compact";
        }

        // Don't render if no languages are enabled
        if (empty($enabled_languages)) {
            return;
        }

        // Get language order from settings
        $language_order = isset($settings['language_order']) ? $settings['language_order'] : array_keys($enabled_languages);
        
        // Sort languages according to saved order
        $sorted_languages = array();
        
        // First add languages in the specified order
        foreach ($language_order as $lang_code) {
            if (isset($enabled_languages[$lang_code])) {
                $sorted_languages[$lang_code] = $enabled_languages[$lang_code];
                unset($enabled_languages[$lang_code]);
            }
        }
        
        // Add any remaining enabled languages that weren't in the order (fallback)
        $sorted_languages = array_merge($sorted_languages, $enabled_languages);

        ?>
        <div class="hk-translate-dropdown <?php echo esc_attr($device_classes); ?>" id="hkTranslateDropdown">
            <div class="hk-translate-btn" onclick="toggleHKTranslateDropdown()" title="<?php esc_attr_e('Select Language', 'hk-translate'); ?>">
                <img
                    src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($default_language)); ?>"
                    alt="<?php echo esc_attr(HK_Translate_Languages::get_language_name($default_language)); ?>"
                    id="hkCurrentFlag"
                />
            </div>

            <div class="hk-translate-menu">
                <?php foreach ($sorted_languages as $code => $language): ?>
                    <a
                        href="#"
                        class="hk-translate-item"
                        onclick="selectHKLanguage('<?php echo esc_attr($code); ?>', '<?php echo esc_attr(strtoupper($code)); ?>', '<?php echo esc_attr($language['name']); ?>', '<?php echo esc_attr($default_language); ?>')"
                        data-lang="<?php echo esc_attr($code === 'en' ? 'en' : $code); ?>"
                    >
                        <img src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($code)); ?>" alt="<?php echo esc_attr($language['name']); ?>" />
                        <span><?php echo esc_html($language['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Google Translate Element (Hidden) -->
        <div id="google_translate_element2" style="display: none"></div>
        <?php
    }

    /**
     * Check if NTW (Navigation Translate Widget) is active on current page
     */
    private function is_ntw_menu_active() {
        // Check if any active menus contain HK Translate items
        $menu_locations = get_nav_menu_locations();
        
        if (empty($menu_locations)) {
            return false;
        }
        
        foreach ($menu_locations as $location => $menu_id) {
            if ($menu_id) {
                $menu_items = wp_get_nav_menu_items($menu_id);
                if ($menu_items) {
                    foreach ($menu_items as $menu_item) {
                        // Check if this is an HK Translate menu item
                        if ($menu_item->type === 'hk_translate') {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }
}