<?php
/**
 * Admin settings page template
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- DEBUG SECTION -->
    <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
    <div class="notice notice-info">
        <h3>🔍 Debug Information</h3>
        <h4>Current Settings from Database:</h4>
        <pre style="background: #f9f9f9; padding: 10px; font-size: 12px; overflow: auto; max-height: 200px;">
        <?php 
        $debug_settings = get_option('hk_translate_settings', array());
        var_dump($debug_settings); 
        ?>
        </pre>
        
        <h4>Auto Detect Language Specific:</h4>
        <pre style="background: #f9f9f9; padding: 10px; font-size: 12px;">
        <?php 
        if (isset($debug_settings['auto_detect_language'])) {
            echo "✅ auto_detect_language exists: ";
            var_dump($debug_settings['auto_detect_language']);
            echo "\nType: " . gettype($debug_settings['auto_detect_language']);
        } else {
            echo "❌ auto_detect_language NOT SET in database";
            echo "\n\n🔧 Fix: Add missing setting to database";
            
            // Add missing auto_detect_language setting
            $debug_settings['auto_detect_language'] = false;
            $update_result = update_option('hk_translate_settings', $debug_settings);
            
            if ($update_result) {
                echo "\n✅ auto_detect_language added to database with default value (false)";
                echo "\n🔄 Refresh page to see the updated settings";
            } else {
                echo "\n❌ Failed to update database";
            }
        }
        ?>
        </pre>
        
        <h4>Template Variables:</h4>
        <pre style="background: #f9f9f9; padding: 10px; font-size: 12px;">
        <?php 
        echo "auto_detect_language variable: ";
        var_dump(isset($auto_detect_language) ? $auto_detect_language : 'NOT SET');
        ?>
        </pre>
        
        <p><strong>Debug Mode:</strong> URL'ye <code>&debug=1</code> ekleyerek bu bilgileri görüyorsun.</p>
    </div>
    <?php endif; ?>
    
    <div id="hk-translate-message" class="notice" style="display: none;">
        <p></p>
    </div>

    <form id="hk-translate-settings-form" method="post" action="">
        <?php wp_nonce_field('hk_translate_settings', 'hk_translate_nonce'); ?>
        
        <table class="form-table">
            <tbody>
                <!-- Plugin Enable/Disable -->
                <tr>
                    <th scope="row">
                        <label for="plugin_enabled"><?php _e('Enable Plugin', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <label class="hk-translate-switch">
                            <input type="checkbox" id="plugin_enabled" name="plugin_enabled" value="1" <?php checked($plugin_enabled, true); ?>>
                            <span class="hk-translate-slider"></span>
                        </label>
                        <p class="description"><?php _e('Enable or disable the translation widget on your site.', 'hk-translate'); ?></p>
                    </td>
                </tr>

                <!-- Language Selection -->
                <tr>
                    <th scope="row">
                        <label><?php _e('Available Languages', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <div class="hk-translate-languages-grid">
                            <?php foreach ($all_languages as $code => $language): ?>
                                <label class="hk-translate-language-item <?php echo in_array($code, $enabled_languages) ? 'selected' : ''; ?>">
                                    <input type="checkbox" 
                                           name="enabled_languages[]" 
                                           value="<?php echo esc_attr($code); ?>"
                                           <?php checked(in_array($code, $enabled_languages)); ?>>
                                    <img src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($code)); ?>" 
                                         alt="<?php echo esc_attr($language['english_name']); ?>"
                                         class="hk-translate-flag">
                                    <span class="hk-translate-language-name">
                                        <?php echo esc_html($language['name']); ?>
                                        <small>(<?php echo esc_html($language['english_name']); ?>)</small>
                                    </span>
                                    <span class="hk-translate-check-icon">
                                        <span class="dashicons dashicons-yes"></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="description"><?php _e('Select which languages will be available in the translation dropdown.', 'hk-translate'); ?></p>
                        
                        <!-- Seçili Diller Listesi -->
                        <div class="hk-translate-selected-languages" id="selectedLanguagesList">
                            <div class="hk-translate-selected-header">
                                <h4><?php _e('Selected Languages:', 'hk-translate'); ?></h4>
                                <div class="hk-translate-ordering-info">
                                    <p class="description">
                                        <?php _e('Drag and drop to reorder how languages appear in the dropdown menu.', 'hk-translate'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="hk-translate-selected-items" id="selectedLanguagesContainer">
                                <?php 
                                // Display languages in order specified by language_order
                                $display_order = 1;
                                foreach ($language_order as $code): 
                                    if (in_array($code, $enabled_languages) && isset($all_languages[$code])): ?>
                                        <div class="hk-translate-selected-item" data-lang="<?php echo esc_attr($code); ?>">
                                            <div class="hk-translate-drag-handle">
                                                <span class="dashicons dashicons-sort"></span>
                                            </div>
                                            <div class="hk-translate-order-number"><?php echo $display_order++; ?></div>
                                            <img src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($code)); ?>" 
                                                 alt="<?php echo esc_attr($all_languages[$code]['english_name']); ?>">
                                            <div class="hk-translate-lang-info">
                                                <span class="hk-translate-lang-name"><?php echo esc_html($all_languages[$code]['name']); ?></span>
                                                <span class="hk-translate-lang-code">(<?php echo esc_html(strtoupper($code)); ?>)</span>
                                            </div>
                                        </div>
                                    <?php endif; 
                                endforeach; ?>
                            </div>
                            <div class="hk-translate-ordering-actions">
                                <button type="button" id="hk-translate-save-order" class="button button-primary">
                                    <span class="dashicons dashicons-saved"></span>
                                    <?php _e('Save Order', 'hk-translate'); ?>
                                </button>
                                <button type="button" id="hk-translate-reset-order" class="button">
                                    <span class="dashicons dashicons-undo"></span>
                                    <?php _e('Reset to Default', 'hk-translate'); ?>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Default Language -->
                <tr>
                    <th scope="row">
                        <label for="default_language"><?php _e('Default Language', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <select id="default_language" name="default_language">
                            <?php foreach ($all_languages as $code => $language): ?>
                                <option value="<?php echo esc_attr($code); ?>" <?php selected($default_language, $code); ?>>
                                    <?php echo esc_html($language['name'] . ' (' . $language['english_name'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <!-- Default Language Warning -->
                        <?php if (!in_array($default_language, $enabled_languages)): ?>
                            <div class="hk-translate-warning" style="margin-top: 10px; padding: 10px; border-left: 4px solid #ffba00; background: #fff8e1; color: #8a6914;">
                                <strong><?php _e('Warning:', 'hk-translate'); ?></strong>
                                <?php _e('The selected default language is not active in the language list. Please activate it or choose a different default language.', 'hk-translate'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <p class="description"><?php _e('The default language of your website content. This language will be shown first in the dropdown and on the button.', 'hk-translate'); ?></p>
                    </td>
                </tr>

                <!-- Auto Browser Language Detection -->
                <tr>
                    <th scope="row">
                        <label for="auto_detect_language"><?php _e('Auto-Detect Browser Language', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <label class="hk-translate-switch">
                            <input type="checkbox" id="auto_detect_language" name="auto_detect_language" value="1" <?php checked($auto_detect_language ?? false, true); ?>>
                            <span class="hk-translate-slider"></span>
                        </label>
                        <p class="description"><?php _e('Automatically detect visitor\'s browser language and switch to it if available. Falls back to default language if browser language is not in the active languages list.', 'hk-translate'); ?></p>
                    </td>
                </tr>


                <!-- Compact Mode -->
                <tr>
                    <th scope="row">
                        <label for="compact_mode"><?php _e('Compact Mode', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <label class="hk-translate-switch">
                            <input type="checkbox" id="compact_mode" name="compact_mode" value="1" <?php checked($compact_mode ?? false, true); ?>>
                            <span class="hk-translate-slider"></span>
                        </label>
                        <p class="description"><?php _e('Show only flags without language names for a cleaner look. Perfect for mobile devices and minimal designs.', 'hk-translate'); ?></p>
                    </td>
                </tr>

                <!-- NTW: Navigation Menu Language Switcher Settings -->
                <tr>
                    <th scope="row">
                        <label for="ntw_menu_position"><span class="dashicons dashicons-menu"></span> <?php _e('NTW Dropdown Position', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <select id="ntw_menu_position" name="ntw_menu_position">
                            <option value="bottom-left" <?php selected($ntw_menu_position ?? 'bottom-left', 'bottom-left'); ?>><?php _e('Bottom Right', 'hk-translate'); ?></option>
                            <option value="bottom-right" <?php selected($ntw_menu_position ?? 'bottom-left', 'bottom-right'); ?>><?php _e('Bottom Left', 'hk-translate'); ?></option>
                            <option value="top-left" <?php selected($ntw_menu_position ?? 'bottom-left', 'top-left'); ?>><?php _e('Top Right', 'hk-translate'); ?></option>
                            <option value="top-right" <?php selected($ntw_menu_position ?? 'bottom-left', 'top-right'); ?>><?php _e('Top Left', 'hk-translate'); ?></option>
                        </select>
                        <p class="description"><?php _e('Set the dropdown direction for the Navigation Menu Language Switcher (NTW).', 'hk-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ntw_hide_main_widget"><span class="dashicons dashicons-visibility"></span> <?php _e('Hide Main Widget When NTW is Active', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <label class="hk-translate-switch">
                            <input type="checkbox" id="ntw_hide_main_widget" name="ntw_hide_main_widget" value="1" <?php checked($ntw_hide_main_widget ?? false, true); ?>>
                            <span class="hk-translate-slider"></span>
                        </label>
                        <p class="description"><?php _e('If enabled, the main floating widget will be hidden when the NTW is present in the menu.', 'hk-translate'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ntw_compact_mode"><span class="dashicons dashicons-smartphone"></span> <?php _e('NTW Compact Mode', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <label class="hk-translate-switch">
                            <input type="checkbox" id="ntw_compact_mode" name="ntw_compact_mode" value="1" <?php checked($ntw_compact_mode ?? false, true); ?>>
                            <span class="hk-translate-slider"></span>
                        </label>
                        <p class="description"><?php _e('Enable compact mode for NTW - shows only flags without language names in the dropdown menu.', 'hk-translate'); ?></p>
                    </td>
                </tr>

                <!-- Widget Position -->
                <tr>
                    <th scope="row">
                        <label><?php _e('Device-Specific Positions', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <div class="hk-translate-position-settings">
                            <!-- Desktop Position -->
                            <div class="hk-translate-position-item">
                                <label for="desktop_position">
                                    <span class="dashicons dashicons-desktop"></span>
                                    <?php _e('Desktop Position', 'hk-translate'); ?>
                                </label>
                                <select id="desktop_position" name="desktop_position">
                                    <option value="bottom-left" <?php selected($desktop_position, 'bottom-left'); ?>><?php _e('Bottom Left', 'hk-translate'); ?></option>
                                    <option value="bottom-right" <?php selected($desktop_position, 'bottom-right'); ?>><?php _e('Bottom Right', 'hk-translate'); ?></option>
                                    <option value="middle-left" <?php selected($desktop_position, 'middle-left'); ?>><?php _e('Middle Left', 'hk-translate'); ?></option>
                                    <option value="middle-right" <?php selected($desktop_position, 'middle-right'); ?>><?php _e('Middle Right', 'hk-translate'); ?></option>
                                    <option value="top-left" <?php selected($desktop_position, 'top-left'); ?>><?php _e('Top Left', 'hk-translate'); ?></option>
                                    <option value="top-right" <?php selected($desktop_position, 'top-right'); ?>><?php _e('Top Right', 'hk-translate'); ?></option>
                                </select>
                            </div>

                            <!-- Tablet Position -->
                            <div class="hk-translate-position-item">
                                <label for="tablet_position">
                                    <span class="dashicons dashicons-tablet"></span>
                                    <?php _e('Tablet Position', 'hk-translate'); ?>
                                </label>
                                <select id="tablet_position" name="tablet_position">
                                    <option value="bottom-left" <?php selected($tablet_position, 'bottom-left'); ?>><?php _e('Bottom Left', 'hk-translate'); ?></option>
                                    <option value="bottom-right" <?php selected($tablet_position, 'bottom-right'); ?>><?php _e('Bottom Right', 'hk-translate'); ?></option>
                                    <option value="middle-left" <?php selected($tablet_position, 'middle-left'); ?>><?php _e('Middle Left', 'hk-translate'); ?></option>
                                    <option value="middle-right" <?php selected($tablet_position, 'middle-right'); ?>><?php _e('Middle Right', 'hk-translate'); ?></option>
                                    <option value="top-left" <?php selected($tablet_position, 'top-left'); ?>><?php _e('Top Left', 'hk-translate'); ?></option>
                                    <option value="top-right" <?php selected($tablet_position, 'top-right'); ?>><?php _e('Top Right', 'hk-translate'); ?></option>
                                </select>
                            </div>

                            <!-- Mobile Position -->
                            <div class="hk-translate-position-item">
                                <label for="mobile_position">
                                    <span class="dashicons dashicons-smartphone"></span>
                                    <?php _e('Mobile Position', 'hk-translate'); ?>
                                </label>
                                <select id="mobile_position" name="mobile_position">
                                    <option value="bottom-left" <?php selected($mobile_position, 'bottom-left'); ?>><?php _e('Bottom Left', 'hk-translate'); ?></option>
                                    <option value="bottom-right" <?php selected($mobile_position, 'bottom-right'); ?>><?php _e('Bottom Right', 'hk-translate'); ?></option>
                                    <option value="middle-left" <?php selected($mobile_position, 'middle-left'); ?>><?php _e('Middle Left', 'hk-translate'); ?></option>
                                    <option value="middle-right" <?php selected($mobile_position, 'middle-right'); ?>><?php _e('Middle Right', 'hk-translate'); ?></option>
                                    <option value="top-left" <?php selected($mobile_position, 'top-left'); ?>><?php _e('Top Left', 'hk-translate'); ?></option>
                                    <option value="top-right" <?php selected($mobile_position, 'top-right'); ?>><?php _e('Top Right', 'hk-translate'); ?></option>
                                </select>
                            </div>
                        </div>
                        <p class="description"><?php _e('Choose where the translation widget will appear on each device type.', 'hk-translate'); ?></p>
                    </td>
                </tr>

                <!-- Position Settings -->
                <tr>
                    <th scope="row">
                        <label><?php _e('Bottom Position Settings', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <div class="hk-translate-position-settings">
                            <div class="hk-translate-position-item">
                                <label for="desktop_bottom">
                                    <span class="dashicons dashicons-desktop"></span>
                                    <?php _e('Desktop Bottom', 'hk-translate'); ?>
                                </label>
                                <input type="number" 
                                       id="desktop_bottom" 
                                       name="desktop_bottom" 
                                       value="<?php echo esc_attr($desktop_bottom); ?>" 
                                       min="-150" 
                                       max="200" 
                                       step="1">
                                <span class="unit">px</span>
                            </div>

                            <div class="hk-translate-position-item">
                                <label for="tablet_bottom">
                                    <span class="dashicons dashicons-tablet"></span>
                                    <?php _e('Tablet Bottom', 'hk-translate'); ?>
                                </label>
                                <input type="number" 
                                       id="tablet_bottom" 
                                       name="tablet_bottom" 
                                       value="<?php echo esc_attr($tablet_bottom); ?>" 
                                       min="-150" 
                                       max="200" 
                                       step="1">
                                <span class="unit">px</span>
                            </div>

                            <div class="hk-translate-position-item">
                                <label for="mobile_bottom">
                                    <span class="dashicons dashicons-smartphone"></span>
                                    <?php _e('Mobile Bottom', 'hk-translate'); ?>
                                </label>
                                <input type="number" 
                                       id="mobile_bottom" 
                                       name="mobile_bottom" 
                                       value="<?php echo esc_attr($mobile_bottom); ?>" 
                                       min="-150" 
                                       max="200" 
                                       step="1">
                                <span class="unit">px</span>
                            </div>
                        </div>
                        <p class="description"><?php _e('Set the bottom position of the translation widget for different devices.', 'hk-translate'); ?></p>
                    </td>
                </tr>

                <!-- Size Settings -->
                <tr>
                    <th scope="row">
                        <label><?php _e('Widget Size Settings', 'hk-translate'); ?></label>
                    </th>
                    <td>
                        <div class="hk-translate-position-settings">
                            <div class="hk-translate-position-item">
                                <label for="desktop_size">
                                    <span class="dashicons dashicons-desktop"></span>
                                    <?php _e('Desktop Size', 'hk-translate'); ?>
                                </label>
                                <input type="number" 
                                       id="desktop_size" 
                                       name="desktop_size" 
                                       value="<?php echo esc_attr($desktop_size); ?>" 
                                       min="20" 
                                       max="80" 
                                       step="1">
                                <span class="unit">px</span>
                            </div>

                            <div class="hk-translate-position-item">
                                <label for="tablet_size">
                                    <span class="dashicons dashicons-tablet"></span>
                                    <?php _e('Tablet Size', 'hk-translate'); ?>
                                </label>
                                <input type="number" 
                                       id="tablet_size" 
                                       name="tablet_size" 
                                       value="<?php echo esc_attr($tablet_size); ?>" 
                                       min="20" 
                                       max="80" 
                                       step="1">
                                <span class="unit">px</span>
                            </div>

                            <div class="hk-translate-position-item">
                                <label for="mobile_size">
                                    <span class="dashicons dashicons-smartphone"></span>
                                    <?php _e('Mobile Size', 'hk-translate'); ?>
                                </label>
                                <input type="number" 
                                       id="mobile_size" 
                                       name="mobile_size" 
                                       value="<?php echo esc_attr($mobile_size); ?>" 
                                       min="20" 
                                       max="80" 
                                       step="1">
                                <span class="unit">px</span>
                            </div>
                        </div>
                        <p class="description"><?php _e('Set the size of the translation widget button for different devices.', 'hk-translate'); ?></p>
                    </td>
                </tr>

                <!-- Visual Settings Section -->
                <tr>
                    <th scope="row" colspan="2">
                        <h3 class="hk-translate-section-title">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php _e('Visual Settings', 'hk-translate'); ?>
                        </h3>
                    </th>
                </tr>

                <!-- Button Visual Settings -->
                <tr>
                    <th scope="row">
                        <label class="hk-translate-section-label">
                            <span class="dashicons dashicons-button"></span>
                            <?php _e('Button Appearance', 'hk-translate'); ?>
                        </label>
                    </th>
                    <td>
                        <div class="hk-translate-visual-section">
                            <div class="hk-translate-visual-grid">
                                <!-- Button Background Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="btn_bg_enabled" class="hk-translate-visual-toggle">
                                            <input type="checkbox" id="btn_bg_enabled" name="btn_bg_enabled" value="1" <?php checked($btn_bg_enabled ?? true, true); ?>>
                                            <span class="hk-translate-toggle-text"><?php _e('Background Color', 'hk-translate'); ?></span>
                                        </label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="btn_bg_color" 
                                               name="btn_bg_color" 
                                               value="<?php echo esc_attr($btn_bg_color ?? '#ffffff'); ?>"
                                               class="hk-translate-color-input"
                                               <?php echo !($btn_bg_enabled ?? true) ? 'disabled' : ''; ?>>
                                    </div>
                                </div>

                                <!-- Button Border -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="btn_border_enabled" class="hk-translate-visual-toggle">
                                            <input type="checkbox" id="btn_border_enabled" name="btn_border_enabled" value="1" <?php checked($btn_border_enabled ?? true, true); ?>>
                                            <span class="hk-translate-toggle-text"><?php _e('Border', 'hk-translate'); ?></span>
                                        </label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="btn_border_color" 
                                               name="btn_border_color" 
                                               value="<?php echo esc_attr($btn_border_color ?? '#dddddd'); ?>"
                                               class="hk-translate-color-input"
                                               <?php echo !($btn_border_enabled ?? true) ? 'disabled' : ''; ?>>
                                        <div class="hk-translate-number-input">
                                            <input type="number" 
                                                   id="btn_border_width" 
                                                   name="btn_border_width" 
                                                   value="<?php echo esc_attr($btn_border_width ?? 2); ?>" 
                                                   min="1" 
                                                   max="5" 
                                                   step="1"
                                                   <?php echo !($btn_border_enabled ?? true) ? 'disabled' : ''; ?>>
                                            <span class="unit">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Button Border Radius -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="btn_border_radius" class="hk-translate-visual-label"><?php _e('Border Radius', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <div class="hk-translate-number-input">
                                            <input type="number" 
                                                   id="btn_border_radius" 
                                                   name="btn_border_radius" 
                                                   value="<?php echo esc_attr($btn_border_radius ?? 50); ?>" 
                                                   min="0" 
                                                   max="50" 
                                                   step="1">
                                            <span class="unit">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Flag Border Radius -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="btn_flag_radius" class="hk-translate-visual-label"><?php _e('Flag Border Radius', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <div class="hk-translate-number-input">
                                            <input type="number" 
                                                   id="btn_flag_radius" 
                                                   name="btn_flag_radius" 
                                                   value="<?php echo esc_attr($btn_flag_radius ?? 50); ?>" 
                                                   min="0" 
                                                   max="50" 
                                                   step="1">
                                            <span class="unit">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Button Hover Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="btn_hover_color" class="hk-translate-visual-label"><?php _e('Hover Border Color', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="btn_hover_color" 
                                               name="btn_hover_color" 
                                               value="<?php echo esc_attr($btn_hover_color ?? '#007cba'); ?>"
                                               class="hk-translate-color-input">
                                    </div>
                                </div>
                            </div>
                            <p class="description"><?php _e('Customize the appearance of the translation button.', 'hk-translate'); ?></p>
                        </div>
                    </td>
                </tr>

                <!-- Menu Visual Settings -->
                <tr>
                    <th scope="row">
                        <label class="hk-translate-section-label">
                            <span class="dashicons dashicons-menu"></span>
                            <?php _e('Menu Appearance', 'hk-translate'); ?>
                        </label>
                    </th>
                    <td>
                        <div class="hk-translate-visual-section">
                            <div class="hk-translate-visual-grid">
                                <!-- Menu Height -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_height" class="hk-translate-visual-label"><?php _e('Menu Height', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <div class="hk-translate-number-input">
                                            <input type="number" 
                                                   id="menu_height" 
                                                   name="menu_height" 
                                                   value="<?php echo esc_attr($menu_height); ?>" 
                                                   min="100" 
                                                   max="500" 
                                                   step="10">
                                            <span class="unit">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Width -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_width" class="hk-translate-visual-label"><?php _e('Menu Width', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <div class="hk-translate-number-input">
                                            <input type="number" 
                                                   id="menu_width" 
                                                   name="menu_width" 
                                                   value="<?php echo esc_attr($menu_width ?? 200); ?>" 
                                                   min="150" 
                                                   max="300" 
                                                   step="10">
                                            <span class="unit">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Background Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_bg_color" class="hk-translate-visual-label"><?php _e('Background Color', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="menu_bg_color" 
                                               name="menu_bg_color" 
                                               value="<?php echo esc_attr($menu_bg_color ?? '#ffffff'); ?>"
                                               class="hk-translate-color-input">
                                    </div>
                                </div>

                                <!-- Menu Border -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_border_enabled" class="hk-translate-visual-toggle">
                                            <input type="checkbox" id="menu_border_enabled" name="menu_border_enabled" value="1" <?php checked($menu_border_enabled ?? true, true); ?>>
                                            <span class="hk-translate-toggle-text"><?php _e('Border', 'hk-translate'); ?></span>
                                        </label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="menu_border_color" 
                                               name="menu_border_color" 
                                               value="<?php echo esc_attr($menu_border_color ?? '#dddddd'); ?>"
                                               class="hk-translate-color-input"
                                               <?php echo !($menu_border_enabled ?? true) ? 'disabled' : ''; ?>>
                                        <div class="hk-translate-number-input">
                                            <input type="number" 
                                                   id="menu_border_width" 
                                                   name="menu_border_width" 
                                                   value="<?php echo esc_attr($menu_border_width ?? 1); ?>" 
                                                   min="1" 
                                                   max="5" 
                                                   step="1"
                                                   <?php echo !($menu_border_enabled ?? true) ? 'disabled' : ''; ?>>
                                            <span class="unit">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Border Radius -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_border_radius" class="hk-translate-visual-label"><?php _e('Border Radius', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <div class="hk-translate-number-input">
                                            <input type="number" 
                                                   id="menu_border_radius" 
                                                   name="menu_border_radius" 
                                                   value="<?php echo esc_attr($menu_border_radius ?? 8); ?>" 
                                                   min="0" 
                                                   max="20" 
                                                   step="1">
                                            <span class="unit">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Text Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_text_color" class="hk-translate-visual-label"><?php _e('Text Color', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="menu_text_color" 
                                               name="menu_text_color" 
                                               value="<?php echo esc_attr($menu_text_color ?? '#333333'); ?>"
                                               class="hk-translate-color-input">
                                    </div>
                                </div>

                                <!-- Hover Background Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_hover_bg_color" class="hk-translate-visual-label"><?php _e('Hover Background', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="menu_hover_bg_color" 
                                               name="menu_hover_bg_color" 
                                               value="<?php echo esc_attr($menu_hover_bg_color ?? '#0073aa'); ?>"
                                               class="hk-translate-color-input">
                                    </div>
                                </div>

                                <!-- Hover Text Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_hover_text_color" class="hk-translate-visual-label"><?php _e('Hover Text Color', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="menu_hover_text_color" 
                                               name="menu_hover_text_color" 
                                               value="<?php echo esc_attr($menu_hover_text_color ?? '#ffffff'); ?>"
                                               class="hk-translate-color-input">
                                    </div>
                                </div>

                                <!-- Active Background Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_active_bg_color" class="hk-translate-visual-label"><?php _e('Active Background', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="menu_active_bg_color" 
                                               name="menu_active_bg_color" 
                                               value="<?php echo esc_attr($menu_active_bg_color ?? '#e7f3ff'); ?>"
                                               class="hk-translate-color-input">
                                    </div>
                                </div>

                                <!-- Active Text Color -->
                                <div class="hk-translate-visual-item">
                                    <div class="hk-translate-visual-header">
                                        <label for="menu_active_text_color" class="hk-translate-visual-label"><?php _e('Active Text Color', 'hk-translate'); ?></label>
                                    </div>
                                    <div class="hk-translate-visual-controls">
                                        <input type="color" 
                                               id="menu_active_text_color" 
                                               name="menu_active_text_color" 
                                               value="<?php echo esc_attr($menu_active_text_color ?? '#007cba'); ?>"
                                               class="hk-translate-color-input">
                                    </div>
                                </div>
                            </div>
                            <p class="description"><?php _e('Customize the appearance of the dropdown menu. Maximum height creates scrollbar when needed.', 'hk-translate'); ?></p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="hk-translate-actions">
            <button type="submit" class="button button-primary" id="save-settings">
                <span class="dashicons dashicons-yes"></span>
                <?php _e('Save Settings', 'hk-translate'); ?>
            </button>
            
            <button type="button" class="button button-secondary" id="reset-settings">
                <span class="dashicons dashicons-update"></span>
                <?php _e('Reset to Defaults', 'hk-translate'); ?>
            </button>
            
            <!-- Preview Update Notice -->
            <div class="hk-translate-preview-notice" style="margin-top: 15px; padding: 12px; border-left: 4px solid #0073aa; background: #f0f6fc; color: #0c4a6e;">
                <span class="dashicons dashicons-info" style="color: #0073aa; margin-right: 5px;"></span>
                <strong><?php _e('Note:', 'hk-translate'); ?></strong>
                <?php _e('After saving settings, please refresh the preview area to see the latest changes. The preview might not update automatically.', 'hk-translate'); ?>
            </div>
        </div>
    </form>

    <!-- Preview Section -->
    <?php
    // Selected languages and all languages are passed to preview-section
    global $enabled_languages, $all_languages, $default_language;
    include HK_TRANSLATE_PLUGIN_DIR . 'admin/preview-section.php';
    ?>
</div>