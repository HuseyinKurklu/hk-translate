<?php
/**
 * Responsive Live Preview Section with Pexels Background and Real Widget
 */
if (!defined('WPINC')) {
    die;
}
if (!function_exists('_e')) { function _e($text, $domain = null) { echo $text; } }
if (!function_exists('esc_url')) { function esc_url($url) { return $url; } }
if (!function_exists('esc_attr')) { function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES); } }
if (!function_exists('esc_html')) { function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES); } }
if (!function_exists('esc_attr_e')) { function esc_attr_e($text, $domain = null) { echo esc_attr($text); } }

// Get real site data
$site_title = get_bloginfo('name');
$site_icon_url = get_site_icon_url();
$primary_menu_items = [];
$menu_locations = get_nav_menu_locations();
if (isset($menu_locations['primary'])) {
    $primary_menu = wp_get_nav_menu_object($menu_locations['primary']);
    if ($primary_menu) {
        $primary_menu_items = wp_get_nav_menu_items($primary_menu->term_id);
    }
}

global $enabled_languages, $all_languages, $default_language;
if (!isset($all_languages) || !is_array($all_languages) || count($all_languages) === 0) {
    if (class_exists('HK_Translate_Languages')) {
        $all_languages = HK_Translate_Languages::get_all_languages();
    } else {
        $all_languages = array('tr'=>['name'=>'Türkçe','flag'=>'tr.svg'],'en'=>['name'=>'English','flag'=>'en-us.svg']);
    }
}
if (!isset($enabled_languages) || !is_array($enabled_languages) || count($enabled_languages) === 0) {
    $enabled_languages = array('tr', 'en');
}
$default_language = isset($default_language) ? $default_language : (isset($enabled_languages[0]) ? $enabled_languages[0] : 'tr');
$visual = array(
    'btn_bg_enabled' => isset($btn_bg_enabled) ? $btn_bg_enabled : true,
    'btn_bg_color' => isset($btn_bg_color) ? $btn_bg_color : '#ffffff',
    'btn_border_enabled' => isset($btn_border_enabled) ? $btn_border_enabled : true,
    'btn_border_color' => isset($btn_border_color) ? $btn_border_color : '#dddddd',
    'btn_border_width' => isset($btn_border_width) ? $btn_border_width : 2,
    'btn_border_radius' => isset($btn_border_radius) ? $btn_border_radius : 50,
    'btn_flag_radius' => isset($btn_flag_radius) ? $btn_flag_radius : 50,
    'btn_hover_color' => isset($btn_hover_color) ? $btn_hover_color : '#007cba',
    'menu_bg_color' => isset($menu_bg_color) ? $menu_bg_color : '#ffffff',
    'menu_border_enabled' => isset($menu_border_enabled) ? $menu_border_enabled : true,
    'menu_border_color' => isset($menu_border_color) ? $menu_border_color : '#dddddd',
    'menu_border_width' => isset($menu_border_width) ? $menu_border_width : 1,
    'menu_border_radius' => isset($menu_border_radius) ? $menu_border_radius : 8,
    'menu_width' => isset($menu_width) ? $menu_width : 200,
    'menu_text_color' => isset($menu_text_color) ? $menu_text_color : '#333333',
    'menu_hover_bg_color' => isset($menu_hover_bg_color) ? $menu_hover_bg_color : '#0073aa',
    'menu_hover_text_color' => isset($menu_hover_text_color) ? $menu_hover_text_color : '#ffffff',
    'menu_active_bg_color' => isset($menu_active_bg_color) ? $menu_active_bg_color : '#e7f3ff',
    'menu_active_text_color' => isset($menu_active_text_color) ? $menu_active_text_color : '#007cba',
);
?>
<div class="hk-translate-advanced-preview-section">
    <h2 class="hk-translate-preview-title">
        <span class="dashicons dashicons-visibility"></span>
        <?php _e('Live Preview', 'hk-translate'); ?>
    </h2>
    <div class="hk-translate-device-switcher" style="margin-bottom: 16px;">
        <button type="button" class="hk-translate-device-btn active" data-device="desktop">
            <span class="dashicons dashicons-desktop"></span>
            <?php _e('Desktop', 'hk-translate'); ?>
        </button>
        <button type="button" class="hk-translate-device-btn" data-device="tablet">
            <span class="dashicons dashicons-tablet"></span>
            <?php _e('Tablet', 'hk-translate'); ?>
        </button>
        <button type="button" class="hk-translate-device-btn" data-device="mobile">
            <span class="dashicons dashicons-smartphone"></span>
            <?php _e('Mobile', 'hk-translate'); ?>
        </button>
    </div>
    <div class="hk-translate-preview-wrapper">
        <div class="hk-translate-preview-frame" id="hkPreviewFrame" style="margin:0 auto;">
            <!-- Real Frontend Preview via iframe -->
            <iframe 
                id="hkFrontendPreview" 
                src="<?php echo home_url('?hk_preview_mode=1'); ?>" 
                style="width:100%;height:100%;border:none;border-radius:18px;"
                onload="initPreviewCommunication()"
            ></iframe>
        </div>
        <div class="hk-translate-preview-controls" style="margin-top:18px;display:flex;align-items:center;gap:12px;">
            <button type="button" class="button" onclick="document.getElementById('hkFrontendPreview').contentWindow.location.reload()">
                <span class="dashicons dashicons-update"></span>
                <?php _e('Refresh Preview', 'hk-translate'); ?>
            </button>
            <span class="hk-translate-preview-info">
                <?php _e('Real frontend preview via iframe', 'hk-translate'); ?>
            </span>
        </div>
    </div>
