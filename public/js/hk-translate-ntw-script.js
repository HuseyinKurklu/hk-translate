/**
 * HK Translate NTW (Navigation Translation Widget) JavaScript
 * Handles menu integration for WordPress navigation menus - CONFLICT FIX VERSION
 */

(function () {
  "use strict";

  // ...existing code...

  // Wait for DOM to be ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeNTW);
  } else {
    initializeNTW();
  }

  function initializeNTW() {
    // ...existing code...

    // Find all NTW widgets
    const ntwWidgets = document.querySelectorAll(".hk-translate-ntw-dropdown");
    // ...existing code...

    ntwWidgets.forEach(function (widget, index) {
      setupNTWWidget(widget, index + 1);
    });

    // Setup NTW-specific outside click handler
    setupNTWOutsideClick();

    // ...existing code...
  }

  function setupNTWWidget(widget, index) {
    const widgetId = widget.id;
    const button = widget.querySelector(".hk-translate-ntw-btn");
    const menu = widget.querySelector(".hk-translate-ntw-menu");

    // ...existing code...

    if (!button || !menu) {
      return;
    }

    // Remove existing onclick to prevent conflicts
    button.removeAttribute("onclick");

    // Add modern event listener
    button.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      // ...existing code...

      // Direct toggle without function conflicts
      const wasOpen = widget.classList.contains("open");

      // Close all other NTW widgets first
      document
        .querySelectorAll(".hk-translate-ntw-dropdown.open")
        .forEach(function (otherWidget) {
          if (otherWidget !== widget) {
            otherWidget.classList.remove("open");
          }
        });

      // Toggle this widget
      widget.classList.toggle("open");

      const isOpen = widget.classList.contains("open");
      // ...existing code...
    });

    // Setup language items
    const items = widget.querySelectorAll(".hk-translate-ntw-item");
    items.forEach(function (item) {
      // Remove existing onclick to prevent conflicts
      item.removeAttribute("onclick");

      item.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const langCode = item.getAttribute("data-lang");
        const langSpan = item.querySelector("span");
        const langName = langSpan ? langSpan.textContent : langCode;

        // ...existing code...

        // Update flag in this widget using main widget's getLocalFlagUrl function
        const flag = widget.querySelector(".hk-translate-ntw-btn img");
        if (flag) {
          if (typeof window.getLocalFlagUrl === 'function') {
            flag.src = window.getLocalFlagUrl(langCode);
          } else {
            // Fallback with proper plugin URL
            var pluginUrl = '';
            if (typeof hk_translate_settings !== 'undefined' && hk_translate_settings.plugin_url) {
              pluginUrl = hk_translate_settings.plugin_url;
            } else {
              // Last resort fallback
              pluginUrl = window.location.origin + '/wp-content/plugins/hk-translate/';
            }
            flag.src = pluginUrl + 'public/flags/svg/' + langCode + '.svg';
          }
          flag.alt = langName;
        }

        // Update active states within this widget only
        widget
          .querySelectorAll(".hk-translate-ntw-item")
          .forEach(function (widgetItem) {
            widgetItem.classList.remove("active");
          });
        item.classList.add("active");

        // Close this widget
        widget.classList.remove("open");

        // Trigger translation if main widget function is available
        if (typeof window.doGTranslate === "function") {
          window.doGTranslate(`tr|${langCode}`);
        } else {
          // ...existing code...
        }
      });
    });

    // ...existing code...
  }

  function setupNTWOutsideClick() {
    document.addEventListener("click", function (event) {
      // Only handle clicks outside NTW widgets
      if (!event.target.closest(".hk-translate-ntw-dropdown")) {
        const openNTWWidgets = document.querySelectorAll(
          ".hk-translate-ntw-dropdown.open"
        );
        openNTWWidgets.forEach(function (widget) {
          widget.classList.remove("open");
          // ...existing code...
        });
      }
    });

    console.log("🔒 NTW outside click handler setup");
  // ...existing code...
  }

  // Prevent main widget conflicts by intercepting its click handlers
  function preventMainWidgetConflicts() {
    // Override the main widget's outside click to ignore NTW
    const originalClick = document.addEventListener;
    let mainWidgetHandlerFound = false;

    // Intercept new event listeners
    document.addEventListener = function (type, listener, options) {
      if (
        type === "click" &&
        !mainWidgetHandlerFound &&
        listener.toString().includes("hkTranslateDropdown")
      ) {
        // ...existing code...
        mainWidgetHandlerFound = true;

        // Wrap the main widget listener to ignore NTW clicks
        const wrappedListener = function (event) {
          // If click is on or within an NTW widget, don't trigger main widget logic
          if (event.target.closest(".hk-translate-ntw-dropdown")) {
            return;
          }
          listener(event);
        };

        originalClick.call(this, type, wrappedListener, options);
      } else {
        originalClick.call(this, type, listener, options);
      }
    };
  }

  // Apply conflict prevention immediately
  preventMainWidgetConflicts();

  console.log("🚀 NTW Script loaded - ready for initialization");
  // ...existing code...
})();
