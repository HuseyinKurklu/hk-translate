<?php
/**
 * Plugin Name: HK Translate
 * Plugin URI: https://github.com/huseyinkurklu/hk-translate
 * Description: WordPress için Google Translate API tabanlı özelleştirilebilir çeviri eklentisi. Dil seçeneklerini yönetin ve farklı cihazlar için pozisyon ayarları yapın.
 * Version: 1.0.0
 * Author: Hüseyin Kürklü
 * Author URI: https://huseyinkurklu.com.tr/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hk-translate
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('HK_TRANSLATE_VERSION', '1.0.0');

/**
 * Plugin directory path.
 */
define('HK_TRANSLATE_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * Plugin directory URL.
 */
define('HK_TRANSLATE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_hk_translate() {
    require_once HK_TRANSLATE_PLUGIN_DIR . 'includes/class-hk-translate-activator.php';
    HK_Translate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_hk_translate() {
    require_once HK_TRANSLATE_PLUGIN_DIR . 'includes/class-hk-translate-deactivator.php';
    HK_Translate_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_hk_translate');
register_deactivation_hook(__FILE__, 'deactivate_hk_translate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require HK_TRANSLATE_PLUGIN_DIR . 'includes/class-hk-translate.php';

/**
 * Begins execution of the plugin.
 */
function run_hk_translate() {
    $plugin = new HK_Translate();
    $plugin->run();
}

run_hk_translate();

// === NTW: Navigation Menu Language Switcher entegrasyonu ===
/**
 * HK Translate Menu Meta Box (Alternative Implementation)
 * Adds HK Translate as a menu item option in WordPress admin menu editor
 */
class HK_Translate_Menu_MetaBox {

    /**
     * Initialize the menu meta box
     */
    public function __construct() {
        add_action('admin_init', array($this, 'add_nav_menu_meta_boxes'));
        add_filter('wp_setup_nav_menu_item', array($this, 'setup_nav_menu_item'));
        add_action('wp_update_nav_menu_item', array($this, 'update_nav_menu_item'), 10, 3);
        add_filter('walker_nav_menu_start_el', array($this, 'walker_nav_menu_start_el'), 10, 4);
    }

    /**
     * Add HK Translate meta box to menu admin page
     */
    public function add_nav_menu_meta_boxes() {
        add_meta_box(
            'add-hk-translate-alt',
            __('HK Translate (Alternative)', 'hk-translate'),
            array($this, 'nav_menu_link'),
            'nav-menus',
            'side',
            'default'
        );
    }

    /**
     * Output the HK Translate menu meta box
     */
    public function nav_menu_link() {
        global $_nav_menu_placeholder;
        $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
        ?>
        <div id="posttype-hk-translate-alt" class="posttypediv">
            <div id="tabs-panel-hk-translate-alt" class="tabs-panel tabs-panel-active">
                <ul id="hk-translate-alt-checklist" class="categorychecklist form-no-clear">
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1" /> 
                            Dil Değiştirici (HK Translate)
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="hk_translate" />
                        <input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="Dil Değiştirici (HK Translate)" />
                        <input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#hk-translate" />
                        <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-classes]" value="hk-translate-menu-item" />
                    </li>
                </ul>
            </div>

            <p class="button-controls wp-clearfix">
                <span class="add-to-menu">
                    <input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-hk-translate-alt" />
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
        <?php
    }

    /**
     * Setup the nav menu item for HK Translate
     */
    public function setup_nav_menu_item($menu_item) {
        if ($menu_item->type == 'hk_translate') {
            $menu_item->type_label = 'HK Translate';
            $menu_item->url = '#hk-translate';
            
            if (empty($menu_item->title)) {
                $menu_item->title = 'Dil Değiştirici (HK Translate)';
            }
        }
        return $menu_item;
    }

    /**
     * Save custom fields for HK Translate menu items
     */
    public function update_nav_menu_item($menu_id, $menu_item_db_id, $args) {
        // Menu item kaydedildiğinde debug
        if (isset($args['menu-item-type']) && $args['menu-item-type'] === 'hk_translate') {
            error_log('HK Translate menu item saved successfully!');
        }
    }

    /**
     * Replace HK Translate menu items with language switcher widget
     */
    public function walker_nav_menu_start_el($item_output, $item, $depth, $args) {
        if ($item->type !== 'hk_translate') {
            return $item_output;
        }

        // HK Translate widget'ını render et - unique ID ile
        $unique_id = 'hkTranslateNTWDropdown_menu_' . $item->ID;
        
        ob_start();
        if (class_exists('HK_Translate_Nav_Widget')) {
            // Widget'ı özelleştirilmiş ID ile render et
            echo '<div class="hk-translate-menu-wrapper">';
            HK_Translate_Nav_Widget::render_ntw_widget_for_menu($unique_id, $item->ID);
            echo '</div>';
        } else {
            echo '<span style="color: red;">HK Translate Widget not found</span>';
        }
        return ob_get_clean();
    }
}

// Initialize the alternative menu meta box only if not already initialized
if (!class_exists('HK_Translate_Menu_MetaBox_Alt')) {
    class_alias('HK_Translate_Menu_MetaBox', 'HK_Translate_Menu_MetaBox_Alt');
    new HK_Translate_Menu_MetaBox();
}

// Frontend'de gerekli CSS/JS dosyalarını yükle
add_action('wp_enqueue_scripts', function() {
    // NTW styles always load if plugin is active
    $settings = get_option('hk_translate_settings', array());
    $plugin_enabled = isset($settings['plugin_enabled']) ? $settings['plugin_enabled'] : true;
    
    if ($plugin_enabled) {
        wp_enqueue_style('hk-translate-ntw-style', HK_TRANSLATE_PLUGIN_URL.'public/css/hk-translate-ntw-style.css', array(), HK_TRANSLATE_VERSION);
        
        // NTW JavaScript is embedded inline in the widget render, but we can add global functions here
        wp_add_inline_script('hk-translate', '
        // Global NTW functions
        window.closeAllNTWDropdowns = function() {
            var dropdowns = document.querySelectorAll(".hk-translate-ntw-dropdown");
            dropdowns.forEach(function(dropdown) {
                dropdown.classList.remove("open");
            });
        };
        
        // Close dropdowns when clicking outside
        document.addEventListener("click", function(event) {
            if (!event.target.closest(".hk-translate-ntw-dropdown")) {
                closeAllNTWDropdowns();
            }
        });
        ', 'after');
    }
});