</div>
<script>
(function($){
    var visual = <?php echo json_encode($visual); ?>;
    const deviceSizes = {
        desktop: { width: 1200, height: 600, widget: { left: 32, bottom: 32 } },
        tablet:  { width: 768, height: 500, widget: { left: 24, bottom: 24 } },
        mobile:  { width: 375, height: 667, widget: { left: 12, bottom: 18 } }
    };
    let currentDevice = 'desktop';
    
    function updatePreviewPosition() {
        // NEW SYSTEM: Device-specific classes only
        var desktopPosition = $('#desktop_position').val() || 'bottom-right';
        $('#hkTranslateDropdownPreview').removeClass()
            .addClass('hk-translate-dropdown desktop-' + desktopPosition);
        
        // Widget positioning based on position - now using desktop position
        var css = {};
        var device = deviceSizes[currentDevice];
        
        switch(desktopPosition) {
            case 'bottom-left':
                css = { left: device.widget.left + 'px', bottom: device.widget.bottom + 'px', right: 'auto', top: 'auto' };
                break;
            case 'bottom-right':
                css = { right: device.widget.left + 'px', bottom: device.widget.bottom + 'px', left: 'auto', top: 'auto' };
                break;
            case 'middle-left':
                css = { left: device.widget.left + 'px', top: '50%', right: 'auto', bottom: 'auto', transform: 'translateY(-50%)' };
                break;
            case 'middle-right':
                css = { right: device.widget.left + 'px', top: '50%', left: 'auto', bottom: 'auto', transform: 'translateY(-50%)' };
                break;
            case 'top-left':
                css = { left: device.widget.left + 'px', top: device.widget.bottom + 'px', right: 'auto', bottom: 'auto' };
                break;
            case 'top-right':
                css = { right: device.widget.left + 'px', top: device.widget.bottom + 'px', left: 'auto', bottom: 'auto' };
                break;
        }
        
        $('#hkWidgetPreview').css(css);
    }
    
    function setDevice(device) {
        currentDevice = device;
        const size = deviceSizes[device];
        $('#hkPreviewFrame').css({
            width: size.width + 'px',
            height: size.height + 'px',
            borderRadius: '18px',
            boxShadow: '0 8px 32px rgba(0,0,0,0.15)',
            position: 'relative',
            overflow: 'hidden',
            background: '#f5f5f5',
            transition: 'all 0.3s ease'
        });
        
        // Notify iframe of new dimensions
        const iframe = document.getElementById('hkFrontendPreview');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage({
                type: 'hk_preview_resize',
                device: device,
                size: size
            }, '*');
        }
    }
    
    // Communication with iframe
    function initPreviewCommunication() {
        // Preview iframe loaded
    }
    
    // Notify iframe when admin settings change
    function notifyIframeSettingsChanged() {
        const iframe = document.getElementById('hkFrontendPreview');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage({
                type: 'hk_preview_settings_update',
                settings: visual,
                // Removed legacy position, using device-specific instead
                desktop_position: $('#desktop_position').val(),
                tablet_position: $('#tablet_position').val(),
                mobile_position: $('#mobile_position').val()
            }, '*');
        }
    }
    $('.hk-translate-device-btn').on('click', function(){
        $('.hk-translate-device-btn').removeClass('active');
        $(this).addClass('active');
        setDevice($(this).data('device'));
    });
    
    // Listen for device-specific position changes
    $('#desktop_position, #tablet_position, #mobile_position').on('change', function(){
        notifyIframeSettingsChanged();
    });
    
    // Listen to all setting changes and notify iframe
    var visualInputs = [
        '#btn_bg_enabled', '#btn_bg_color', '#btn_border_enabled', '#btn_border_color', '#btn_border_width', '#btn_border_radius', '#btn_flag_radius', '#btn_hover_color',
        '#menu_bg_color', '#menu_border_enabled', '#menu_border_color', '#menu_border_width', '#menu_border_radius', '#menu_width', '#menu_text_color', '#menu_hover_bg_color', '#menu_hover_text_color', '#menu_active_bg_color', '#menu_active_text_color'
    ];
    $(visualInputs.join(',')).on('input change', function(){
        visual.btn_bg_enabled = $('#btn_bg_enabled').is(':checked');
        visual.btn_bg_color = $('#btn_bg_color').val();
        visual.btn_border_enabled = $('#btn_border_enabled').is(':checked');
        visual.btn_border_color = $('#btn_border_color').val();
        visual.btn_border_width = $('#btn_border_width').val();
        visual.btn_border_radius = $('#btn_border_radius').val();
        visual.btn_flag_radius = $('#btn_flag_radius').val();
        visual.btn_hover_color = $('#btn_hover_color').val();
        visual.menu_bg_color = $('#menu_bg_color').val();
        visual.menu_border_enabled = $('#menu_border_enabled').is(':checked');
        visual.menu_border_color = $('#menu_border_color').val();
        visual.menu_border_width = $('#menu_border_width').val();
        visual.menu_border_radius = $('#menu_border_radius').val();
        visual.menu_width = $('#menu_width').val();
        visual.menu_text_color = $('#menu_text_color').val();
        visual.menu_hover_bg_color = $('#menu_hover_bg_color').val();
        visual.menu_hover_text_color = $('#menu_hover_text_color').val();
        visual.menu_active_bg_color = $('#menu_active_bg_color').val();
        visual.menu_active_text_color = $('#menu_active_text_color').val();
        
        // Notify iframe of settings
        notifyIframeSettingsChanged();
    });
    
    // Set correct desktop size when page loads
    $(document).ready(function() {
        setDevice('desktop');
        
        // Send settings when iframe loads
        setTimeout(() => {
            notifyIframeSettingsChanged();
        }, 1500);
    });
    
})(jQuery);
</script>
<style>
.hk-translate-advanced-preview-section { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; }
.hk-translate-preview-title { display: flex; align-items: center; gap: 8px; font-size: 1.4em; margin-bottom: 18px; }
.hk-translate-device-switcher { display: flex; gap: 10px; }
.hk-translate-device-btn { background: #f7f7f7; border: 1px solid #ddd; border-radius: 6px; padding: 8px 18px; font-size: 1em; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.2s, border 0.2s; }
.hk-translate-device-btn.active, .hk-translate-device-btn:hover { background: #0073aa; color: #fff; border-color: #0073aa; }
.hk-translate-preview-wrapper { margin-top: 18px; display: flex; flex-direction: column; align-items: center; }
.hk-translate-preview-frame { transition: width 0.3s, height 0.3s; background: #222; position: relative; }
.hk-translate-website-mockup { min-height: 100%; }
.hk-translate-widget-preview { transition: left 0.3s, bottom 0.3s; }

/* ===== START: FRONTEND CSS REPLICA ===== */

/* Preview uses relative positioning for the dropdown */
#hkWidgetPreview .hk-translate-dropdown {
  position: relative !important;
  display: block;
}

/* Dropdown Button (Static values, controlled by JS) */
.hk-translate-btn {
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

/* Dropdown Menu (Static values, controlled by JS) */
.hk-translate-menu {
  position: absolute;
  z-index: 10000;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;
  transform: scale(0.95);
  overflow-y: auto;
  overflow-x: hidden;
  white-space: nowrap;
}

/* --- Menu Positioning (Viewport-aware positioning) --- */

/* Bottom Left - Menu opens upward to the right */
.hk-translate-dropdown.desktop-bottom-left .hk-translate-menu {
  bottom: calc(100% + 10px);
  left: 0;
  transform-origin: bottom left;
}

/* Bottom Right - Menu opens upward to the left */
.hk-translate-dropdown.desktop-bottom-right .hk-translate-menu {
  bottom: calc(100% + 10px);
  right: 0;
  transform-origin: bottom right;
}

/* Top Left - Menu opens downward to the right */
.hk-translate-dropdown.desktop-top-left .hk-translate-menu {
  top: calc(100% + 10px);
  left: 0;
  transform-origin: top left;
}

/* Top Right - Menu opens downward to the left */
.hk-translate-dropdown.desktop-top-right .hk-translate-menu {
  top: calc(100% + 10px);
  right: 0;
  transform-origin: top right;
}

/* Middle Left - Menu opens to the right and centered */
.hk-translate-dropdown.desktop-middle-left .hk-translate-menu {
  top: 50%;
  left: calc(100% + 10px);
  transform: translateY(-50%) scale(0.95);
  transform-origin: center left;
}

/* Middle Right - Menu opens to the left and centered */
.hk-translate-dropdown.desktop-middle-right .hk-translate-menu {
  top: 50%;
  right: calc(100% + 10px);
  transform: translateY(-50%) scale(0.95);
  transform-origin: center right;
}

/* --- Menu Open State --- */
.hk-translate-dropdown.open .hk-translate-menu {
  opacity: 1 !important;
  visibility: visible !important;
  transform: scale(1) !important;
}
.hk-translate-dropdown.desktop-middle-left.open .hk-translate-menu,
.hk-translate-dropdown.desktop-middle-right.open .hk-translate-menu {
    transform: translateY(-50%) scale(1) !important;
}

/* Menu Items */
.hk-translate-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  border-bottom: 1px solid #f0f0f0;
}
.hk-translate-item:last-child {
  border-bottom: none;
}
.hk-translate-item img {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  flex-shrink: 0;
  object-fit: cover;
  border: 1px solid #eee;
}
.hk-translate-item span {
  font-size: 14px;
  font-weight: 400;
}
.hk-translate-item.active {
  font-weight: 500;
}

/* Custom scrollbar */
.hk-translate-menu::-webkit-scrollbar { width: 5px; }
.hk-translate-menu::-webkit-scrollbar-track { background: #f1f1f1; }
.hk-translate-menu::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }
.hk-translate-menu::-webkit-scrollbar-thumb:hover { background: #888; }

/* ===== END: FRONTEND CSS REPLICA ===== */
</style>