(function () {
  "use strict";
  // Preview mode support - now using device-specific positions
  window.updateHKTranslateSettings = function (settings, positions) {
    // Update widget position with device-aware classes
    const dropdown = document.getElementById("hkTranslateDropdown");
    if (dropdown && positions) {
      // Remember if dropdown was open before position change
      const wasOpen = dropdown.classList.contains("open");
      // Detect current device based on viewport
      const width = window.innerWidth;
      let devicePrefix = "";
      let actualPosition = "bottom-right"; // default
      if (width <= 480) {
        devicePrefix = "mobile-";
        actualPosition =
          positions.mobile_position ||
          positions.desktop_position ||
          "bottom-right";
      } else if (width <= 768) {
        devicePrefix = "tablet-";
        actualPosition =
          positions.tablet_position ||
          positions.desktop_position ||
          "bottom-right";
      } else {
        devicePrefix = "desktop-";
        actualPosition = positions.desktop_position || "bottom-right";
      }
      // Apply device-specific classes while preserving open state
      // KEEP ALL DEVICE CLASSES - Controlled by CSS media queries
      const allDeviceClasses = `hk-translate-dropdown desktop-${
        positions.desktop_position || "bottom-right"
      } tablet-${positions.tablet_position || "bottom-right"} mobile-${
        positions.mobile_position || "bottom-right"
      }`;
      dropdown.className = wasOpen
        ? `${allDeviceClasses} open`
        : allDeviceClasses;
      // TRANSFORM CLEANUP: Reset any inline transforms that might interfere
      const menu = dropdown.querySelector(".hk-translate-menu");
      if (menu) {
        // Clear any lingering transforms from previous positions
        menu.style.transform = "";
        // CLEAR ALL FORCED STYLES - Sadece CSS ile kontrol et
        menu.style.opacity = "";
        menu.style.visibility = "";
        menu.style.display = "";
        menu.style.background = "";
        menu.style.zIndex = "";
        menu.classList.remove("force-visible");
      }
    }
    // Update dynamic CSS
    updateDynamicStyles(settings);
  };
  // Dynamic CSS update function
  function updateDynamicStyles(settings) {
    // Remove existing dynamic styles
    const existingStyle = document.getElementById(
      "hk-translate-dynamic-styles"
    );
    if (existingStyle) {
      existingStyle.remove();
    }
    // Create new style element
    const style = document.createElement("style");
    style.id = "hk-translate-dynamic-styles";
    const css = `
      :root {
        --hk-translate-btn-bg: ${
          settings.btn_bg_enabled ? settings.btn_bg_color : "transparent"
        };
        --hk-translate-btn-border: ${
          settings.btn_border_enabled
            ? `${settings.btn_border_width}px solid ${settings.btn_border_color}`
            : "none"
        };
        --hk-translate-btn-radius: ${settings.btn_border_radius}%;
        --hk-translate-btn-flag-radius: ${settings.btn_flag_radius}%;
        --hk-translate-btn-hover-color: ${settings.btn_hover_color};
        --hk-translate-menu-bg: ${settings.menu_bg_color};
        --hk-translate-menu-border: ${
          settings.menu_border_enabled
            ? `${settings.menu_border_width}px solid ${settings.menu_border_color}`
            : "none"
        };
        --hk-translate-menu-radius: ${settings.menu_border_radius}px;
        --hk-translate-menu-width: ${settings.menu_width}px;
        --hk-translate-menu-text: ${settings.menu_text_color};
        --hk-translate-menu-hover-bg: ${settings.menu_hover_bg_color};
        --hk-translate-menu-hover-text: ${settings.menu_hover_text_color};
      }
    `;
    style.textContent = css;
    document.head.appendChild(style);
  }
  // Google Translate Initialization
  function googleTranslateElementInit2() {
    new google.translate.TranslateElement(
      {
        pageLanguage: "tr",
        autoDisplay: false,
      },
      "google_translate_element2"
    );
  }
  // Google Translate Script Loading
  if (!window.gt_translate_script) {
    window.gt_translate_script = document.createElement("script");
    gt_translate_script.src =
      "https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2";
    document.body.appendChild(gt_translate_script);
  }
  // Google Translate Functions
  function GTranslateGetCurrentLang() {
    var keyValue = document["cookie"].match("(^|;) ?googtrans=([^;]*)(;|$)");
    return keyValue ? keyValue[2].split("/")[2] : null;
  }
  function GTranslateFireEvent(element, event) {
    try {
      if (document.createEventObject) {
        var evt = document.createEventObject();
        element.fireEvent("on" + event, evt);
      } else {
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(event, true, true);
        element.dispatchEvent(evt);
      }
    } catch (e) {}
  }
  function doGTranslate(lang_pair) {
    if (lang_pair.value) lang_pair = lang_pair.value;
    if (lang_pair == "") return;
    var lang = lang_pair.split("|")[1];
    if (GTranslateGetCurrentLang() == null && lang == lang_pair.split("|")[0])
      return;
    if (typeof ga == "function") {
      ga(
        "send",
        "event",
        "GTranslate",
        lang,
        location.hostname + location.pathname + location.search
      );
    }
    var teCombo;
    var sel = document.getElementsByTagName("select");
    for (var i = 0; i < sel.length; i++) {
      if (sel[i].className.indexOf("goog-te-combo") != -1) {
        teCombo = sel[i];
        break;
      }
    }
    if (
      document.getElementById("google_translate_element2") == null ||
      document.getElementById("google_translate_element2").innerHTML.length ==
        0 ||
      teCombo.length == 0 ||
      teCombo.innerHTML.length == 0
    ) {
      setTimeout(function () {
        doGTranslate(lang_pair);
      }, 500);
    } else {
      teCombo.value = lang;
      GTranslateFireEvent(teCombo, "change");
      GTranslateFireEvent(teCombo, "change");

      // After Google Translate loads, apply manual translations
      setTimeout(function () {
        loadAndApplyManualTranslations(lang);
      }, 2000);
    }
  }
  // Dropdown Toggle - SIMPLE SYSTEM
  window.toggleHKTranslateDropdown = function () {
    const dropdown = document.getElementById("hkTranslateDropdown");
    if (dropdown) {
      // Basit toggle
      dropdown.classList.toggle("open");
      const isOpen = dropdown.classList.contains("open");
    } else {
    }
  };
  // Dil Seçimi
  window.selectHKLanguage = function (
    langCode,
    shortCode,
    langName,
    defaultLang
  ) {
    // Mark that user manually changed language (disable auto-detect)
    userManuallyChanged = true;
    // Save user language preference
    setUserLanguagePreference(langCode);
    // RTL dil kontrolü
    const rtlLanguages = ["ar", "he", "fa", "ur", "ps", "sd", "yi"];
    // Eğer seçilen dil varsayılan dil ise, çeviri yapma
    if (langCode === defaultLang) {
      // Clear translation cookie
      document.cookie =
        "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      // Sadece UI'yi güncelle
      document.getElementById("hkCurrentFlag").src = getLocalFlagUrl(langCode);
      document.getElementById("hkCurrentFlag").alt = langName;
      // Aktif öğeleri işaretle
      document.querySelectorAll(".hk-translate-item").forEach((item) => {
        item.classList.remove("active");
      });
      document
        .querySelector(
          `[data-lang="${langCode === "en-us" ? "en" : langCode}"]`
        )
        .classList.add("active");
      // Dropdown'ı kapat
      document.getElementById("hkTranslateDropdown").classList.remove("open");
      // RTL class'ını kaldır
      document.body.classList.remove("hk-translate-rtl-active");
      // Reload to show original content
      window.location.reload();
      return false;
    }
    // RTL dil seçildiyse body'ye class ekle
    if (rtlLanguages.includes(langCode)) {
      document.body.classList.add("hk-translate-rtl-active");
    } else {
      document.body.classList.remove("hk-translate-rtl-active");
    }
    // Butonları güncelle
    document.getElementById("hkCurrentFlag").src = getLocalFlagUrl(langCode);
    document.getElementById("hkCurrentFlag").alt = langName;
    // Aktif öğeleri işaretle
    document.querySelectorAll(".hk-translate-item").forEach((item) => {
      item.classList.remove("active");
    });
    document
      .querySelector(`[data-lang="${langCode === "en-us" ? "en" : langCode}"]`)
      .classList.add("active");
    // Dropdown'ı kapat
    document.getElementById("hkTranslateDropdown").classList.remove("open");
    // Google Translate tetikle
    const targetLang = langCode === "en-us" ? "en" : langCode;
    doGTranslate(`tr|${targetLang}`);
    return false;
  };
  // Update Button Flag Function
  function updateButtonFlag(langCode, langName) {
    try {
      const currentFlag = document.getElementById("hkCurrentFlag");
      if (currentFlag) {
        currentFlag.src = getLocalFlagUrl(langCode);
        currentFlag.alt = langName;
      }
    } catch (error) {
      // Silently fail in production
    }
  }
  // Get local flag URL
  function getLocalFlagUrl(langCode) {
    // Plugin URL'ini localized değişkenden al
    let pluginUrl = "";
    if (
      typeof hk_translate_settings !== "undefined" &&
      hk_translate_settings.plugin_url
    ) {
      pluginUrl = hk_translate_settings.plugin_url + "public/flags/svg/";
    } else {
      // Fallback - script tag'inden al
      const scripts = document.getElementsByTagName("script");
      for (let i = 0; i < scripts.length; i++) {
        if (
          scripts[i].src &&
          scripts[i].src.includes("hk-translate-script.js")
        ) {
          pluginUrl = scripts[i].src.replace(
            "/public/js/hk-translate-script.js",
            "/public/flags/svg/"
          );
          break;
        }
      }
      // Son fallback
      if (!pluginUrl) {
        pluginUrl =
          window.location.origin +
          "/wp-content/plugins/hk-translate/public/flags/svg/";
      }
    }
    return pluginUrl + getLanguageFlag(langCode);
  }
  // Get language flag filename
  function getLanguageFlag(langCode) {
    const flagMap = {
      af: "af.svg",
      am: "am.svg",
      ar: "ar.svg",
      az: "az.svg",
      be: "be.svg",
      bg: "bg.svg",
      bn: "bn.svg",
      bs: "bs.svg",
      ca: "ca.svg",
      co: "co.svg",
      cs: "cs.svg",
      cy: "cy.svg",
      da: "da.svg",
      de: "de.svg",
      el: "el.svg",
      en: "en-us.svg",
      eo: "eo.svg",
      es: "es.svg",
      et: "et.svg",
      eu: "eu.svg",
      fa: "fa.svg",
      fi: "fi.svg",
      fr: "fr.svg",
      fy: "fy.svg",
      ga: "ga.svg",
      gd: "gd.svg",
      gl: "gl.svg",
      gu: "gu.svg",
      ha: "ha.svg",
      haw: "haw.svg",
      he: "iw.svg",
      hi: "hi.svg",
      hr: "hr.svg",
      ht: "ht.svg",
      hu: "hu.svg",
      hy: "hy.svg",
      id: "id.svg",
      ig: "ig.svg",
      is: "is.svg",
      it: "it.svg",
      ja: "ja.svg",
      jw: "jw.svg",
      ka: "ka.svg",
      kk: "kk.svg",
      km: "km.svg",
      kn: "kn.svg",
      ko: "ko.svg",
      ku: "ku.svg",
      ky: "ky.svg",
      la: "la.svg",
      lb: "lb.svg",
      lo: "lo.svg",
      lt: "lt.svg",
      lv: "lv.svg",
      mg: "mg.svg",
      mi: "mi.svg",
      mk: "mk.svg",
      ml: "ml.svg",
      mn: "mn.svg",
      mr: "mr.svg",
      ms: "ms.svg",
      mt: "mt.svg",
      my: "my.svg",
      ne: "ne.svg",
      nl: "nl.svg",
      no: "no.svg",
      ny: "ny.svg",
      pa: "pa.svg",
      pl: "pl.svg",
      ps: "ps.svg",
      pt: "pt.svg",
      ro: "ro.svg",
      ru: "ru.svg",
      sd: "sd.svg",
      si: "si.svg",
      sk: "sk.svg",
      sl: "sl.svg",
      sm: "sm.svg",
      sn: "sn.svg",
      so: "so.svg",
      sq: "sq.svg",
      sr: "sr.svg",
      st: "st.svg",
      su: "su.svg",
      sv: "sv.svg",
      sw: "sw.svg",
      ta: "ta.svg",
      te: "te.svg",
      tg: "tg.svg",
      th: "th.svg",
      tl: "tl.svg",
      tr: "tr.svg",
      uk: "uk.svg",
      ur: "ur.svg",
      uz: "uz.svg",
      vi: "vi.svg",
      xh: "xh.svg",
      yi: "yi.svg",
      yo: "yo.svg",
      zh: "zh-CN.svg",
      zu: "zu.svg",
    };
    return flagMap[langCode] || "tr.svg";
  }
  // Dışarı tıklamada dropdown'ı kapat
  document.addEventListener("click", function (event) {
    const dropdown = document.getElementById("hkTranslateDropdown");
    if (dropdown && !dropdown.contains(event.target)) {
      dropdown.classList.remove("open");
      // Clean up forced styles
      const menu = dropdown.querySelector(".hk-translate-menu");
      if (menu) {
        menu.style.opacity = "";
        menu.style.visibility = "";
        menu.style.display = "";
        menu.style.background = "";
        menu.style.zIndex = "";
        menu.classList.remove("force-visible");
      }
    }
  });
  // Alternative jQuery-based click handler (backup)
  document.addEventListener("DOMContentLoaded", function () {
    const button = document.querySelector(".hk-translate-btn");
    if (button) {
      // Remove any existing onclick and add event listener
      button.removeAttribute("onclick");
      button.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        window.toggleHKTranslateDropdown();
      });
    }
  });
  // Sayfa yüklendiğinde aktif dili belirle
  document.addEventListener("DOMContentLoaded", function () {
    // ...existing code...
    const rtlLanguages = ["ar", "he", "fa", "ur", "ps", "sd", "yi"];
    const currentLang = GTranslateGetCurrentLang();
    const userPreference = getUserLanguagePreference();
    // Default language from settings
    const defaultLanguage =
      typeof hk_translate_settings !== "undefined" &&
      hk_translate_settings.default_language
        ? hk_translate_settings.default_language
        : "tr";
    // Check if user has a saved preference
    if (userPreference) {
      if (userPreference === defaultLanguage) {
        // User prefers default language - no translation
        if (currentLang && currentLang !== defaultLanguage) {
          document.cookie =
            "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
          window.location.reload();
          return;
        }
      } else {
        // User prefers a translated language
        if (currentLang !== userPreference) {
          setTimeout(function () {
            doGTranslate(`${defaultLanguage}|${userPreference}`);
            setTimeout(() => {
              updateButtonFlag(userPreference, userPreference.toUpperCase());
            }, 500);
          }, 1500);
          return;
        }
      }
      // Mark that user has made a choice (don't auto-detect)
      userManuallyChanged = true;
      autoDetectCompleted = true;
    }
    // Auto-detect browser language if enabled, no user preference, and not already translated
    if (
      typeof hk_translate_settings !== "undefined" &&
      hk_translate_settings.auto_detect_language &&
      !currentLang &&
      !userPreference
    ) {
      // ...existing code...
      // Wait for Google Translate to initialize properly
      setTimeout(function () {
        detectAndSwitchBrowserLanguage();
      }, 1500);
    } else {
      // ...existing code...
    }
    // RTL dil aktifse body'ye class ekle
    if (currentLang && rtlLanguages.includes(currentLang)) {
      document.body.classList.add("hk-translate-rtl-active");
    }
    if (currentLang && currentLang !== "tr") {
      const langMap = {
        en: { code: "en-us", short: "EN", name: "English" },
        de: { code: "de", short: "DE", name: "Deutsch" },
        fr: { code: "fr", short: "FR", name: "Français" },
        es: { code: "es", short: "ES", name: "Español" },
        it: { code: "it", short: "IT", name: "Italiano" },
        pt: { code: "pt", short: "PT", name: "Português" },
        ru: { code: "ru", short: "RU", name: "Русский" },
        zh: { code: "zh", short: "ZH", name: "中文" },
        ja: { code: "ja", short: "JA", name: "日本語" },
        ko: { code: "ko", short: "KO", name: "한국어" },
        ar: { code: "ar", short: "AR", name: "العربية" },
        he: { code: "he", short: "HE", name: "עברית" },
        fa: { code: "fa", short: "FA", name: "فارسی" },
        ur: { code: "ur", short: "UR", name: "اردو" },
        hi: { code: "hi", short: "HI", name: "हिन्दी" },
        bn: { code: "bn", short: "BN", name: "বাংলা" },
        th: { code: "th", short: "TH", name: "ไทย" },
        vi: { code: "vi", short: "VI", name: "Tiếng Việt" },
        id: { code: "id", short: "ID", name: "Bahasa Indonesia" },
        ms: { code: "ms", short: "MS", name: "Bahasa Melayu" },
        nl: { code: "nl", short: "NL", name: "Nederlands" },
        sv: { code: "sv", short: "SV", name: "Svenska" },
        no: { code: "no", short: "NO", name: "Norsk" },
        da: { code: "da", short: "DA", name: "Dansk" },
        fi: { code: "fi", short: "FI", name: "Suomi" },
        pl: { code: "pl", short: "PL", name: "Polski" },
        cs: { code: "cs", short: "CS", name: "Čeština" },
        sk: { code: "sk", short: "SK", name: "Slovenčina" },
        hu: { code: "hu", short: "HU", name: "Magyar" },
        ro: { code: "ro", short: "RO", name: "Română" },
        bg: { code: "bg", short: "BG", name: "Български" },
        hr: { code: "hr", short: "HR", name: "Hrvatski" },
        sr: { code: "sr", short: "SR", name: "Српски" },
        uk: { code: "uk", short: "UK", name: "Українська" },
        el: { code: "el", short: "EL", name: "Ελληνικά" },
      };
      if (langMap[currentLang]) {
        const lang = langMap[currentLang];
        const flagElement = document.getElementById("hkCurrentFlag");
        if (flagElement) {
          flagElement.src = getLocalFlagUrl(lang.code);
          flagElement.alt = lang.name;
        }
        const activeItem = document.querySelector(
          `[data-lang="${currentLang}"]`
        );
        if (activeItem) {
          activeItem.classList.add("active");
        }
      }
    } else {
      // Varsayılan olarak Türkçe'yi aktif yap
      const trItem = document.querySelector('[data-lang="tr"]');
      if (trItem) {
        trItem.classList.add("active");
      }
    }
  });
  // Browser language detection flags
  let autoDetectCompleted = false;
  let userManuallyChanged = false;
  // User preference functions
  function getUserLanguagePreference() {
    try {
      return localStorage.getItem("hk_translate_user_language");
    } catch (error) {
      return null;
    }
  }
  function setUserLanguagePreference(langCode) {
    try {
      localStorage.setItem("hk_translate_user_language", langCode);
    } catch (error) {
      // ...existing code...
    }
  }
  function clearUserLanguagePreference() {
    try {
      localStorage.removeItem("hk_translate_user_language");
    } catch (error) {
      // ...existing code...
    }
  }
  // Browser Language Detection Function
  function detectAndSwitchBrowserLanguage() {
    try {
      // Skip if auto-detect already completed or user manually changed language
      if (autoDetectCompleted || userManuallyChanged) {
        return;
      }
      // Check if Google Translate is ready
      if (!isGoogleTranslateReady()) {
        // Only retry if not completed yet
        if (!autoDetectCompleted) {
          setTimeout(detectAndSwitchBrowserLanguage, 1000);
        }
        return;
      }
      // Get browser language(s)
      const browserLang = navigator.language || navigator.userLanguage;
      const browserLangs = navigator.languages || [browserLang];
      // Get enabled languages from settings
      const enabledLanguages =
        typeof hk_translate_settings !== "undefined" &&
        hk_translate_settings.enabled_languages
          ? Object.keys(hk_translate_settings.enabled_languages)
          : [];
      const defaultLanguage =
        typeof hk_translate_settings !== "undefined" &&
        hk_translate_settings.default_language
          ? hk_translate_settings.default_language
          : "tr";
      const autoDetectEnabled =
        typeof hk_translate_settings !== "undefined" &&
        hk_translate_settings.auto_detect_language
          ? hk_translate_settings.auto_detect_language
          : false;
      // Check if auto-detect is actually enabled
      if (!autoDetectEnabled) {
        return;
      }
      // Check each browser language preference
      for (let i = 0; i < browserLangs.length; i++) {
        let detectedLang = browserLangs[i].toLowerCase();
        // Handle region-specific codes (en-US -> en, pt-BR -> pt, etc.)
        if (detectedLang.includes("-")) {
          detectedLang = detectedLang.split("-")[0];
        }
        // Skip if detected language is the same as default
        if (detectedLang === defaultLanguage) {
          continue;
        }
        // Check if detected language is in enabled languages
        if (enabledLanguages.includes(detectedLang)) {
          // Find the language in our dropdown and get its details
          const languageItem = document.querySelector(
            `[data-lang="${detectedLang}"]`
          );
          if (languageItem) {
            // Extract language name from the item
            const languageNameElement =
              languageItem.querySelector("span:last-child");
            const languageName = languageNameElement
              ? languageNameElement.textContent.trim()
              : detectedLang;
            // Mark auto-detect as completed
            autoDetectCompleted = true;
            // Save user preference for auto-detected language
            setUserLanguagePreference(detectedLang);
            // Auto-switch to detected language immediately
            selectHKLanguage(
              detectedLang,
              detectedLang.toUpperCase(),
              languageName,
              defaultLanguage
            );
            // Update button flag after auto-detection
            setTimeout(() => {
              updateButtonFlag(detectedLang, languageName);
            }, 500);
          } else {
            // ...existing code...
          }
          break; // Stop after first match
        } else {
        }
      }
      // Mark auto-detect as completed even if no language was found
      autoDetectCompleted = true;
    } catch (error) {
      autoDetectCompleted = true; // Mark as completed even on error
    }
  }
  // Check if Google Translate is ready
  function isGoogleTranslateReady() {
    // Check if the Google Translate element exists and has content
    const gtElement = document.getElementById("google_translate_element2");
    if (!gtElement || gtElement.innerHTML.length === 0) {
      return false;
    }
    // Check if the dropdown/combo exists
    const comboElements = document.getElementsByClassName("goog-te-combo");
    if (comboElements.length === 0 || comboElements[0].innerHTML.length === 0) {
      return false;
    }
    return true;
  }

  /**
   * Load and apply manual translations for current page and language
   */
  function loadAndApplyManualTranslations(targetLanguage) {
    // Skip if in edit mode
    if (window.location.href.includes("hk_translate_edit_mode=1")) {
      return;
    }

    const data = new FormData();
    data.append("action", "hk_translate_get_manual_translations");
    data.append("page_url", window.location.href);
    data.append("target_language", targetLanguage);
    data.append("nonce", hk_translate_settings.nonce);

    // ...existing code...

    fetch(hk_translate_settings.ajax_url, {
      method: "POST",
      body: data,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success && result.data && result.data.translations) {
          applyManualTranslationsToPage(result.data.translations);
        } else {
          // ...existing code...
        }
      })
      .catch((error) => {
        // ...existing code...
      });
  }

  /**
   * Apply manual translations to elements (for normal view)
   */
  function applyManualTranslationsToPage(translations) {
    if (!translations || translations.length === 0) return;

    translations.forEach((translation) => {
      // Find elements with matching text hash
      const elements = document.querySelectorAll(
        "p, h1, h2, h3, h4, h5, h6, span, a, li, td, th, label, button"
      );

      elements.forEach((element) => {
        const text = element.textContent.trim();
        const textHash = generateSimpleHash(text);

        if (textHash === translation.text_hash) {
          // Apply manual translation
          if (!translation.is_excluded && translation.manual_translation) {
            element.textContent = translation.manual_translation;
            element.setAttribute("data-hk-manual-translated", "true");
          }
          // If excluded, prevent Google Translate from affecting it
          else if (translation.is_excluded) {
            element.setAttribute("data-hk-excluded", "true");
            element.style.pointerEvents = "none"; // Prevent GT from translating
          }
        }
      });
    });
  }

  /**
   * Simple hash function for content matching
   */
  function generateSimpleHash(text) {
    let hash = 0;
    for (let i = 0; i < text.length; i++) {
      const char = text.charCodeAt(i);
      hash = (hash << 5) - hash + char;
      hash = hash & hash;
    }
    return Math.abs(hash).toString(16);
  }

  // Global fonksiyonları window objesine ekle
  window.googleTranslateElementInit2 = googleTranslateElementInit2;
  window.doGTranslate = doGTranslate;
  window.getLocalFlagUrl = getLocalFlagUrl; // NTW widget için gerekli
  window.GTranslateGetCurrentLang = GTranslateGetCurrentLang;
  window.GTranslateFireEvent = GTranslateFireEvent;
})();
