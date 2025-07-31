=== HK Translate ===
Contributors: huseyinkurklu
Author: Hüseyin Kürklü
Author URI: https://huseyinkurklu.com.tr/
Tags: translate, translation, google translate, multilingual, language, auto-detect, flags, drag-drop, ordering
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Smart translation system with Google Translate API-based customizable translation plugin for WordPress sites.

== Description ==

HK Translate is a next-generation smart translation plugin for WordPress sites using Google Translate API. It automatically detects your site language and optimizes user experience.

**⭐ NEXT-GENERATION FEATURES:**

🧠 **Smart Detection System**
* **Site Language Auto-Detection** - Automatically detects your WordPress site language
* **Browser Language Recognition** - Detects user's browser language
* **User Preference Memory** - Remembers preferences with localStorage
* **One-time Detection** - Prevents infinite loops

🌍 **Comprehensive Language Support (70+ Languages)**
* **9 Essential Languages Auto-Active**: Site language + English, German, French, Italian, Spanish, Portuguese, Russian, Arabic, Turkish
* **Flagged Visual Menu** - SVG quality flag icons
* **Language Priority System** - Site language always comes first
* **Drag & Drop Language Ordering** - Reorder languages with intuitive drag-and-drop interface

⚙️ **Advanced Admin Panel**
* **Dedicated Admin Menu** - Not embedded in Settings menu, has its own menu
* **Language Count Badge** - Shows active language count in menu
* **Real-time Preview** - See changes instantly
* **Smart Validation** - Prevents incorrect settings
* **Device-Specific Positioning** - Separate position settings for Desktop/Tablet/Mobile

📱 **Responsive & Visual Design**
* **6 Position Options** - Bottom/Middle/Top + Left/Right
* **Device-Based Settings** - Separate positions for Desktop/Tablet/Mobile
* **Customizable Styles** - Colors, borders, radius settings
* **Visual Settings Panel** - Easy customization with color palette
* **Negative Position Support** - Use negative values for advanced positioning (-150px to +200px)

🚀 **Performance & Security**
* **Lightweight Code Structure** - Minimum resource usage
* **WordPress Standards** - Security and performance best practices
* **AJAX-Based Settings** - Save without page refresh
* **Cache Friendly** - Compatible with caching plugins

