(function ($) {
  "use strict";

  $(document).ready(function () {
    // Initialize preview
    updatePreview();

    // Update preview when position values change
    $(
      "#desktop_bottom, #tablet_bottom, #mobile_bottom, #desktop_size, #tablet_size, #mobile_size, #desktop_position, #tablet_position, #mobile_position"
    ).on("input change", function () {
      updatePreview();
    });

    // Update preview when default language changes
    $("#default_language").on("change", function () {
      updateDefaultLanguagePreview();
    });

    // Save settings
    $("#hk-translate-settings-form").on("submit", function (e) {
      e.preventDefault();
      saveSettings();
    });

    // Reset settings
    $("#reset-settings").on("click", function (e) {
      e.preventDefault();
      resetSettings();
    });

    // Language selection validation and UI updates
    $('input[name="enabled_languages[]"]').on("change", function () {
      validateLanguageSelection();
      updateSelectedLanguagesList();
      updateLanguageItemStyles();
    });

    // Initial validation and UI setup
    validateLanguageSelection();
    updateSelectedLanguagesList();
    updateLanguageItemStyles();

    // Visual settings handlers
    setupVisualSettings();

    // Language ordering handlers
    setupLanguageOrdering();
  });

  /**
   * Update preview positions and sizes
   */
  function updatePreview() {
    var desktopBottom = $("#desktop_bottom").val() || 20;
    var tabletBottom = $("#tablet_bottom").val() || 15;
    var mobileBottom = $("#mobile_bottom").val() || 10;
    var desktopSize = $("#desktop_size").val() || 40;
    var tabletSize = $("#tablet_size").val() || 36;
    var mobileSize = $("#mobile_size").val() || 32;
    // Legacy position removed - using device-specific positions now

    // Update positions
    $("#preview-desktop").css("bottom", desktopBottom + "px");
    $("#preview-tablet").css("bottom", tabletBottom + "px");
    $("#preview-mobile").css("bottom", mobileBottom + "px");

    // Update sizes
    $("#preview-desktop .hk-translate-preview-btn").css({
      width: desktopSize + "px",
      height: desktopSize + "px",
    });
    $("#preview-tablet .hk-translate-preview-btn").css({
      width: tabletSize + "px",
      height: tabletSize + "px",
    });
    $("#preview-mobile .hk-translate-preview-btn").css({
      width: mobileSize + "px",
      height: mobileSize + "px",
    });

    // Update position based on device-specific selections
    $("#preview-desktop, #preview-tablet, #preview-mobile").css({
      top: "auto",
      bottom: "auto",
      left: "auto",
      right: "auto",
      transform: "none",
    });

    // Use device-specific positions instead of legacy position
    var desktopPosition = $("#desktop_position").val() || "bottom-right";
    var tabletPosition = $("#tablet_position").val() || "bottom-right";
    var mobilePosition = $("#mobile_position").val() || "bottom-right";

    // Apply desktop position
    switch (desktopPosition) {
      case "bottom-left":
        $("#preview-desktop").css({
          bottom: desktopBottom + "px",
          left: "20px",
        });
        break;
      case "bottom-right":
        $("#preview-desktop").css({
          bottom: desktopBottom + "px",
          right: "20px",
        });
        break;
      case "middle-left":
        $("#preview-desktop").css({
          top: "50%",
          left: "20px",
          transform: "translateY(-50%)",
        });
        break;
      case "middle-right":
        $("#preview-desktop").css({
          top: "50%",
          right: "20px",
          transform: "translateY(-50%)",
        });
        break;
      case "top-left":
        $("#preview-desktop").css({ top: desktopBottom + "px", left: "20px" });
        break;
      case "top-right":
        $("#preview-desktop").css({ top: desktopBottom + "px", right: "20px" });
        break;
    }

    // Apply tablet position
    switch (tabletPosition) {
      case "bottom-left":
        $("#preview-tablet").css({ bottom: tabletBottom + "px", left: "20px" });
        break;
      case "bottom-right":
        $("#preview-tablet").css({
          bottom: tabletBottom + "px",
          right: "20px",
        });
        break;
      case "middle-left":
        $("#preview-tablet").css({
          top: "50%",
          left: "20px",
          transform: "translateY(-50%)",
        });
        break;
      case "middle-right":
        $("#preview-tablet").css({
          top: "50%",
          right: "20px",
          transform: "translateY(-50%)",
        });
        break;
      case "top-left":
        $("#preview-tablet").css({ top: tabletBottom + "px", left: "20px" });
        break;
      case "top-right":
        $("#preview-tablet").css({ top: tabletBottom + "px", right: "20px" });
        break;
    }

    // Apply mobile position
    switch (mobilePosition) {
      case "bottom-left":
        $("#preview-mobile").css({ bottom: mobileBottom + "px", left: "20px" });
        break;
      case "bottom-right":
        $("#preview-mobile").css({
          bottom: mobileBottom + "px",
          right: "20px",
        });
        break;
      case "middle-left":
        $("#preview-mobile").css({
          top: "50%",
          left: "20px",
          transform: "translateY(-50%)",
        });
        break;
      case "middle-right":
        $("#preview-mobile").css({
          top: "50%",
          right: "20px",
          transform: "translateY(-50%)",
        });
        break;
      case "top-left":
        $("#preview-mobile").css({ top: mobileBottom + "px", left: "20px" });
        break;
      case "top-right":
        $("#preview-mobile").css({ top: mobileBottom + "px", right: "20px" });
        break;
    }
  }

  /**
   * Update default language in preview
   */
  function updateDefaultLanguagePreview() {
    var selectedLanguage = $("#default_language").val();
    var flagUrl = hk_translate_ajax.plugin_url + "public/flags/svg/";

    // Map language codes to flag files
    var flagMap = {
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

    var flagFile = flagMap[selectedLanguage] || "tr.svg";
    var newFlagUrl = flagUrl + flagFile;

    $(".hk-translate-preview-btn img").attr("src", newFlagUrl);
  }

  /**
   * Validate language selection
   */
  function validateLanguageSelection() {
    var checkedLanguages = $(
      'input[name="enabled_languages[]"]:checked'
    ).length;
    var saveButton = $("#save-settings");

    if (checkedLanguages === 0) {
      saveButton.prop("disabled", true);
      showMessage("error", "Please select at least one language.");
    } else {
      saveButton.prop("disabled", false);
      hideMessage();
    }
  }

  /**
   * Save settings via AJAX
   */
  function saveSettings() {
    var $form = $("#hk-translate-settings-form");
    var $saveButton = $("#save-settings");
    var formData = $form.serialize();

    // Add loading state
    $saveButton
      .prop("disabled", true)
      .find("span")
      .removeClass("dashicons-yes")
      .addClass("dashicons-update");
    $saveButton.find(".dashicons").addClass("hk-translate-spin");

    $.ajax({
      url: hk_translate_ajax.ajax_url,
      type: "POST",
      data: {
        action: "hk_translate_save_settings",
        nonce: hk_translate_ajax.nonce,
        enabled_languages: $('input[name="enabled_languages[]"]:checked')
          .map(function () {
            return this.value;
          })
          .get(),
        desktop_bottom: $("#desktop_bottom").val(),
        tablet_bottom: $("#tablet_bottom").val(),
        mobile_bottom: $("#mobile_bottom").val(),
        desktop_size: $("#desktop_size").val(),
        tablet_size: $("#tablet_size").val(),
        mobile_size: $("#mobile_size").val(),
        menu_height: $("#menu_height").val(),
        desktop_position: $("#desktop_position").val(),
        tablet_position: $("#tablet_position").val(),
        mobile_position: $("#mobile_position").val(),
        default_language: $("#default_language").val(),
        plugin_enabled: $("#plugin_enabled").is(":checked") ? 1 : 0,
        auto_detect_language: $("#auto_detect_language").is(":checked") ? 1 : 0,
        compact_mode: $("#compact_mode").is(":checked") ? 1 : 0,
        // Visual settings
        btn_bg_enabled: $("#btn_bg_enabled").is(":checked") ? 1 : 0,
        btn_bg_color: $("#btn_bg_color").val(),
        btn_border_enabled: $("#btn_border_enabled").is(":checked") ? 1 : 0,
        btn_border_color: $("#btn_border_color").val(),
        btn_border_width: $("#btn_border_width").val(),
        btn_border_radius: $("#btn_border_radius").val(),
        btn_flag_radius: $("#btn_flag_radius").val(),
        btn_hover_color: $("#btn_hover_color").val(),
        menu_bg_color: $("#menu_bg_color").val(),
        menu_border_enabled: $("#menu_border_enabled").is(":checked") ? 1 : 0,
        menu_border_color: $("#menu_border_color").val(),
        menu_border_width: $("#menu_border_width").val(),
        menu_border_radius: $("#menu_border_radius").val(),
        menu_width: $("#menu_width").val(),
        menu_text_color: $("#menu_text_color").val(),
        menu_hover_bg_color: $("#menu_hover_bg_color").val(),
        menu_hover_text_color: $("#menu_hover_text_color").val(),
        menu_active_bg_color: $("#menu_active_bg_color").val(),
        menu_active_text_color: $("#menu_active_text_color").val(),
        // NTW settings
        ntw_menu_position: $("#ntw_menu_position").val(),
        ntw_compact_mode: $("#ntw_compact_mode").is(":checked") ? 1 : 0,
        ntw_hide_main_widget: $("#ntw_hide_main_widget").is(":checked") ? 1 : 0,
      },
      success: function (response) {
        if (response.success) {
          showMessage("success", response.data.message);
          
          // Debug information
          if (response.data.debug) {
            console.log("HK Translate Save Debug:", response.data.debug);
          }
        } else {
          showMessage("error", response.data.message);
          
          // Debug information for errors
          if (response.data.debug) {
            console.log("HK Translate Save Error Debug:", response.data.debug);
          }
        }
      },
      error: function (xhr, status, error) {
        showMessage("error", "AJAX Error: " + error + " - Status: " + status);
      },
      complete: function () {
        // Remove loading state
        $saveButton
          .prop("disabled", false)
          .find("span")
          .removeClass("dashicons-update hk-translate-spin")
          .addClass("dashicons-yes");
      },
    });
  }

  /**
   * Reset settings to defaults
   */
  function resetSettings() {
    if (!confirm(hk_translate_ajax.strings.reset_confirm)) {
      return;
    }

    var $resetButton = $("#reset-settings");

    // Add loading state
    $resetButton
      .prop("disabled", true)
      .find("span")
      .removeClass("dashicons-update")
      .addClass("dashicons-update");
    $resetButton.find(".dashicons").addClass("hk-translate-spin");

    $.ajax({
      url: hk_translate_ajax.ajax_url,
      type: "POST",
      data: {
        action: "hk_translate_reset_settings",
        nonce: hk_translate_ajax.nonce,
      },
      success: function (response) {
        if (response.success) {
          showMessage("success", response.data.message);

          // Update form with default values
          var settings = response.data.settings;

          // Reset checkboxes
          $('input[name="enabled_languages[]"]').prop("checked", false);
          settings.enabled_languages.forEach(function (lang) {
            $('input[name="enabled_languages[]"][value="' + lang + '"]').prop(
              "checked",
              true
            );
          });

          // Reset position values
          $("#desktop_bottom").val(settings.desktop_bottom);
          $("#tablet_bottom").val(settings.tablet_bottom);
          $("#mobile_bottom").val(settings.mobile_bottom);

          // Reset size values
          $("#desktop_size").val(settings.desktop_size);
          $("#tablet_size").val(settings.tablet_size);
          $("#mobile_size").val(settings.mobile_size);

          // Reset other values
          $("#menu_height").val(settings.menu_height);
          $("#desktop_position").val(settings.desktop_position);
          $("#tablet_position").val(settings.tablet_position);
          $("#mobile_position").val(settings.mobile_position);

          // Reset default language
          $("#default_language").val(settings.default_language);

          // Reset plugin enabled
          $("#plugin_enabled").prop("checked", settings.plugin_enabled);

          // Reset auto-detect and compact mode
          $("#auto_detect_language").prop(
            "checked",
            settings.auto_detect_language
          );
          $("#compact_mode").prop("checked", settings.compact_mode);

          // Reset visual settings
          $("#btn_bg_enabled").prop("checked", settings.btn_bg_enabled);
          $("#btn_bg_color").val(settings.btn_bg_color);
          $("#btn_border_enabled").prop("checked", settings.btn_border_enabled);
          $("#btn_border_color").val(settings.btn_border_color);
          $("#btn_border_width").val(settings.btn_border_width);
          $("#btn_border_radius").val(settings.btn_border_radius);
          $("#btn_flag_radius").val(settings.btn_flag_radius);
          $("#btn_hover_color").val(settings.btn_hover_color);
          $("#menu_bg_color").val(settings.menu_bg_color);
          $("#menu_border_enabled").prop(
            "checked",
            settings.menu_border_enabled
          );
          $("#menu_border_color").val(settings.menu_border_color);
          $("#menu_border_width").val(settings.menu_border_width);
          $("#menu_border_radius").val(settings.menu_border_radius);
          $("#menu_width").val(settings.menu_width);
          $("#menu_text_color").val(settings.menu_text_color);
          $("#menu_hover_bg_color").val(settings.menu_hover_bg_color);
          $("#menu_hover_text_color").val(settings.menu_hover_text_color);
          $("#menu_active_bg_color").val(settings.menu_active_bg_color);
          $("#menu_active_text_color").val(settings.menu_active_text_color);

          // Reset NTW settings
          $("#ntw_hide_main_widget").prop("checked", settings.ntw_hide_main_widget || false);
          $("#ntw_menu_position").val(settings.ntw_menu_position || 'bottom-left');
          $("#ntw_compact_mode").prop("checked", settings.ntw_compact_mode || false);

          // Update disabled states
          $("#btn_bg_color").prop("disabled", !settings.btn_bg_enabled);
          $("#btn_border_color, #btn_border_width").prop(
            "disabled",
            !settings.btn_border_enabled
          );
          $("#menu_border_color, #menu_border_width").prop(
            "disabled",
            !settings.menu_border_enabled
          );

          // Update preview and UI
          updatePreview();
          updateDefaultLanguagePreview();
          validateLanguageSelection();
          updateSelectedLanguagesList();
          updateLanguageItemStyles();
          updatePreviewStyles();
        } else {
          showMessage("error", response.data.message);
        }
      },
      error: function () {
        showMessage("error", hk_translate_ajax.strings.error);
      },
      complete: function () {
        // Remove loading state
        $resetButton
          .prop("disabled", false)
          .find(".dashicons")
          .removeClass("hk-translate-spin");
      },
    });
  }

  /**
   * Show message
   */
  function showMessage(type, message) {
    var $messageDiv = $("#hk-translate-message");
    $messageDiv
      .removeClass("notice-success notice-error notice-warning")
      .addClass("notice-" + type)
      .find("p")
      .text(message);
    $messageDiv.show();

    // Auto hide success messages
    if (type === "success") {
      setTimeout(function () {
        hideMessage();
      }, 3000);
    }
  }

  /**
   * Hide message
   */
  function hideMessage() {
    $("#hk-translate-message").hide();
  }

  /**
   * Update selected languages list
   */
  function updateSelectedLanguagesList() {
    var selectedLanguages = $('input[name="enabled_languages[]"]:checked');
    var container = $("#selectedLanguagesContainer");
    var listContainer = $("#selectedLanguagesList");

    if (selectedLanguages.length === 0) {
      listContainer.hide();
      return;
    }

    // Get current order from existing items
    var currentOrder = [];
    container.find(".hk-translate-selected-item").each(function () {
      currentOrder.push($(this).data("lang"));
    });

    // Clear existing items
    container.empty();

    // Create array of selected language codes
    var selectedCodes = [];
    selectedLanguages.each(function () {
      selectedCodes.push($(this).val());
    });

    // First add languages in current order (if they're still selected)
    var orderIndex = 1;
    currentOrder.forEach(function (langCode) {
      if (selectedCodes.includes(langCode)) {
        addLanguageItem(langCode, orderIndex++);
        // Remove from selectedCodes so we don't add it again
        selectedCodes = selectedCodes.filter((code) => code !== langCode);
      }
    });

    // Add any newly selected languages that weren't in the previous order
    selectedCodes.forEach(function (langCode) {
      addLanguageItem(langCode, orderIndex++);
    });

    // Re-initialize sortable functionality
    if (container.hasClass("ui-sortable")) {
      container.sortable("destroy");
    }

    container.sortable({
      handle: ".hk-translate-drag-handle",
      placeholder: "ui-sortable-placeholder hk-translate-selected-item",
      helper: "clone",
      tolerance: "pointer",
      axis: "y",
      update: function (event, ui) {
        updateOrderNumbers();
      },
      start: function (event, ui) {
        ui.placeholder.height(ui.helper.outerHeight());
      },
    });

    listContainer.show();
  }

  /**
   * Add a language item to the selected languages list
   */
  function addLanguageItem(langCode, orderNumber) {
    var langItem = $(
      'input[name="enabled_languages[]"][value="' + langCode + '"]'
    ).closest(".hk-translate-language-item");
    var flagSrc = langItem.find("img").attr("src");
    var langName = langItem
      .find(".hk-translate-language-name")
      .contents()
      .first()
      .text()
      .trim();

    var selectedItem = $(
      '<div class="hk-translate-selected-item" data-lang="' +
        langCode +
        '">' +
        '<div class="hk-translate-drag-handle">' +
        '<span class="dashicons dashicons-sort"></span>' +
        "</div>" +
        '<div class="hk-translate-order-number">' +
        orderNumber +
        "</div>" +
        '<img src="' +
        flagSrc +
        '" alt="' +
        langName +
        '">' +
        '<div class="hk-translate-lang-info">' +
        '<span class="hk-translate-lang-name">' +
        langName +
        "</span>" +
        '<span class="hk-translate-lang-code">(' +
        langCode.toUpperCase() +
        ")</span>" +
        "</div>" +
        "</div>"
    );

    $("#selectedLanguagesContainer").append(selectedItem);
  }

  /**
   * Update language item styles (add/remove selected class and check icons)
   */
  function updateLanguageItemStyles() {
    $(".hk-translate-language-item").each(function () {
      var checkbox = $(this).find('input[type="checkbox"]');
      if (checkbox.is(":checked")) {
        $(this).addClass("selected");
      } else {
        $(this).removeClass("selected");
      }
    });
  }

  /**
   * Setup visual settings handlers
   */
  function setupVisualSettings() {
    // Button background enable/disable
    $("#btn_bg_enabled").on("change", function () {
      $("#btn_bg_color").prop("disabled", !this.checked);
      updatePreviewStyles();
    });

    // Button border enable/disable
    $("#btn_border_enabled").on("change", function () {
      var enabled = this.checked;
      $("#btn_border_color, #btn_border_width").prop("disabled", !enabled);
      updatePreviewStyles();
    });

    // Menu border enable/disable
    $("#menu_border_enabled").on("change", function () {
      var enabled = this.checked;
      $("#menu_border_color, #menu_border_width").prop("disabled", !enabled);
      updatePreviewStyles();
    });

    // Visual settings change handlers
    $(
      "#btn_bg_color, #btn_border_color, #btn_border_width, #btn_border_radius, #btn_flag_radius, #btn_hover_color, " +
        "#menu_height, #menu_bg_color, #menu_border_color, #menu_border_width, #menu_border_radius, #menu_width, " +
        "#menu_text_color, #menu_hover_bg_color, #menu_hover_text_color, #menu_active_bg_color, #menu_active_text_color"
    ).on("input change", function () {
      updatePreviewStyles();
    });

    // Compact mode change handler
    $("#compact_mode").on("change", function () {
      updatePreviewStyles();
    });

    // Initial preview update
    updatePreviewStyles();
  }

  /**
   * Update preview styles based on visual settings
   */
  function updatePreviewStyles() {
    // Button styles
    var btnBgEnabled = $("#btn_bg_enabled").is(":checked");
    var btnBgColor = $("#btn_bg_color").val();
    var btnBorderEnabled = $("#btn_border_enabled").is(":checked");
    var btnBorderColor = $("#btn_border_color").val();
    var btnBorderWidth = $("#btn_border_width").val() || 2;
    var btnBorderRadius = $("#btn_border_radius").val() || 50;

    // Menu styles
    var menuBgColor = $("#menu_bg_color").val() || "#ffffff";
    var menuBorderEnabled = $("#menu_border_enabled").is(":checked");
    var menuBorderColor = $("#menu_border_color").val();
    var menuBorderWidth = $("#menu_border_width").val() || 1;
    var menuBorderRadius = $("#menu_border_radius").val() || 8;
    var menuWidth = $("#menu_width").val() || 200;
    var menuTextColor = $("#menu_text_color").val() || "#333333";
    var menuHoverBgColor = $("#menu_hover_bg_color").val() || "#0073aa";
    var menuHoverTextColor = $("#menu_hover_text_color").val() || "#ffffff";
    var menuActiveBgColor = $("#menu_active_bg_color").val() || "#e7f3ff";
    var menuActiveTextColor = $("#menu_active_text_color").val() || "#007cba";

    // Apply button styles to preview
    var buttonStyles = {
      "border-radius": btnBorderRadius + "px",
    };

    if (btnBgEnabled) {
      buttonStyles.background = btnBgColor;
    } else {
      buttonStyles.background = "transparent";
    }

    if (btnBorderEnabled) {
      buttonStyles.border = btnBorderWidth + "px solid " + btnBorderColor;
    } else {
      buttonStyles.border = "none";
    }

    $(".hk-translate-preview-btn").css(buttonStyles);

    // Create or update preview menu styles
    updatePreviewMenuStyles(
      menuBgColor,
      menuBorderEnabled,
      menuBorderColor,
      menuBorderWidth,
      menuBorderRadius,
      menuWidth,
      menuTextColor,
      menuHoverBgColor,
      menuHoverTextColor,
      menuActiveBgColor,
      menuActiveTextColor
    );

    // Apply compact mode
    var compactMode = $("#compact_mode").is(":checked");
    if (compactMode) {
      $(".hk-translate-preview-menu").addClass("hk-translate-compact");
    } else {
      $(".hk-translate-preview-menu").removeClass("hk-translate-compact");
    }
  }

  /**
   * Update preview menu styles
   */
  function updatePreviewMenuStyles(
    bgColor,
    borderEnabled,
    borderColor,
    borderWidth,
    borderRadius,
    width,
    textColor,
    hoverBgColor,
    hoverTextColor,
    activeBgColor,
    activeTextColor
  ) {
    // Remove existing style tag
    $("#hk-translate-preview-styles").remove();

    // Create new style tag
    var styles = `
      <style id="hk-translate-preview-styles">
        .hk-translate-preview-menu {
          background: ${bgColor} !important;
          width: ${width}px !important;
          border-radius: ${borderRadius}px !important;
          ${
            borderEnabled
              ? `border: ${borderWidth}px solid ${borderColor} !important;`
              : "border: none !important;"
          }
          overflow-x: hidden !important;
          scrollbar-width: thin !important;
          scrollbar-color: #c1c1c1 transparent !important;
        }
        
        .hk-translate-preview-menu::-webkit-scrollbar {
          width: 6px !important;
        }
        
        .hk-translate-preview-menu::-webkit-scrollbar-track {
          background: transparent !important;
        }
        
        .hk-translate-preview-menu::-webkit-scrollbar-thumb {
          background-color: #c1c1c1 !important;
          border-radius: 3px !important;
        }
        
        .hk-translate-preview-menu::-webkit-scrollbar-thumb:hover {
          background-color: #a8a8a8 !important;
        }
        
        .hk-translate-preview-menu-item {
          color: ${textColor} !important;
        }
        
        .hk-translate-preview-menu-item:hover {
          background-color: ${hoverBgColor} !important;
          color: ${hoverTextColor} !important;
        }
        
        .hk-translate-preview-menu-item.active {
          background-color: ${activeBgColor} !important;
          color: ${activeTextColor} !important;
        }
      </style>
    `;

    $("head").append(styles);
  }

  /**
   * Setup language ordering (drag & drop) functionality
   */
  function setupLanguageOrdering() {
    // Initialize jQuery UI Sortable
    $("#selectedLanguagesContainer").sortable({
      handle: ".hk-translate-drag-handle",
      placeholder: "ui-sortable-placeholder hk-translate-selected-item",
      helper: "clone",
      tolerance: "pointer",
      axis: "y",
      update: function (event, ui) {
        updateOrderNumbers();
      },
      start: function (event, ui) {
        ui.placeholder.height(ui.helper.outerHeight());
      },
    });

    // Save order button
    $("#hk-translate-save-order").on("click", function () {
      saveLanguageOrder();
    });

    // Reset order button
    $("#hk-translate-reset-order").on("click", function () {
      resetLanguageOrder();
    });

    // Initial order numbers update
    updateOrderNumbers();
  }

  /**
   * Update order numbers after sorting
   */
  function updateOrderNumbers() {
    $("#selectedLanguagesContainer .hk-translate-selected-item").each(function (
      index
    ) {
      $(this)
        .find(".hk-translate-order-number")
        .text(index + 1);
    });
  }

  /**
   * Save language order via AJAX
   */
  function saveLanguageOrder() {
    var $saveButton = $("#hk-translate-save-order");
    var originalText = $saveButton.text();

    // Get current order
    var languageOrder = [];
    $("#selectedLanguagesContainer .hk-translate-selected-item").each(
      function () {
        languageOrder.push($(this).data("lang"));
      }
    );

    // Add loading state
    $saveButton
      .prop("disabled", true)
      .html('<span class="dashicons dashicons-update"></span> Saving...');

    $.ajax({
      url: hk_translate_ajax.ajax_url,
      type: "POST",
      data: {
        action: "hk_translate_save_language_order",
        nonce: hk_translate_ajax.nonce,
        language_order: languageOrder,
      },
      success: function (response) {
        if (response.success) {
          showMessage("success", response.data.message);
        } else {
          showMessage("error", response.data.message);
        }
      },
      error: function (xhr, status, error) {
        // Try to parse response text for more details
        try {
          var errorResponse = JSON.parse(xhr.responseText);
          if (errorResponse.data && errorResponse.data.message) {
            showMessage("error", errorResponse.data.message);
          } else {
            showMessage("error", "Error saving language order: " + error);
          }
        } catch (e) {
          showMessage(
            "error",
            "Error saving language order: " +
              error +
              " (Status: " +
              status +
              ")"
          );
        }
      },
      complete: function () {
        // Restore button state
        $saveButton
          .prop("disabled", false)
          .html('<span class="dashicons dashicons-saved"></span> Save Order');
      },
    });
  }

  /**
   * Reset language order to default
   */
  function resetLanguageOrder() {
    if (
      !confirm("Are you sure you want to reset the language order to default?")
    ) {
      return;
    }

    // Get enabled languages in alphabetical order (as default)
    var enabledLanguages = [];
    $('input[name="enabled_languages[]"]:checked').each(function () {
      enabledLanguages.push($(this).val());
    });

    // Sort alphabetically (as default order)
    enabledLanguages.sort();

    // NTW ayarlarını da sıfırla (görsel olarak)
    $("#ntw_menu_position").val('bottom-left');
    $("#ntw_compact_mode").prop("checked", false);

    var $resetButton = $("#hk-translate-reset-order");

    // Add loading state
    $resetButton
      .prop("disabled", true)
      .html('<span class="dashicons dashicons-update"></span> Resetting...');

    $.ajax({
      url: hk_translate_ajax.ajax_url,
      type: "POST",
      data: {
        action: "hk_translate_save_language_order",
        nonce: hk_translate_ajax.nonce,
        language_order: enabledLanguages,
      },
      success: function (response) {
        if (response.success) {
          showMessage("success", "Language order reset to default!");
          // Reload page to show new order
          setTimeout(function () {
            location.reload();
          }, 1000);
        } else {
          showMessage("error", response.data.message);
        }
      },
      error: function (xhr, status, error) {
        showMessage("error", "Error resetting language order: " + error);
      },
      complete: function () {
        // Restore button state
        $resetButton
          .prop("disabled", false)
          .html(
            '<span class="dashicons dashicons-undo"></span> Reset to Default'
          );
      },
    });
  }
})(jQuery);
