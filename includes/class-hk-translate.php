<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */
class HK_Translate {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        if (defined('HK_TRANSLATE_VERSION')) {
            $this->version = HK_TRANSLATE_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'hk-translate';

        $this->load_dependencies();
        $this->set_locale();

        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once HK_TRANSLATE_PLUGIN_DIR . 'includes/class-hk-translate-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once HK_TRANSLATE_PLUGIN_DIR . 'includes/class-hk-translate-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once HK_TRANSLATE_PLUGIN_DIR . 'admin/class-hk-translate-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once HK_TRANSLATE_PLUGIN_DIR . 'public/class-hk-translate-public.php';
        require_once HK_TRANSLATE_PLUGIN_DIR . 'public/class-hk-translate-nav-widget.php';

        /**
         * The class responsible for language management.
         */
        require_once HK_TRANSLATE_PLUGIN_DIR . 'includes/class-hk-translate-languages.php';

        $this->loader = new HK_Translate_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() {
        $plugin_i18n = new HK_Translate_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     */
    private function define_admin_hooks() {
        $plugin_admin = new HK_Translate_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('wp_ajax_hk_translate_save_settings', $plugin_admin, 'save_settings');
        $this->loader->add_action('wp_ajax_hk_translate_reset_settings', $plugin_admin, 'reset_settings');
        $this->loader->add_action('wp_ajax_hk_translate_save_language_order', $plugin_admin, 'save_language_order');
        
        // Edit mode AJAX endpoints
        $this->loader->add_action('wp_ajax_hk_translate_save_manual_translation', $plugin_admin, 'save_manual_translation');
        $this->loader->add_action('wp_ajax_hk_translate_save_exclusion', $plugin_admin, 'save_exclusion');
        $this->loader->add_action('wp_ajax_hk_translate_load_manual_translations', $plugin_admin, 'load_manual_translations');
        $this->loader->add_action('wp_ajax_hk_translate_get_manual_translations', $plugin_admin, 'get_manual_translations');
        $this->loader->add_action('wp_ajax_hk_translate_remove_outdated_translation', $plugin_admin, 'remove_outdated_translation');
        
        // Admin bar için edit mode butonu
        $this->loader->add_action('admin_bar_menu', $plugin_admin, 'add_admin_bar_edit_mode', 999);
    }

    // NTW ile ilgili hooklar class dışına taşındı

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks() {
        $plugin_public = new HK_Translate_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        // NTW aktifse ana widget'ı gizle
        // NTW aktifse ana widget'ı gizle: loader ile değil, doğrudan WordPress add_action ile eklenmeli
        add_action('wp_footer', function() use ($plugin_public) {
            $settings = get_option('hk_translate_settings', array());
            $hide_main = isset($settings['ntw_hide_main_widget']) && $settings['ntw_hide_main_widget'];
            $ntw_in_menu = false;
            // Aktif menülerde NTW var mı kontrolü
            $locations = get_nav_menu_locations();
            foreach ($locations as $location) {
                $menu_items = wp_get_nav_menu_items($location);
                if ($menu_items) {
                    foreach ($menu_items as $item) {
                        if (isset($item->title) && $item->title == 'Dil Değiştirici (HK Translate)') {
                            $ntw_in_menu = true;
                            break 2;
                        }
                    }
                }
            }
            if ($hide_main && $ntw_in_menu) {
                // NTW menüdeyse ana widget'ı gösterme
                return;
            }
            $plugin_public->render_translate_widget();
        });
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
        
        // Initialize NTW menu integration
        if (class_exists('HK_Translate_Nav_Widget')) {
            HK_Translate_Nav_Widget::init_menu_integration();
        }
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}