**SUPPORTED LANGUAGES (104 Languages):**
Afrikaans (Afrikaans), Amharic (አማርኛ), Arabic (العربية), Azerbaijani (Azərbaycan), Belarusian (Беларуская), Bulgarian (Български), Bengali (বাংলা), Bosnian (Bosanski), Catalan (Català), Cebuano (Cebuano), Corsican (Corsu), Czech (Čeština), Welsh (Cymraeg), Danish (Dansk), German (Deutsch), Greek (Ελληνικά), English (English), Esperanto (Esperanto), Spanish (Español), Estonian (Eesti), Basque (Euskera), Persian (فارسی), Finnish (Suomi), French (Français), Western Frisian (Frysk), Irish (Gaeilge), Scottish Gaelic (Gàidhlig), Galician (Galego), Gujarati (ગુજરાતી), Hausa (Hausa), Hawaiian (ʻŌlelo Hawaiʻi), Hindi (हिन्दी), Hmong (Hmoob), Croatian (Hrvatski), Haitian Creole (Kreyòl Ayisyen), Hungarian (Magyar), Armenian (Հայերեն), Indonesian (Bahasa Indonesia), Igbo (Igbo), Icelandic (Íslenska), Italian (Italiano), Hebrew (עברית), Japanese (日本語), Javanese (Basa Jawa), Georgian (ქართული), Kazakh (Қазақ), Khmer (ខ្មែរ), Kannada (ಕನ್ನಡ), Korean (한국어), Kurdish (Kurdî), Kyrgyz (Кыргызча), Latin (Latina), Luxembourgish (Lëtzebuergesch), Lao (ລາວ), Lithuanian (Lietuvių), Latvian (Latviešu), Malagasy (Malagasy), Maori (Te Reo Māori), Macedonian (Македонски), Malayalam (മലയാളം), Mongolian (Монгол), Marathi (मराठी), Malay (Bahasa Melayu), Maltese (Malti), Myanmar (မြန်မာ), Nepali (नेपाली), Dutch (Nederlands), Norwegian (Norsk), Nyanja (Chichewa), Punjabi (ਪੰਜਾਬੀ), Polish (Polski), Pashto (پښتو), Portuguese (Português), Romanian (Română), Russian (Русский), Sindhi (سنڌي), Sinhala (සිංහල), Slovak (Slovenčina), Slovenian (Slovenščina), Samoan (Gagana Samoa), Shona (ChiShona), Somali (Soomaali), Albanian (Shqip), Serbian (Српски), Sesotho (Sesotho), Sundanese (Basa Sunda), Swedish (Svenska), Swahili (Kiswahili), Tamil (தமிழ்), Telugu (తెలుగు), Tajik (Тоҷикӣ), Thai (ไทย), Filipino (Filipino), Turkish (Türkçe), Ukrainian (Українська), Urdu (اردو), Uzbek (O'zbek), Vietnamese (Tiếng Việt), Xhosa (isiXhosa), Yiddish (ייִדיש), Yoruba (Yorùbá), Chinese Simplified (简体中文), Chinese Traditional (繁體中文), Zulu (isiZulu).

== Installation ==

**Automatic Installation:**
1. Go to 'Plugins' > 'Add New' in WordPress admin panel
2. Search for "HK Translate" and click 'Install Now'
3. When installation is complete, click 'Activate'
4. Configure settings by selecting 'HK Translate' from the left menu

**Manual Installation:**
1. Upload plugin files to `/wp-content/plugins/hk-translate/` directory
2. Activate the plugin from 'Plugins' menu in WordPress admin panel
3. Configure settings from 'HK Translate' menu

**After First Installation:**
✅ Your site language is automatically detected
✅ 9 essential languages are automatically activated
✅ Translation button appears in bottom-right corner
✅ Browser language auto-detection is active

== Frequently Asked Questions ==

= Is the plugin completely free? =

Yes, HK Translate is completely free and open source. There are no premium features or limitations.

= How many languages are supported? =

It supports 70+ languages supported by Google Translate API. It's continuously updated.

= Is my site language automatically detected? =

Yes! It automatically detects your site language using WordPress's `get_locale()` function and sets it as the default language.

= Are user language preferences remembered? =

Yes, it remembers user preferences using localStorage. This way the same language selection is preserved on each visit.

= Where does the translation button appear? =

By default, it appears in the bottom-right corner. There are 6 different position options: top/middle/bottom + left/right.

= Can I set different positions for mobile devices? =

Yes! You can set separate position settings for Desktop, Tablet, and Mobile.

= Where is the admin menu? =

It's not under the Settings menu, it has its own dedicated menu. It appears as "HK Translate" in the left menu and shows the active language count.

= Will I have problems with RTL languages (Arabic, Hebrew)? =

No, there are protection mechanisms to prevent your site layout from breaking in RTL languages.

= How does the preview feature work? =

You can see the changes you make in the admin panel in real-time. You can test how it will look before saving.

= Can I reorder languages? =

Yes! You can reorder languages using the intuitive drag-and-drop interface in the admin panel. The language order is preserved and affects the display order in the frontend widget.

= What WordPress versions are supported? =

WordPress 5.0+ and PHP 7.4+ are required. It has been tested up to WordPress 6.4.

= Is it compatible with caching plugins? =

Yes, it's compatible with popular caching plugins like WP Rocket, W3 Total Cache, WP Super Cache.

== Screenshots ==

1. **Admin Panel Main Page** - Language selection and smart detection settings
2. **Position Settings** - 6 different position options and device-based settings
3. **Visual Customization** - Color palette and style settings
4. **Real-time Preview** - See changes instantly
5. **Language Ordering** - Drag & drop language reordering interface
6. **Frontend Translation Button** - Desktop view
7. **Open Translation Menu** - Flagged language list
8. **Mobile Responsive Design** - Tablet and phone view
9. **Admin Menu Badge** - Active language count indicator

== Changelog ==

= 1.0.0 =
🎉 **Initial Release**

**🧠 Smart Features:**
* Site language auto-detection
* Browser language recognition system
* User preference memory (localStorage)
* One-time detection logic

**🌍 Language & Visual:**
* 70+ language support
* SVG flag icons
* 9 essential languages auto-activation
* Language priority system
* Drag & drop language ordering

**⚙️ Admin Panel:**
* Dedicated admin menu
* Language count badge system
* Real-time preview
* AJAX-based settings save
* Device-specific positioning
* Negative position value support

**📱 Responsive Design:**
* 6 position options
* Device-based separate settings
* Advanced visual customization
* CSS custom properties

**🚀 Performance:**
* WordPress coding standards
* Security best practices
* Minimum resource usage
* Caching compatibility

== Upgrade Notice ==

= 1.0.0 =
🎉 Initial release! Meet the smart translation system that automatically detects your site language.

== Technical Requirements ==

* **WordPress:** 5.0 or higher
* **PHP:** 7.4 or higher
* **JavaScript:** ES6 compatible browsers
* **API:** Google Translate Web Widget v2.0+

== Developer Information ==

**Hooks & Filters:**
* `hk_translate_enabled_languages` - Filter active languages
* `hk_translate_default_language` - Change default language
* `hk_translate_widget_position` - Customize widget position

**CSS Custom Properties:**
* All style variables with `--hk-translate-*` prefix
* Responsive breakpoints included
* Dark mode ready infrastructure

== Support ==

**Support Channels:**
* WordPress.org Support Forum
* GitHub Issues (github.com/huseyinkurklu/hk-translate)
* Email: support@hktranslate.com

**Documentation:**
* Installation guide
* Developer API reference
* Customization examples

== Privacy & GDPR ==

**Data Processing:**
* Translation is done through Google Translate API
* User language preferences are stored in localStorage (local)
* No personal data is sent to server
* GDPR compliant design

**Google Services:**
This plugin uses Google Translate Widget API. During translation, page content is sent to Google servers. Please review Google's privacy policy: https://policies.google.com/privacy

== Credits & Acknowledgments ==

* **Google Translate API** - Translation engine
* **Flag Icons** - Custom SVG flag set (70+ countries)
* **WordPress Community** - Best practices and standards
* **Contributors** - Those who provided testing and feedback

**Open Source Libraries:**
* WordPress Coding Standards
* CSS Custom Properties
* JavaScript ES6+ Features

== Installation ==

**Automatic Installation:**
1. Go to 'Plugins' > 'Add New' in WordPress admin panel
2. Search for "HK Translate" and click 'Install Now'
3. When installation is complete, click 'Activate'
4. Configure settings by selecting 'HK Translate' from the left menu

**Manual Installation:**
1. Upload plugin files to `/wp-content/plugins/hk-translate/` directory
2. Activate the plugin from 'Plugins' menu in WordPress admin panel
3. Configure settings from 'HK Translate' menu

**After First Installation:**
✅ Your site language is automatically detected
✅ 9 essential languages are automatically activated
✅ Translation button appears in bottom-right corner
✅ Browser language auto-detection is active

== Frequently Asked Questions ==

= Is the plugin completely free? =

Yes, HK Translate is completely free and open source. There are no premium features or limitations.

= How many languages are supported? =

It supports 70+ languages supported by Google Translate API. It's continuously updated.

= Is my site language automatically detected? =

Yes! It automatically detects your site language using WordPress's `get_locale()` function and sets it as the default language.

= Are user language preferences remembered? =

Yes, it remembers user preferences using localStorage. This way the same language selection is preserved on each visit.

= Where does the translation button appear? =

By default, it appears in the bottom-right corner. There are 6 different position options: top/middle/bottom + left/right.

= Can I set different positions for mobile devices? =

Yes! You can set separate position settings for Desktop, Tablet, and Mobile.

= Where is the admin menu? =

It's not under the Settings menu, it has its own dedicated menu. It appears as "HK Translate" in the left menu and shows the active language count.

= Will I have problems with RTL languages (Arabic, Hebrew)? =

No, there are protection mechanisms to prevent your site layout from breaking in RTL languages.

= How does the preview feature work? =

You can see the changes you make in the admin panel in real-time. You can test how it will look before saving.

= Can I reorder languages? =

Yes! You can reorder languages using the intuitive drag-and-drop interface in the admin panel. The language order is preserved and affects the display order in the frontend widget.

= What WordPress versions are supported? =

WordPress 5.0+ and PHP 7.4+ are required. It has been tested up to WordPress 6.4.

= Is it compatible with caching plugins? =

Yes, it's compatible with popular caching plugins like WP Rocket, W3 Total Cache, WP Super Cache.

== Screenshots ==

1. **Admin Panel Main Page** - Language selection and smart detection settings
2. **Position Settings** - 6 different position options and device-based settings
3. **Visual Customization** - Color palette and style settings
4. **Real-time Preview** - See changes instantly
5. **Language Ordering** - Drag & drop language reordering interface
6. **Frontend Translation Button** - Desktop view
7. **Open Translation Menu** - Flagged language list
8. **Mobile Responsive Design** - Tablet and phone view
9. **Admin Menu Badge** - Active language count indicator

== Changelog ==

= 1.0.0 =
🎉 **Initial Release**

**🧠 Smart Features:**
* Site language auto-detection
* Browser language recognition system
* User preference memory (localStorage)
* One-time detection logic

**🌍 Language & Visual:**
* 70+ language support
* SVG flag icons
* 9 essential languages auto-activation
* Language priority system
* Drag & drop language ordering

**⚙️ Admin Panel:**
* Dedicated admin menu
* Language count badge system
* Real-time preview
* AJAX-based settings save
* Device-specific positioning
* Negative position value support

**📱 Responsive Design:**
* 6 position options
* Device-based separate settings
* Advanced visual customization
* CSS custom properties

**🚀 Performance:**
* WordPress coding standards
* Security best practices
* Minimum resource usage
* Caching compatibility

== Upgrade Notice ==

= 1.0.0 =
🎉 Initial release! Meet the smart translation system that automatically detects your site language.

== Technical Requirements ==

* **WordPress:** 5.0 or higher
* **PHP:** 7.4 or higher
* **JavaScript:** ES6 compatible browsers
* **API:** Google Translate Web Widget v2.0+

== Developer Information ==

**Hooks & Filters:**
* `hk_translate_enabled_languages` - Filter active languages
* `hk_translate_default_language` - Change default language
* `hk_translate_widget_position` - Customize widget position

**CSS Custom Properties:**
* All style variables with `--hk-translate-*` prefix
* Responsive breakpoints included
* Dark mode ready infrastructure

== Privacy & GDPR ==

**Data Processing:**
* Translation is done through Google Translate API
* User language preferences are stored in localStorage (local)
* No personal data is sent to server
* GDPR compliant design

**Google Services:**
This plugin uses Google Translate Widget API. During translation, page content is sent to Google servers. Please review Google's privacy policy: https://policies.google.com/privacy

== Credits & Acknowledgments ==

* **Google Translate API** - Translation engine
* **Flag Icons** - Custom SVG flag set (70+ countries)
* **WordPress Community** - Best practices and standards
* **Contributors** - Those who provided testing and feedback

**Open Source Libraries:**
* WordPress Coding Standards
* CSS Custom Properties
* JavaScript ES6+ Features