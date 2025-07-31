<?php

/**
 * Language management functionality
 */

/**
 * The language management class.
 *
 * Defines all available languages, their properties, and management functions.
 */
class HK_Translate_Languages {

    /**
     * Get all available languages with their properties.
     *
     * @return array Array of language data
     */
    public static function get_all_languages() {
        $languages = array(
            'af' => array(
                'name' => __('Afrikaans', 'hk-translate'),
                'english_name' => 'Afrikaans',
                'flag' => 'af.svg',
                'code' => 'af',
                'rtl' => false
            ),
            'am' => array(
                'name' => __('አማርኛ', 'hk-translate'),
                'english_name' => 'Amharic',
                'flag' => 'am.svg',
                'code' => 'am',
                'rtl' => false
            ),
            'ar' => array(
                'name' => __('العربية', 'hk-translate'),
                'english_name' => 'Arabic',
                'flag' => 'ar.svg',
                'code' => 'ar',
                'rtl' => true
            ),
            'az' => array(
                'name' => __('Azərbaycan', 'hk-translate'),
                'english_name' => 'Azerbaijani',
                'flag' => 'az.svg',
                'code' => 'az',
                'rtl' => false
            ),
            'be' => array(
                'name' => __('Беларуская', 'hk-translate'),
                'english_name' => 'Belarusian',
                'flag' => 'be.svg',
                'code' => 'be',
                'rtl' => false
            ),
            'bg' => array(
                'name' => __('Български', 'hk-translate'),
                'english_name' => 'Bulgarian',
                'flag' => 'bg.svg',
                'code' => 'bg',
                'rtl' => false
            ),
            'bn' => array(
                'name' => __('বাংলা', 'hk-translate'),
                'english_name' => 'Bengali',
                'flag' => 'bn.svg',
                'code' => 'bn',
                'rtl' => false
            ),
            'bs' => array(
                'name' => __('Bosanski', 'hk-translate'),
                'english_name' => 'Bosnian',
                'flag' => 'bs.svg',
                'code' => 'bs',
                'rtl' => false
            ),
            'ca' => array(
                'name' => __('Català', 'hk-translate'),
                'english_name' => 'Catalan',
                'flag' => 'ca.svg',
                'code' => 'ca',
                'rtl' => false
            ),
            'co' => array(
                'name' => __('Corsu', 'hk-translate'),
                'english_name' => 'Corsican',
                'flag' => 'co.svg',
                'code' => 'co',
                'rtl' => false
            ),
            'cs' => array(
                'name' => __('Čeština', 'hk-translate'),
                'english_name' => 'Czech',
                'flag' => 'cs.svg',
                'code' => 'cs',
                'rtl' => false
            ),
            'cy' => array(
                'name' => __('Cymraeg', 'hk-translate'),
                'english_name' => 'Welsh',
                'flag' => 'cy.svg',
                'code' => 'cy',
                'rtl' => false
            ),
            'da' => array(
                'name' => __('Dansk', 'hk-translate'),
                'english_name' => 'Danish',
                'flag' => 'da.svg',
                'code' => 'da',
                'rtl' => false
            ),
            'de' => array(
                'name' => __('Deutsch', 'hk-translate'),
                'english_name' => 'German',
                'flag' => 'de.svg',
                'code' => 'de',
                'rtl' => false
            ),
            'el' => array(
                'name' => __('Ελληνικά', 'hk-translate'),
                'english_name' => 'Greek',
                'flag' => 'el.svg',
                'code' => 'el',
                'rtl' => false
            ),
            'en' => array(
                'name' => __('English', 'hk-translate'),
                'english_name' => 'English',
                'flag' => 'en-us.svg',
                'code' => 'en',
                'rtl' => false
            ),
            'eo' => array(
                'name' => __('Esperanto', 'hk-translate'),
                'english_name' => 'Esperanto',
                'flag' => 'eo.svg',
                'code' => 'eo',
                'rtl' => false
            ),
            'es' => array(
                'name' => __('Español', 'hk-translate'),
                'english_name' => 'Spanish',
                'flag' => 'es.svg',
                'code' => 'es',
                'rtl' => false
            ),
            'et' => array(
                'name' => __('Eesti', 'hk-translate'),
                'english_name' => 'Estonian',
                'flag' => 'et.svg',
                'code' => 'et',
                'rtl' => false
            ),
            'eu' => array(
                'name' => __('Euskera', 'hk-translate'),
                'english_name' => 'Basque',
                'flag' => 'eu.svg',
                'code' => 'eu',
                'rtl' => false
            ),
            'fa' => array(
                'name' => __('فارسی', 'hk-translate'),
                'english_name' => 'Persian',
                'flag' => 'fa.svg',
                'code' => 'fa',
                'rtl' => true
            ),
            'fi' => array(
                'name' => __('Suomi', 'hk-translate'),
                'english_name' => 'Finnish',
                'flag' => 'fi.svg',
                'code' => 'fi',
                'rtl' => false
            ),
            'fr' => array(
                'name' => __('Français', 'hk-translate'),
                'english_name' => 'French',
                'flag' => 'fr.svg',
                'code' => 'fr',
                'rtl' => false
            ),
            'fy' => array(
                'name' => __('Frysk', 'hk-translate'),
                'english_name' => 'Frisian',
                'flag' => 'fy.svg',
                'code' => 'fy',
                'rtl' => false
            ),
            'ga' => array(
                'name' => __('Gaeilge', 'hk-translate'),
                'english_name' => 'Irish',
                'flag' => 'ga.svg',
                'code' => 'ga',
                'rtl' => false
            ),
            'gd' => array(
                'name' => __('Gàidhlig', 'hk-translate'),
                'english_name' => 'Scottish Gaelic',
                'flag' => 'gd.svg',
                'code' => 'gd',
                'rtl' => false
            ),
            'gl' => array(
                'name' => __('Galego', 'hk-translate'),
                'english_name' => 'Galician',
                'flag' => 'gl.svg',
                'code' => 'gl',
                'rtl' => false
            ),
            'gu' => array(
                'name' => __('ગુજરાતી', 'hk-translate'),
                'english_name' => 'Gujarati',
                'flag' => 'gu.svg',
                'code' => 'gu',
                'rtl' => false
            ),
            'ha' => array(
                'name' => __('Hausa', 'hk-translate'),
                'english_name' => 'Hausa',
                'flag' => 'ha.svg',
                'code' => 'ha',
                'rtl' => false
            ),
            'haw' => array(
                'name' => __('ʻŌlelo Hawaiʻi', 'hk-translate'),
                'english_name' => 'Hawaiian',
                'flag' => 'haw.svg',
                'code' => 'haw',
                'rtl' => false
            ),
            'he' => array(
                'name' => __('עברית', 'hk-translate'),
                'english_name' => 'Hebrew',
                'flag' => 'iw.svg',
                'code' => 'he',
                'rtl' => true
            ),
            'hi' => array(
                'name' => __('हिन्दी', 'hk-translate'),
                'english_name' => 'Hindi',
                'flag' => 'hi.svg',
                'code' => 'hi',
                'rtl' => false
            ),
            'hr' => array(
                'name' => __('Hrvatski', 'hk-translate'),
                'english_name' => 'Croatian',
                'flag' => 'hr.svg',
                'code' => 'hr',
                'rtl' => false
            ),
            'ht' => array(
                'name' => __('Kreyòl Ayisyen', 'hk-translate'),
                'english_name' => 'Haitian Creole',
                'flag' => 'ht.svg',
                'code' => 'ht',
                'rtl' => false
            ),
            'hu' => array(
                'name' => __('Magyar', 'hk-translate'),
                'english_name' => 'Hungarian',
                'flag' => 'hu.svg',
                'code' => 'hu',
                'rtl' => false
            ),
            'hy' => array(
                'name' => __('Հայերեն', 'hk-translate'),
                'english_name' => 'Armenian',
                'flag' => 'hy.svg',
                'code' => 'hy',
                'rtl' => false
            ),
            'id' => array(
                'name' => __('Bahasa Indonesia', 'hk-translate'),
                'english_name' => 'Indonesian',
                'flag' => 'id.svg',
                'code' => 'id',
                'rtl' => false
            ),
            'ig' => array(
                'name' => __('Igbo', 'hk-translate'),
                'english_name' => 'Igbo',
                'flag' => 'ig.svg',
                'code' => 'ig',
                'rtl' => false
            ),
            'is' => array(
                'name' => __('Íslenska', 'hk-translate'),
                'english_name' => 'Icelandic',
                'flag' => 'is.svg',
                'code' => 'is',
                'rtl' => false
            ),
            'it' => array(
                'name' => __('Italiano', 'hk-translate'),
                'english_name' => 'Italian',
                'flag' => 'it.svg',
                'code' => 'it',
                'rtl' => false
            ),
            'ja' => array(
                'name' => __('日本語', 'hk-translate'),
                'english_name' => 'Japanese',
                'flag' => 'ja.svg',
                'code' => 'ja',
                'rtl' => false
            ),
            'jw' => array(
                'name' => __('Basa Jawa', 'hk-translate'),
                'english_name' => 'Javanese',
                'flag' => 'jw.svg',
                'code' => 'jw',
                'rtl' => false
            ),
            'ka' => array(
                'name' => __('ქართული', 'hk-translate'),
                'english_name' => 'Georgian',
                'flag' => 'ka.svg',
                'code' => 'ka',
                'rtl' => false
            ),
            'kk' => array(
                'name' => __('Қазақ', 'hk-translate'),
                'english_name' => 'Kazakh',
                'flag' => 'kk.svg',
                'code' => 'kk',
                'rtl' => false
            ),
            'km' => array(
                'name' => __('ខ្មែរ', 'hk-translate'),
                'english_name' => 'Khmer',
                'flag' => 'km.svg',
                'code' => 'km',
                'rtl' => false
            ),
            'kn' => array(
                'name' => __('ಕನ್ನಡ', 'hk-translate'),
                'english_name' => 'Kannada',
                'flag' => 'kn.svg',
                'code' => 'kn',
                'rtl' => false
            ),
            'ko' => array(
                'name' => __('한국어', 'hk-translate'),
                'english_name' => 'Korean',
                'flag' => 'ko.svg',
                'code' => 'ko',
                'rtl' => false
            ),
            'ku' => array(
                'name' => __('Kurdî', 'hk-translate'),
                'english_name' => 'Kurdish',
                'flag' => 'ku.svg',
                'code' => 'ku',
                'rtl' => false
            ),
            'ky' => array(
                'name' => __('Кыргызча', 'hk-translate'),
                'english_name' => 'Kyrgyz',
                'flag' => 'ky.svg',
                'code' => 'ky',
                'rtl' => false
            ),
            'la' => array(
                'name' => __('Latina', 'hk-translate'),
                'english_name' => 'Latin',
                'flag' => 'la.svg',
                'code' => 'la',
                'rtl' => false
            ),
            'lb' => array(
                'name' => __('Lëtzebuergesch', 'hk-translate'),
                'english_name' => 'Luxembourgish',
                'flag' => 'lb.svg',
                'code' => 'lb',
                'rtl' => false
            ),
            'lo' => array(
                'name' => __('ລາວ', 'hk-translate'),
                'english_name' => 'Lao',
                'flag' => 'lo.svg',
                'code' => 'lo',
                'rtl' => false
            ),
            'lt' => array(
                'name' => __('Lietuvių', 'hk-translate'),
                'english_name' => 'Lithuanian',
                'flag' => 'lt.svg',
                'code' => 'lt',
                'rtl' => false
            ),
            'lv' => array(
                'name' => __('Latviešu', 'hk-translate'),
                'english_name' => 'Latvian',
                'flag' => 'lv.svg',
                'code' => 'lv',
                'rtl' => false
            ),
            'mg' => array(
                'name' => __('Malagasy', 'hk-translate'),
                'english_name' => 'Malagasy',
                'flag' => 'mg.svg',
                'code' => 'mg',
                'rtl' => false
            ),
            'mi' => array(
                'name' => __('Te Reo Māori', 'hk-translate'),
                'english_name' => 'Maori',
                'flag' => 'mi.svg',
                'code' => 'mi',
                'rtl' => false
            ),
            'mk' => array(
                'name' => __('Македонски', 'hk-translate'),
                'english_name' => 'Macedonian',
                'flag' => 'mk.svg',
                'code' => 'mk',
                'rtl' => false
            ),
            'ml' => array(
                'name' => __('മലയാളം', 'hk-translate'),
                'english_name' => 'Malayalam',
                'flag' => 'ml.svg',
                'code' => 'ml',
                'rtl' => false
            ),
            'mn' => array(
                'name' => __('Монгол', 'hk-translate'),
                'english_name' => 'Mongolian',
                'flag' => 'mn.svg',
                'code' => 'mn',
                'rtl' => false
            ),
            'mr' => array(
                'name' => __('मराठी', 'hk-translate'),
                'english_name' => 'Marathi',
                'flag' => 'mr.svg',
                'code' => 'mr',
                'rtl' => false
            ),
            'ms' => array(
                'name' => __('Bahasa Melayu', 'hk-translate'),
                'english_name' => 'Malay',
                'flag' => 'ms.svg',
                'code' => 'ms',
                'rtl' => false
            ),
            'mt' => array(
                'name' => __('Malti', 'hk-translate'),
                'english_name' => 'Maltese',
                'flag' => 'mt.svg',
                'code' => 'mt',
                'rtl' => false
            ),
            'my' => array(
                'name' => __('မြန်မာ', 'hk-translate'),
                'english_name' => 'Myanmar (Burmese)',
                'flag' => 'my.svg',
                'code' => 'my',
                'rtl' => false
            ),
            'ne' => array(
                'name' => __('नेपाली', 'hk-translate'),
                'english_name' => 'Nepali',
                'flag' => 'ne.svg',
                'code' => 'ne',
                'rtl' => false
            ),
            'nl' => array(
                'name' => __('Nederlands', 'hk-translate'),
                'english_name' => 'Dutch',
                'flag' => 'nl.svg',
                'code' => 'nl',
                'rtl' => false
            ),
            'no' => array(
                'name' => __('Norsk', 'hk-translate'),
                'english_name' => 'Norwegian',
                'flag' => 'no.svg',
                'code' => 'no',
                'rtl' => false
            ),
            'ny' => array(
                'name' => __('Chichewa', 'hk-translate'),
                'english_name' => 'Chichewa',
                'flag' => 'ny.svg',
                'code' => 'ny',
                'rtl' => false
            ),
            'pa' => array(
                'name' => __('ਪੰਜਾਬੀ', 'hk-translate'),
                'english_name' => 'Punjabi',
                'flag' => 'pa.svg',
                'code' => 'pa',
                'rtl' => false
            ),
            'pl' => array(
                'name' => __('Polski', 'hk-translate'),
                'english_name' => 'Polish',
                'flag' => 'pl.svg',
                'code' => 'pl',
                'rtl' => false
            ),
            'ps' => array(
                'name' => __('پښتو', 'hk-translate'),
                'english_name' => 'Pashto',
                'flag' => 'ps.svg',
                'code' => 'ps',
                'rtl' => true
            ),
            'pt' => array(
                'name' => __('Português', 'hk-translate'),
                'english_name' => 'Portuguese',
                'flag' => 'pt.svg',
                'code' => 'pt',
                'rtl' => false
            ),
            'ro' => array(
                'name' => __('Română', 'hk-translate'),
                'english_name' => 'Romanian',
                'flag' => 'ro.svg',
                'code' => 'ro',
                'rtl' => false
            ),
            'ru' => array(
                'name' => __('Русский', 'hk-translate'),
                'english_name' => 'Russian',
                'flag' => 'ru.svg',
                'code' => 'ru',
                'rtl' => false
            ),
            'sd' => array(
                'name' => __('سنڌي', 'hk-translate'),
                'english_name' => 'Sindhi',
                'flag' => 'sd.svg',
                'code' => 'sd',
                'rtl' => true
            ),
            'si' => array(
                'name' => __('සිංහල', 'hk-translate'),
                'english_name' => 'Sinhala',
                'flag' => 'si.svg',
                'code' => 'si',
                'rtl' => false
            ),
            'sk' => array(
                'name' => __('Slovenčina', 'hk-translate'),
                'english_name' => 'Slovak',
                'flag' => 'sk.svg',
                'code' => 'sk',
                'rtl' => false
            ),
            'sl' => array(
                'name' => __('Slovenščina', 'hk-translate'),
                'english_name' => 'Slovenian',
                'flag' => 'sl.svg',
                'code' => 'sl',
                'rtl' => false
            ),
            'sm' => array(
                'name' => __('Gagana Samoa', 'hk-translate'),
                'english_name' => 'Samoan',
                'flag' => 'sm.svg',
                'code' => 'sm',
                'rtl' => false
            ),
            'sn' => array(
                'name' => __('ChiShona', 'hk-translate'),
                'english_name' => 'Shona',
                'flag' => 'sn.svg',
                'code' => 'sn',
                'rtl' => false
            ),
            'so' => array(
                'name' => __('Soomaali', 'hk-translate'),
                'english_name' => 'Somali',
                'flag' => 'so.svg',
                'code' => 'so',
                'rtl' => false
            ),
            'sq' => array(
                'name' => __('Shqip', 'hk-translate'),
                'english_name' => 'Albanian',
                'flag' => 'sq.svg',
                'code' => 'sq',
                'rtl' => false
            ),
            'sr' => array(
                'name' => __('Српски', 'hk-translate'),
                'english_name' => 'Serbian',
                'flag' => 'sr.svg',
                'code' => 'sr',
                'rtl' => false
            ),
            'st' => array(
                'name' => __('Sesotho', 'hk-translate'),
                'english_name' => 'Sesotho',
                'flag' => 'st.svg',
                'code' => 'st',
                'rtl' => false
            ),
            'su' => array(
                'name' => __('Basa Sunda', 'hk-translate'),
                'english_name' => 'Sundanese',
                'flag' => 'su.svg',
                'code' => 'su',
                'rtl' => false
            ),
            'sv' => array(
                'name' => __('Svenska', 'hk-translate'),
                'english_name' => 'Swedish',
                'flag' => 'sv.svg',
                'code' => 'sv',
                'rtl' => false
            ),
            'sw' => array(
                'name' => __('Kiswahili', 'hk-translate'),
                'english_name' => 'Swahili',
                'flag' => 'sw.svg',
                'code' => 'sw',
                'rtl' => false
            ),
            'ta' => array(
                'name' => __('தமிழ்', 'hk-translate'),
                'english_name' => 'Tamil',
                'flag' => 'ta.svg',
                'code' => 'ta',
                'rtl' => false
            ),
            'te' => array(
                'name' => __('తెలుగు', 'hk-translate'),
                'english_name' => 'Telugu',
                'flag' => 'te.svg',
                'code' => 'te',
                'rtl' => false
            ),
            'tg' => array(
                'name' => __('Тоҷикӣ', 'hk-translate'),
                'english_name' => 'Tajik',
                'flag' => 'tg.svg',
                'code' => 'tg',
                'rtl' => false
            ),
            'th' => array(
                'name' => __('ไทย', 'hk-translate'),
                'english_name' => 'Thai',
                'flag' => 'th.svg',
                'code' => 'th',
                'rtl' => false
            ),
            'tl' => array(
                'name' => __('Filipino', 'hk-translate'),
                'english_name' => 'Filipino',
                'flag' => 'tl.svg',
                'code' => 'tl',
                'rtl' => false
            ),
            'tr' => array(
                'name' => __('Türkçe', 'hk-translate'),
                'english_name' => 'Turkish',
                'flag' => 'tr.svg',
                'code' => 'tr',
                'rtl' => false
            ),
            'uk' => array(
                'name' => __('Українська', 'hk-translate'),
                'english_name' => 'Ukrainian',
                'flag' => 'uk.svg',
                'code' => 'uk',
                'rtl' => false
            ),
            'ur' => array(
                'name' => __('اردو', 'hk-translate'),
                'english_name' => 'Urdu',
                'flag' => 'ur.svg',
                'code' => 'ur',
                'rtl' => true
            ),
            'uz' => array(
                'name' => __('Oʻzbek', 'hk-translate'),
                'english_name' => 'Uzbek',
                'flag' => 'uz.svg',
                'code' => 'uz',
                'rtl' => false
            ),
            'vi' => array(
                'name' => __('Tiếng Việt', 'hk-translate'),
                'english_name' => 'Vietnamese',
                'flag' => 'vi.svg',
                'code' => 'vi',
                'rtl' => false
            ),
            'xh' => array(
                'name' => __('isiXhosa', 'hk-translate'),
                'english_name' => 'Xhosa',
                'flag' => 'xh.svg',
                'code' => 'xh',
                'rtl' => false
            ),
            'yi' => array(
                'name' => __('ייִדיש', 'hk-translate'),
                'english_name' => 'Yiddish',
                'flag' => 'yi.svg',
                'code' => 'yi',
                'rtl' => true
            ),
            'yo' => array(
                'name' => __('Yorùbá', 'hk-translate'),
                'english_name' => 'Yoruba',
                'flag' => 'yo.svg',
                'code' => 'yo',
                'rtl' => false
            ),
            'zh' => array(
                'name' => __('中文', 'hk-translate'),
                'english_name' => 'Chinese',
                'flag' => 'zh-CN.svg',
                'code' => 'zh',
                'rtl' => false
            ),
            'zu' => array(
                'name' => __('isiZulu', 'hk-translate'),
                'english_name' => 'Zulu',
                'flag' => 'zu.svg',
                'code' => 'zu',
                'rtl' => false
            )
        );

        // Alfabetik sıralama - English name'e göre
        uasort($languages, function($a, $b) {
            return strcmp($a['english_name'], $b['english_name']);
        });

        return $languages;
    }

