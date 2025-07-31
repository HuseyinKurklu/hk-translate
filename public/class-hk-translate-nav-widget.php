<?php
/**
 * HK Translate Navigation Menu Language Switcher Widget (NTW)
 *
 * This class renders the language switcher as a WordPress nav menu item.
 *
 * @since 1.1.0
 */
class HK_Translate_Nav_Widget {
    
    private static $widget_counter = 0;
    
    /**
     * Render the NTW widget for nav menu - IMPROVED VERSION
     * @param array $args
     */
    public static function render_ntw_widget($args = array()) {
        self::$widget_counter++;
        
        // Get plugin settings
        $settings = get_option('hk_translate_settings', array());
        $enabled_languages = class_exists('HK_Translate_Languages') ? HK_Translate_Languages::get_enabled_languages() : array(
            'tr' => ['name' => 'Türkçe', 'flag' => 'tr.svg'],
            'en' => ['name' => 'English', 'flag' => 'en.svg'],
            'de' => ['name' => 'Deutsch', 'flag' => 'de.svg'],
            'fr' => ['name' => 'Français', 'flag' => 'fr.svg'],
            'es' => ['name' => 'Español', 'flag' => 'es.svg']
        );
        
        $default_language = isset($settings['default_language']) ? $settings['default_language'] : 'tr';
        $language_order = isset($settings['language_order']) ? $settings['language_order'] : array_keys($enabled_languages);
        $ntw_position = isset($settings['ntw_menu_position']) ? $settings['ntw_menu_position'] : 'bottom-left';
        $compact_mode = isset($settings['ntw_compact_mode']) ? $settings['ntw_compact_mode'] : false;
        
        // Unique ID for this widget instance
        $widget_id = isset($args['menu_item_id']) ? 'hkNTW_menu_' . $args['menu_item_id'] : 'hkNTW_' . self::$widget_counter;
        
        // Sort languages
        $sorted_languages = array();
        foreach ($language_order as $lang_code) {
            if (isset($enabled_languages[$lang_code])) {
                $sorted_languages[$lang_code] = $enabled_languages[$lang_code];
            }
        }
        
        // Add any missing enabled languages
        foreach ($enabled_languages as $code => $lang) {
            if (!isset($sorted_languages[$code])) {
                $sorted_languages[$code] = $lang;
            }
        }
        
        // Widget classes
        $ntw_class = 'hk-translate-ntw-dropdown ntw-' . $ntw_position;
        if ($compact_mode) {
            $ntw_class .= ' hk-translate-ntw-compact';
        }

        // CSS Variables for dynamic styling
        $css_vars = self::generate_css_variables($settings);
        
        // Render the widget with inline CSS and JS
        echo '<style>' . $css_vars . '</style>';
        
        ?>
        <script>
        (function() {
            // Unique functions for this widget instance
            window['toggle_<?php echo $widget_id; ?>'] = function() {
                console.log('🎯 NTW Toggle for: <?php echo $widget_id; ?>');
                var dropdown = document.getElementById('<?php echo $widget_id; ?>');
                if (dropdown) {
                    // Close all other NTW dropdowns first
                    var allDropdowns = document.querySelectorAll('.hk-translate-ntw-dropdown');
                    allDropdowns.forEach(function(dd) {
                        if (dd.id !== '<?php echo $widget_id; ?>') {
                            dd.classList.remove('open');
                        }
                    });
                    
                    // Toggle this dropdown
                    dropdown.classList.toggle('open');
                    console.log('✅ Dropdown state:', dropdown.classList.contains('open'));
                } else {
                    console.error('❌ NTW Element not found:', '<?php echo $widget_id; ?>');
                }
                return false;
            };
            
            window['select_<?php echo $widget_id; ?>'] = function(langCode, langName) {
                console.log('🌍 Language selected:', langCode, langName);
                
                // Update flag in this widget using main widget's getLocalFlagUrl function
                var flag = document.getElementById('<?php echo $widget_id; ?>_flag');
                if (flag) {
                    if (typeof window.getLocalFlagUrl === 'function') {
                        var newUrl = window.getLocalFlagUrl(langCode);
                        console.log('🔍 NTW Flag URL from main widget:', newUrl);
                        flag.src = newUrl;
                    } else {
                        // Fallback with proper flag mapping
                        var pluginUrl = '';
                        if (typeof hk_translate_settings !== 'undefined' && hk_translate_settings.plugin_url) {
                            pluginUrl = hk_translate_settings.plugin_url;
                        } else {
                            pluginUrl = '<?php echo HK_TRANSLATE_PLUGIN_URL; ?>';
                        }
                        console.log('🔍 NTW Fallback Plugin URL:', pluginUrl);
                        
                        var flagMap = {
                            'af': 'af.svg', 'am': 'am.svg', 'ar': 'ar.svg', 'az': 'az.svg', 'be': 'be.svg',
                            'bg': 'bg.svg', 'bn': 'bn.svg', 'bs': 'bs.svg', 'ca': 'ca.svg', 'co': 'co.svg',
                            'cs': 'cs.svg', 'cy': 'cy.svg', 'da': 'da.svg', 'de': 'de.svg', 'el': 'el.svg',
                            'en': 'en-us.svg', 'eo': 'eo.svg', 'es': 'es.svg', 'et': 'et.svg', 'eu': 'eu.svg',
                            'fa': 'fa.svg', 'fi': 'fi.svg', 'fr': 'fr.svg', 'fy': 'fy.svg', 'ga': 'ga.svg',
                            'gd': 'gd.svg', 'gl': 'gl.svg', 'gu': 'gu.svg', 'ha': 'ha.svg', 'haw': 'haw.svg',
                            'he': 'iw.svg', 'hi': 'hi.svg', 'hr': 'hr.svg', 'ht': 'ht.svg', 'hu': 'hu.svg',
                            'hy': 'hy.svg', 'id': 'id.svg', 'ig': 'ig.svg', 'is': 'is.svg', 'it': 'it.svg',
                            'ja': 'ja.svg', 'jw': 'jw.svg', 'ka': 'ka.svg', 'kk': 'kk.svg', 'km': 'km.svg',
                            'kn': 'kn.svg', 'ko': 'ko.svg', 'ku': 'ku.svg', 'ky': 'ky.svg', 'la': 'la.svg',
                            'lb': 'lb.svg', 'lo': 'lo.svg', 'lt': 'lt.svg', 'lv': 'lv.svg', 'mg': 'mg.svg',
                            'mi': 'mi.svg', 'mk': 'mk.svg', 'ml': 'ml.svg', 'mn': 'mn.svg', 'mr': 'mr.svg',
                            'ms': 'ms.svg', 'mt': 'mt.svg', 'my': 'my.svg', 'ne': 'ne.svg', 'nl': 'nl.svg',
                            'no': 'no.svg', 'ny': 'ny.svg', 'pa': 'pa.svg', 'pl': 'pl.svg', 'ps': 'ps.svg',
                            'pt': 'pt.svg', 'ro': 'ro.svg', 'ru': 'ru.svg', 'sd': 'sd.svg', 'si': 'si.svg',
                            'sk': 'sk.svg', 'sl': 'sl.svg', 'sm': 'sm.svg', 'sn': 'sn.svg', 'so': 'so.svg',
                            'sq': 'sq.svg', 'sr': 'sr.svg', 'st': 'st.svg', 'su': 'su.svg', 'sv': 'sv.svg',
                            'sw': 'sw.svg', 'ta': 'ta.svg', 'te': 'te.svg', 'tg': 'tg.svg', 'th': 'th.svg',
                            'tl': 'tl.svg', 'tr': 'tr.svg', 'uk': 'uk.svg', 'ur': 'ur.svg', 'uz': 'uz.svg',
                            'vi': 'vi.svg', 'xh': 'xh.svg', 'yi': 'yi.svg', 'yo': 'yo.svg', 'zh': 'zh-CN.svg',
                            'zu': 'zu.svg'
                        };
                        var filename = flagMap[langCode] || 'tr.svg';
                        flag.src = pluginUrl + 'public/flags/svg/' + filename;
                    }
                    flag.alt = langName;
                }
                
                // Update active states in this widget
                var items = document.querySelectorAll('#<?php echo $widget_id; ?> .hk-translate-ntw-item');
                items.forEach(function(item) {
                    item.classList.remove('active');
                    if (item.getAttribute('data-lang') === langCode) {
                        item.classList.add('active');
                    }
                });
                
                // Close dropdown
                var dropdown = document.getElementById('<?php echo $widget_id; ?>');
                if (dropdown) {
                    dropdown.classList.remove('open');
                }
                
                // Update main widget flag if exists using main widget's function
                var mainFlag = document.getElementById('hkCurrentFlag');
                if (mainFlag && typeof window.getLocalFlagUrl === 'function') {
                    mainFlag.src = window.getLocalFlagUrl(langCode);
                    mainFlag.alt = langName;
                }
                
                // Call translation function
                if (typeof doGTranslate === 'function') {
                    doGTranslate('tr|' + langCode);
                } else if (typeof selectHKLanguage === 'function') {
                    selectHKLanguage(langCode, langCode.toUpperCase(), langName, '<?php echo $default_language; ?>');
                } else {
                    console.log('🔄 Would translate:', 'tr|' + langCode);
                }
                
                return false;
            };
        })();
        </script>
        
        <div class="<?php echo esc_attr($ntw_class); ?>" id="<?php echo esc_attr($widget_id); ?>">
            <div class="hk-translate-ntw-btn" onclick="toggle_<?php echo esc_attr($widget_id); ?>()" title="<?php esc_attr_e('Select Language', 'hk-translate'); ?>">
                <img 
                    src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($default_language)); ?>" 
                    alt="<?php echo esc_attr(HK_Translate_Languages::get_language_name($default_language)); ?>" 
                    id="<?php echo esc_attr($widget_id); ?>_flag"
                />
            </div>
            <div class="hk-translate-ntw-menu">
                <?php foreach ($sorted_languages as $code => $language): ?>
                    <a href="#" 
                       class="hk-translate-ntw-item <?php echo ($code === $default_language) ? 'active' : ''; ?>" 
                       onclick="select_<?php echo esc_attr($widget_id); ?>('<?php echo esc_attr($code); ?>', '<?php echo esc_attr($language['name']); ?>')" 
                       data-lang="<?php echo esc_attr($code); ?>">
                        <img src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($code)); ?>" alt="<?php echo esc_attr($language['name']); ?>" />
                        <span><?php echo esc_html($language['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Generate CSS variables for dynamic styling
     */
    private static function generate_css_variables($settings) {
        $desktop_size = isset($settings['desktop_size']) ? intval($settings['desktop_size']) : 40;
        $tablet_size = isset($settings['tablet_size']) ? intval($settings['tablet_size']) : 36;
        $mobile_size = isset($settings['mobile_size']) ? intval($settings['mobile_size']) : 32;
        $menu_height = isset($settings['menu_height']) ? intval($settings['menu_height']) : 250;
        
        $btn_bg_enabled = isset($settings['btn_bg_enabled']) ? $settings['btn_bg_enabled'] : true;
        $btn_bg_color = isset($settings['btn_bg_color']) ? $settings['btn_bg_color'] : '#ffffff';
        $btn_border_enabled = isset($settings['btn_border_enabled']) ? $settings['btn_border_enabled'] : true;
        $btn_border_color = isset($settings['btn_border_color']) ? $settings['btn_border_color'] : '#dddddd';
        $btn_border_width = isset($settings['btn_border_width']) ? intval($settings['btn_border_width']) : 2;
        $btn_border_radius = isset($settings['btn_border_radius']) ? intval($settings['btn_border_radius']) : 50;
        $btn_flag_radius = isset($settings['btn_flag_radius']) ? intval($settings['btn_flag_radius']) : 50;
        $btn_hover_color = isset($settings['btn_hover_color']) ? $settings['btn_hover_color'] : '#007cba';
        
        $menu_bg_color = isset($settings['menu_bg_color']) ? $settings['menu_bg_color'] : '#ffffff';
        $menu_border_enabled = isset($settings['menu_border_enabled']) ? $settings['menu_border_enabled'] : true;
        $menu_border_color = isset($settings['menu_border_color']) ? $settings['menu_border_color'] : '#dddddd';
        $menu_border_width = isset($settings['menu_border_width']) ? intval($settings['menu_border_width']) : 1;
        $menu_border_radius = isset($settings['menu_border_radius']) ? intval($settings['menu_border_radius']) : 8;
        $menu_width = isset($settings['menu_width']) ? intval($settings['menu_width']) : 160;
        $menu_text_color = isset($settings['menu_text_color']) ? $settings['menu_text_color'] : '#333333';
        $menu_hover_bg_color = isset($settings['menu_hover_bg_color']) ? $settings['menu_hover_bg_color'] : '#0073aa';
        $menu_hover_text_color = isset($settings['menu_hover_text_color']) ? $settings['menu_hover_text_color'] : '#ffffff';
        $menu_active_bg_color = isset($settings['menu_active_bg_color']) ? $settings['menu_active_bg_color'] : '#e7f3ff';
        $menu_active_text_color = isset($settings['menu_active_text_color']) ? $settings['menu_active_text_color'] : '#007cba';
        
        $btn_bg = $btn_bg_enabled ? $btn_bg_color : 'transparent';
        $btn_border = $btn_border_enabled ? "{$btn_border_width}px solid {$btn_border_color}" : 'none';
        $menu_border = $menu_border_enabled ? "{$menu_border_width}px solid {$menu_border_color}" : 'none';
        
        return ":root {
            --hk-translate-desktop-size: {$desktop_size}px;
            --hk-translate-tablet-size: {$tablet_size}px;
            --hk-translate-mobile-size: {$mobile_size}px;
            --hk-translate-menu-height: {$menu_height}px;
            --hk-translate-btn-bg: {$btn_bg};
            --hk-translate-btn-border: {$btn_border};
            --hk-translate-btn-radius: {$btn_border_radius}px;
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
        }";
    }

    /**
     * Initialize menu integration hooks
     */
    public static function init_menu_integration() {
        add_action('admin_init', array(__CLASS__, 'add_nav_menu_meta_boxes'));
        add_filter('wp_setup_nav_menu_item', array(__CLASS__, 'setup_nav_menu_item'));
        add_action('wp_update_nav_menu_item', array(__CLASS__, 'update_nav_menu_item'), 10, 3);
        add_filter('walker_nav_menu_start_el', array(__CLASS__, 'walker_nav_menu_start_el'), 10, 4);
    }

    /**
     * Add HK Translate meta box to menu admin page
     */
    public static function add_nav_menu_meta_boxes() {
        add_meta_box(
            'add-hk-translate',
            __('HK Translate', 'hk-translate'),
            array(__CLASS__, 'nav_menu_link'),
            'nav-menus',
            'side',
            'default'
        );
    }

    /**
     * Output the HK Translate menu meta box
     */
    public static function nav_menu_link() {
        global $_nav_menu_placeholder;
        $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
        ?>
        <div id="posttype-hk-translate" class="posttypediv">
            <div id="tabs-panel-hk-translate" class="tabs-panel tabs-panel-active">
                <ul id="hk-translate-checklist" class="categorychecklist form-no-clear">
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1" /> 
                            Language Switcher
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="hk_translate" />
                        <input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="Language Switcher" />
                        <input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#hk-translate" />
                        <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-classes]" value="hk-translate-menu-item" />
                    </li>
                </ul>
            </div>
            <p class="button-controls wp-clearfix">
                <span class="add-to-menu">
                    <input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-hk-translate" />
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
        <?php
    }

    /**
     * Setup the nav menu item for HK Translate
     */
    public static function setup_nav_menu_item($menu_item) {
        if ($menu_item->type == 'hk_translate') {
            $menu_item->type_label = 'HK Translate';
            $menu_item->url = '#hk-translate';
            if (empty($menu_item->title)) {
                $menu_item->title = 'Language Switcher';
            }
        }
        return $menu_item;
    }

    /**
     * Save custom fields for HK Translate menu items
     */
    public static function update_nav_menu_item($menu_id, $menu_item_db_id, $args) {
        if (isset($args['menu-item-type']) && $args['menu-item-type'] === 'hk_translate') {
            error_log('HK Translate menu item saved successfully!');
        }
    }

    /**
     * Replace HK Translate menu items with language switcher widget
     */
    public static function walker_nav_menu_start_el($item_output, $item, $depth, $args) {
        if ($item->type !== 'hk_translate') {
            return $item_output;
        }

        ob_start();
        echo '<div class="hk-translate-menu-wrapper">';
        self::render_ntw_widget_for_menu('hkTranslateNTWDropdown_menu_' . $item->ID, $item->ID);
        echo '</div>';
        return ob_get_clean();
    }
    
    /**
     * NTW widget'ını menü için özelleştirilmiş şekilde render et
     */
    public static function render_ntw_widget_for_menu($widget_id, $menu_item_id = null) {
        // Get plugin settings
        $settings = get_option('hk_translate_settings', array());
        $enabled_languages = class_exists('HK_Translate_Languages') ? HK_Translate_Languages::get_enabled_languages() : array('tr'=>['name'=>'Türkçe','flag'=>'tr.svg'],'en'=>['name'=>'English','flag'=>'en-us.svg']);
        $default_language = isset($settings['default_language']) ? $settings['default_language'] : 'tr';
        $language_order = isset($settings['language_order']) ? $settings['language_order'] : array_keys($enabled_languages);
        $ntw_position = isset($settings['ntw_menu_position']) ? $settings['ntw_menu_position'] : 'bottom-left';
        $compact_mode = isset($settings['ntw_compact_mode']) ? $settings['ntw_compact_mode'] : (isset($settings['compact_mode']) ? $settings['compact_mode'] : false);
        
        // Sort languages
        $sorted_languages = array();
        foreach ($language_order as $lang_code) {
            if (isset($enabled_languages[$lang_code])) {
                $sorted_languages[$lang_code] = $enabled_languages[$lang_code];
                unset($enabled_languages[$lang_code]);
            }
        }
        $sorted_languages = array_merge($sorted_languages, $enabled_languages);
        
        // Widget class
        $ntw_class = 'hk-translate-ntw-dropdown ntw-' . $ntw_position . ($compact_mode ? ' hk-translate-ntw-compact' : '');

        // Inline styles for menu wrapper (isteğe bağlı, JS ile açma/kapama)
        echo '<style>
        .hk-translate-menu-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .menu-item .hk-translate-menu-wrapper .hk-translate-ntw-dropdown {
            margin: 0;
            padding: 0;
        }
        </style>';

        ?>
        <div class="<?php echo esc_attr($ntw_class); ?>" id="<?php echo esc_attr($widget_id); ?>">
            <div class="hk-translate-ntw-btn" title="<?php esc_attr_e('Select Language', 'hk-translate'); ?>">
                <img
                    src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($default_language)); ?>"
                    alt="<?php echo esc_attr(HK_Translate_Languages::get_language_name($default_language)); ?>"
                    id="<?php echo esc_attr($widget_id); ?>_flag"
                />
            </div>

            <div class="hk-translate-ntw-menu">
                <?php foreach ($sorted_languages as $code => $language): ?>
                    <a
                        href="#"
                        class="hk-translate-ntw-item <?php echo ($code === $default_language) ? 'active' : ''; ?>"
                        data-lang="<?php echo esc_attr($code === 'en' ? 'en' : $code); ?>"
                    >
                        <img src="<?php echo esc_url(HK_Translate_Languages::get_flag_url($code)); ?>" alt="<?php echo esc_attr($language['name']); ?>" />
                        <span><?php echo esc_html($language['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