    /**
     * Get enabled languages based on admin settings.
     *
     * @return array Array of enabled language data
     */
    public static function get_enabled_languages() {
        $settings = get_option('hk_translate_settings', array());
        $enabled_codes = isset($settings['enabled_languages']) ? $settings['enabled_languages'] : array('tr', 'en');
        $all_languages = self::get_all_languages();
        $enabled_languages = array();

        foreach ($enabled_codes as $code) {
            if (isset($all_languages[$code])) {
                $enabled_languages[$code] = $all_languages[$code];
            }
        }

        return $enabled_languages;
    }

    /**
     * Check if a language code is valid.
     *
     * @param string $code Language code to validate
     * @return bool True if valid, false otherwise
     */
    public static function is_valid_language($code) {
        $all_languages = self::get_all_languages();
        return isset($all_languages[$code]);
    }

    /**
     * Check if a language is RTL (Right-to-Left).
     *
     * @param string $code Language code
     * @return bool True if RTL, false otherwise
     */
    public static function is_rtl_language($code) {
        $all_languages = self::get_all_languages();
        return isset($all_languages[$code]) && $all_languages[$code]['rtl'];
    }

    /**
     * Get flag URL for a language.
     *
     * @param string $code Language code
     * @return string Flag URL
     */
    public static function get_flag_url($code) {
        $all_languages = self::get_all_languages();
        if (isset($all_languages[$code])) {
            return HK_TRANSLATE_PLUGIN_URL . 'public/flags/svg/' . $all_languages[$code]['flag'];
        }
        return '';
    }

    /**
     * Get language name.
     *
     * @param string $code Language code
     * @return string Language name
     */
    public static function get_language_name($code) {
        $all_languages = self::get_all_languages();
        return isset($all_languages[$code]) ? $all_languages[$code]['name'] : '';
    }

    /**
     * Get language English name.
     *
     * @param string $code Language code
     * @return string Language English name
     */
    public static function get_language_english_name($code) {
        $all_languages = self::get_all_languages();
        return isset($all_languages[$code]) ? $all_languages[$code]['english_name'] : '';
    }

    /**
     * Validate and sanitize enabled languages array.
     *
     * @param array $languages Array of language codes
     * @return array Sanitized array of valid language codes
     */
    public static function validate_enabled_languages($languages) {
        if (!is_array($languages)) {
            return array('tr', 'en'); // Default fallback
        }

        $valid_languages = array();
        $all_languages = self::get_all_languages();

        foreach ($languages as $code) {
            $code = sanitize_text_field($code);
            if (isset($all_languages[$code])) {
                $valid_languages[] = $code;
            }
        }

        // Ensure at least one language is enabled
        if (empty($valid_languages)) {
            $valid_languages = array('tr', 'en');
        }

        return array_unique($valid_languages);
    }
}