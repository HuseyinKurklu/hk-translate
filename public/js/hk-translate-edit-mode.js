/**
 * HK Translate Edit Mode
 * Manual Translation Editor for WordPress Plugin
 * Version: 1.0.0
 */

(function () {
  "use strict";

  // Edit Mode Global Variables
  let isEditModeActive = false;
  let currentEditLanguage = "tr"; // Default source language
  let currentTargetLanguage = null;
  let editableElements = [];
  let manualTranslations = new Map(); // Cache for manual translations
  let editSession = null;

  // Configuration
  const EDIT_MODE_PARAM = "hk_translate_edit_mode";
  const EDITABLE_SELECTORS = [
    "p",
    "h1",
    "h2",
    "h3",
    "h4",
    "h5",
    "h6",
    "span",
    "a",
    "li",
    "td",
    "th",
    "label",
    "button",
    'div[role="text"]',
    ".text-content",
  ];

  /**
   * Initialize Edit Mode
   */
  function initEditMode() {
    // Debug permission check
    // console.log("🔍 Checking edit permissions...");
    // console.log("hk_translate_settings:", typeof hk_translate_settings !== "undefined" ? hk_translate_settings : "UNDEFINED");
    
    // Check if user has permission (admin only)
    if (!hasEditPermission()) {
      // console.warn("❌ Edit mode requires admin permissions");
      // console.warn("user_can_edit value:", typeof hk_translate_settings !== "undefined" ? hk_translate_settings.user_can_edit : "SETTINGS UNDEFINED");
      showPermissionError();
      return;
    }
    
    // Hide language dropdown in edit mode
    hideLangaugeDropdown();

    // Detect current language
    detectCurrentLanguage();

    // Scan page for editable elements
    scanEditableElements();

    // Add edit buttons to elements
    addEditButtons();

    // Load existing manual translations
    loadManualTranslations();

    // Initialize edit mode UI
    initEditModeUI();

    // Start edit session tracking
    startEditSession();

    isEditModeActive = true;
    // console.log("✅ Edit Mode activated successfully");
  }

  /**
   * Check if current user has edit permissions
   */
  function hasEditPermission() {
    // Debug permission check
    console.log("🔍 hasEditPermission() called");
    console.log("typeof hk_translate_settings:", typeof hk_translate_settings);
    
    if (typeof hk_translate_settings === "undefined") {
      console.error("❌ hk_translate_settings is undefined!");
      return false;
    }
    
    console.log("hk_translate_settings.user_can_edit:", hk_translate_settings.user_can_edit);
    console.log("Type of user_can_edit:", typeof hk_translate_settings.user_can_edit);
    
    // Check for both boolean true and string "1" (PHP sometimes sends 1 as string)
    const canEdit = hk_translate_settings.user_can_edit === true || 
                   hk_translate_settings.user_can_edit === 1 || 
                   hk_translate_settings.user_can_edit === "1";
    console.log("Final permission result:", canEdit);
    
    return canEdit;
  }

  /**
   * Show permission error message
   */
  function showPermissionError() {
    const errorDiv = document.createElement("div");
    errorDiv.className = "hk-translate-error";
    errorDiv.innerHTML = `
      <div style="position: fixed; top: 20px; right: 20px; background: #dc3545; color: white; 
                  padding: 15px; border-radius: 5px; z-index: 10000; font-size: 14px;">
        <strong>Access Denied:</strong> Edit mode requires administrator privileges.
        <button onclick="this.parentElement.parentElement.remove()" 
                style="margin-left: 10px; background: transparent; border: 1px solid white; 
                       color: white; padding: 2px 8px; border-radius: 3px; cursor: pointer;">✕</button>
      </div>
    `;
    document.body.appendChild(errorDiv);
  }

  /**
   * Hide language dropdown during edit mode
   */
  function hideLangaugeDropdown() {
    const dropdown = document.getElementById("hkTranslateDropdown");
    if (dropdown) {
      dropdown.style.display = "none";
    }
  }

  /**
   * Detect current page language
   */
  function detectCurrentLanguage() {
    // Try to get from Google Translate cookie
    const currentLang = GTranslateGetCurrentLang();

    if (currentLang) {
      currentTargetLanguage = currentLang;
    } else {
      // Default language
      const defaultLang =
        typeof hk_translate_settings !== "undefined" &&
        hk_translate_settings.default_language
          ? hk_translate_settings.default_language
          : "tr";
      currentTargetLanguage = defaultLang;
    }
  }

  /**
   * Scan page for editable text elements
   */
  function scanEditableElements() {
    editableElements = [];

    EDITABLE_SELECTORS.forEach((selector) => {
      const elements = document.querySelectorAll(selector);
      elements.forEach((element) => {
        const text = element.textContent.trim();

        // Skip elements with no text or very short text
        if (text.length < 3) return;

        // Skip elements that are likely navigation or system text
        if (isSystemElement(element)) return;

        // Skip elements already processed
        if (element.hasAttribute("data-hk-edit-processed")) return;

        editableElements.push({
          element: element,
          originalText: text,
          textHash: generateTextHash(text),
          selector: getElementSelector(element),
        });

        element.setAttribute("data-hk-edit-processed", "true");
      });
    });

    console.log(`📝 Found ${editableElements.length} editable elements`);
  }

  /**
   * Check if element is system/navigation element to skip
   */
  function isSystemElement(element) {
    const skipClasses = [
      "nav",
      "navbar",
      "menu",
      "header",
      "footer",
      "sidebar",
      "admin",
      "wp-admin",
      "login",
      "search",
      "pagination",
      "hk-translate",
      "google-translate",
      "goog-te",
    ];

    const elementClass = element.className.toLowerCase();
    const elementId = element.id.toLowerCase();

    return skipClasses.some(
      (skipClass) =>
        elementClass.includes(skipClass) || elementId.includes(skipClass)
    );
  }

  /**
   * Generate unique hash for text content
   */
  function generateTextHash(text) {
    // Simple hash function - in production, use crypto.subtle.digest
    let hash = 0;
    for (let i = 0; i < text.length; i++) {
      const char = text.charCodeAt(i);
      hash = (hash << 5) - hash + char;
      hash = hash & hash; // Convert to 32-bit integer
    }
    return Math.abs(hash).toString(16);
  }

  /**
   * Get CSS selector for element
   */
  function getElementSelector(element) {
    if (element.id) {
      return `#${element.id}`;
    }

    if (element.className) {
      const classes = element.className.split(" ").filter((c) => c.length > 0);
      if (classes.length > 0) {
        return `${element.tagName.toLowerCase()}.${classes[0]}`;
      }
    }

    return element.tagName.toLowerCase();
  }

  /**
   * Add edit buttons to all editable elements
   */
  function addEditButtons() {
    editableElements.forEach((item, index) => {
      const element = item.element;

      // Create edit button container
      const buttonContainer = document.createElement("div");
      buttonContainer.className = "hk-edit-buttons";
      buttonContainer.setAttribute("data-element-index", index);

      buttonContainer.innerHTML = `
        <button class="hk-edit-btn hk-edit-translate" title="Edit Translation" 
                onclick="openEditModal(${index})">
          ✏️
        </button>
        <button class="hk-edit-btn hk-exclude-btn" title="Exclude from Translation" 
                onclick="toggleExclude(${index})">
          🚫
        </button>
      `;

      // Position button container
      element.style.position = "relative";
      element.appendChild(buttonContainer);

      // Add hover effects
      element.addEventListener("mouseenter", () => showEditButtons(index));
      element.addEventListener("mouseleave", () => hideEditButtons(index));
    });

    // Add edit mode styles
    addEditModeStyles();
  }

  /**
   * Add CSS styles for edit mode
   */
  function addEditModeStyles() {
    const style = document.createElement("style");
    style.id = "hk-edit-mode-styles";
    style.textContent = `
      .hk-edit-buttons {
        position: absolute;
        top: -5px;
        right: -5px;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 1000;
      }
      
      .hk-edit-buttons.visible {
        opacity: 1;
      }
      
      .hk-edit-btn {
        background: #007cba;
        border: none;
        border-radius: 3px;
        color: white;
        padding: 4px 6px;
        margin-left: 2px;
        cursor: pointer;
        font-size: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      }
      
      .hk-edit-btn:hover {
        background: #005a87;
      }
      
      .hk-exclude-btn {
        background: #dc3545;
      }
      
      .hk-exclude-btn:hover {
        background: #c82333;
      }
      
      .hk-excluded-element {
        background-color: rgba(220, 53, 69, 0.1) !important;
        border: 1px dashed #dc3545 !important;
      }
      
      .hk-manual-translated {
        background-color: rgba(40, 167, 69, 0.1) !important;
        border: 1px solid #28a745 !important;
      }
      
      /* Edit Modal Styles */
      .hk-edit-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      
      .hk-edit-modal-content {
        background: white;
        border-radius: 8px;
        padding: 20px;
        max-width: 600px;
        width: 90%;
        max-height: 80%;
        overflow-y: auto;
      }
      
      .hk-edit-modal h3 {
        margin-top: 0;
        color: #333;
      }
      
      .hk-edit-modal textarea {
        width: 100%;
        min-height: 100px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        resize: vertical;
      }
      
      .hk-edit-modal-buttons {
        margin-top: 15px;
        text-align: right;
      }
      
      .hk-edit-modal-buttons button {
        margin-left: 10px;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
      }
      
      .hk-save-btn {
        background: #28a745;
        color: white;
      }
      
      .hk-cancel-btn {
        background: #6c757d;
        color: white;
      }
      
      /* Edit Mode Header */
      .hk-edit-mode-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: #007cba;
        color: white;
        padding: 10px 20px;
        z-index: 9999;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      
      .hk-edit-mode-header .exit-btn {
        float: right;
        background: #dc3545;
        border: none;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
      }
    `;

    document.head.appendChild(style);
  }

  /**
   * Show edit buttons on hover
   */
  function showEditButtons(index) {
    const buttons = document.querySelector(`[data-element-index="${index}"]`);
    if (buttons) {
      buttons.classList.add("visible");
    }
  }

  /**
   * Hide edit buttons
   */
  function hideEditButtons(index) {
    const buttons = document.querySelector(`[data-element-index="${index}"]`);
    if (buttons) {
      buttons.classList.remove("visible");
    }
  }

  /**
   * Initialize Edit Mode UI
   */
  function initEditModeUI() {
    // Add edit mode header
    const header = document.createElement("div");
    header.className = "hk-edit-mode-header";
    header.innerHTML = `
      <span>🔧 Edit Mode Active - Language: ${currentTargetLanguage.toUpperCase()}</span>
      <button class="exit-btn" onclick="exitEditMode()">Exit Edit Mode</button>
    `;
    document.body.appendChild(header);

    // Adjust body padding to account for header
    document.body.style.paddingTop = "50px";
  }

  /**
   * Load existing manual translations
   */
  function loadManualTranslations() {
    // AJAX call to load existing translations
    const data = new FormData();
    data.append("action", "hk_translate_load_manual_translations");
    data.append("page_url", window.location.href);
    data.append("target_language", currentTargetLanguage);
    data.append("nonce", hk_translate_settings.nonce);

    console.log("📥 Loading manual translations...");

    fetch(hk_translate_settings.ajax_url, {
      method: "POST",
      body: data,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          console.log("✅ Manual translations loaded:", result.data);
          // Apply loaded translations
          if (result.data && result.data.translations) {
            result.data.translations.forEach((translation) => {
              manualTranslations.set(translation.text_hash, translation);
            });
          }
          applyManualTranslations();
        } else {
          console.warn(
            "❌ Failed to load manual translations:",
            result.data.message
          );
        }
      })
      .catch((error) => {
        console.error("❌ AJAX error loading translations:", error);
      });
  }

  /**
   * Apply manual translations to elements
   */
  function applyManualTranslations() {
    editableElements.forEach((item, index) => {
      // Check if element has manual translation
      if (manualTranslations.has(item.textHash)) {
        const translation = manualTranslations.get(item.textHash);
        item.element.classList.add("hk-manual-translated");

        if (translation.is_excluded) {
          item.element.classList.add("hk-excluded-element");
        }
      }
    });

    // Perform content change detection
    detectContentChanges();
  }

  /**
   * Detect if original content has changed and invalidate outdated translations
   * This handles cases where page content is updated after manual translations were made
   */
  function detectContentChanges() {
    console.log("🔍 Checking for content changes...");

    const outdatedTranslations = [];

    // Check each manual translation against current content
    manualTranslations.forEach((translationData, oldTextHash) => {
      let found = false;

      // Look for this hash in current elements
      editableElements.forEach((item) => {
        if (item.textHash === oldTextHash) {
          found = true;
        }
      });

      // If hash not found, this means content has changed
      if (!found) {
        outdatedTranslations.push({
          hash: oldTextHash,
          originalText: translationData.original_text || "Unknown",
          translation: translationData.manual_translation || "",
          isExcluded: translationData.is_excluded || false,
        });
      }
    });

    if (outdatedTranslations.length > 0) {
      console.warn(
        `⚠️ Found ${outdatedTranslations.length} outdated translations:`
      );
      outdatedTranslations.forEach((item) => {
        console.warn(
          `- "${item.originalText.substring(
            0,
            50
          )}..." -> "${item.translation.substring(0, 30)}..."`
        );
      });

      // Show user notification
      showContentChangeNotification(outdatedTranslations);

      // Mark for cleanup (optional - you can auto-clean or ask user)
      handleOutdatedTranslations(outdatedTranslations);
    } else {
      console.log("✅ All manual translations are up to date");
    }
  }

  /**
   * Show notification about content changes
   */
  function showContentChangeNotification(outdatedItems) {
    if (outdatedItems.length === 0) return;

    const notification = document.createElement("div");
    notification.className = "hk-content-change-notification";
    notification.innerHTML = `
      <div style="position: fixed; top: 60px; right: 20px; background: #fff3cd; 
                  border: 1px solid #ffeaa7; color: #856404; padding: 15px; 
                  border-radius: 5px; z-index: 10001; max-width: 400px; 
                  box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h4 style="margin: 0 0 10px 0; color: #856404;">📋 Content Changes Detected</h4>
        <p style="margin: 0 0 10px 0; font-size: 14px;">
          ${
            outdatedItems.length
          } manual translation(s) may be outdated because the original content has changed.
        </p>
        <div style="margin-bottom: 10px;">
          <strong>Affected items:</strong>
          <ul style="margin: 5px 0; padding-left: 20px; font-size: 12px;">
            ${outdatedItems
              .slice(0, 3)
              .map(
                (item) => `<li>"${item.originalText.substring(0, 40)}..."</li>`
              )
              .join("")}
            ${
              outdatedItems.length > 3
                ? `<li>...and ${outdatedItems.length - 3} more</li>`
                : ""
            }
          </ul>
        </div>
        <div style="text-align: right;">
          <button onclick="cleanupOutdatedTranslations()" 
                  style="background: #dc3545; color: white; border: none; 
                         padding: 5px 10px; border-radius: 3px; cursor: pointer; 
                         margin-right: 5px;">
            Remove Outdated
          </button>
          <button onclick="dismissContentChangeNotification()" 
                  style="background: #6c757d; color: white; border: none; 
                         padding: 5px 10px; border-radius: 3px; cursor: pointer;">
            Keep & Dismiss
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(notification);

    // Store outdated items for cleanup function
    window.hkOutdatedTranslations = outdatedItems;
  }

  /**
   * Handle outdated translations (mark for cleanup or auto-remove)
   */
  function handleOutdatedTranslations(outdatedItems) {
    // You can choose different strategies:

    // Strategy 1: Auto-remove (aggressive)
    // outdatedItems.forEach(item => {
    //   removeOutdatedTranslation(item.hash);
    // });

    // Strategy 2: Mark as outdated (conservative) - current approach
    outdatedItems.forEach((item) => {
      manualTranslations.set(item.hash + "_OUTDATED", {
        ...manualTranslations.get(item.hash),
        is_outdated: true,
        outdated_date: new Date().toISOString(),
      });
    });

    console.log("📝 Marked outdated translations for review");
  }

  /**
   * Remove an outdated translation from server and cache
   */
  function removeOutdatedTranslation(textHash) {
    const data = new FormData();
    data.append("action", "hk_translate_remove_outdated_translation");
    data.append("text_hash", textHash);
    data.append("page_url", window.location.href);
    data.append("target_language", currentTargetLanguage);
    data.append("nonce", hk_translate_settings.nonce);

    console.log("🗑️ Removing outdated translation...");

    fetch(hk_translate_settings.ajax_url, {
      method: "POST",
      body: data,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          console.log("✅ Outdated translation removed:", result.data);

          // Remove from local cache
          manualTranslations.delete(textHash);

          showNotification("🗑️ Outdated translation removed", "success");
        } else {
          console.error(
            "❌ Failed to remove translation:",
            result.data.message
          );
          showNotification(
            "❌ Failed to remove translation: " + result.data.message,
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("❌ AJAX error removing translation:", error);
        showNotification(
          "❌ Network error while removing translation",
          "error"
        );
      });
  }

  /**
   * Show notification to user
   */
  function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `hk-edit-notification hk-edit-notification-${type}`;
    notification.innerHTML = `
      <div style="position: fixed; top: 70px; right: 20px; 
                  background: ${
                    type === "success"
                      ? "#28a745"
                      : type === "error"
                      ? "#dc3545"
                      : "#007cba"
                  }; 
                  color: white; padding: 12px 20px; border-radius: 5px; 
                  z-index: 10002; font-size: 14px; max-width: 300px; 
                  box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        ${message}
      </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
      notification.remove();
    }, 3000);
  }

  /**
   * Global functions for content change notifications
   */
  window.cleanupOutdatedTranslations = function () {
    if (
      window.hkOutdatedTranslations &&
      window.hkOutdatedTranslations.length > 0
    ) {
      const count = window.hkOutdatedTranslations.length;

      if (
        confirm(
          `Are you sure you want to remove ${count} outdated translation(s)? This action cannot be undone.`
        )
      ) {
        window.hkOutdatedTranslations.forEach((item) => {
          removeOutdatedTranslation(item.hash);
        });

        // Remove notification
        document.querySelector(".hk-content-change-notification")?.remove();

        console.log(`✅ Removed ${count} outdated translations`);

        // Refresh edit mode to reflect changes
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      }
    }
  };

  window.dismissContentChangeNotification = function () {
    document.querySelector(".hk-content-change-notification")?.remove();
    console.log("📋 Content change notification dismissed");
  };

  /**
   * Start edit session tracking
   */
  function startEditSession() {
    editSession = {
      startTime: new Date(),
      edits: 0,
      pageUrl: window.location.href,
      language: currentTargetLanguage,
    };

    console.log("📊 Edit session started:", editSession);
  }

  /**
   * Check if edit mode should be activated
   */
  function checkEditModeActivation() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(EDIT_MODE_PARAM) === "1";
  }

  /**
   * Global functions for button actions
   */
  window.openEditModal = function (elementIndex) {
    const item = editableElements[elementIndex];
    if (!item) return;

    showEditModal(item, elementIndex);
  };

  window.toggleExclude = function (elementIndex) {
    const item = editableElements[elementIndex];
    if (!item) return;

    const isCurrentlyExcluded = item.element.classList.contains(
      "hk-excluded-element"
    );

    if (isCurrentlyExcluded) {
      // Remove exclusion
      item.element.classList.remove("hk-excluded-element");
      console.log(
        "✅ Removed exclusion for element:",
        item.originalText.substring(0, 50)
      );
    } else {
      // Add exclusion
      item.element.classList.add("hk-excluded-element");
      console.log("🚫 Excluded element:", item.originalText.substring(0, 50));
    }

    // Save exclusion status
    saveExclusionStatus(item, !isCurrentlyExcluded);
  };

  window.exitEditMode = function () {
    if (
      confirm(
        "Are you sure you want to exit edit mode? Any unsaved changes will be lost."
      )
    ) {
      // Remove edit mode elements and styles
      document
        .querySelectorAll(".hk-edit-buttons")
        .forEach((btn) => btn.remove());
      document.getElementById("hk-edit-mode-styles")?.remove();
      document.querySelector(".hk-edit-mode-header")?.remove();

      // Reset body padding
      document.body.style.paddingTop = "";

      // Remove URL parameter and reload
      const url = new URL(window.location);
      url.searchParams.delete(EDIT_MODE_PARAM);
      window.location.href = url.toString();
    }
  };

  /**
   * Show edit modal for translation
   */
  function showEditModal(item, elementIndex) {
    const modal = document.createElement("div");
    modal.className = "hk-edit-modal";
    modal.innerHTML = `
      <div class="hk-edit-modal-content">
        <h3>Edit Translation - ${currentTargetLanguage.toUpperCase()}</h3>
        <p><strong>Original Text:</strong></p>
        <p style="background: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
          ${item.originalText}
        </p>
        <p><strong>Manual Translation:</strong></p>
        <textarea id="manualTranslationText" placeholder="Enter your translation here...">${getExistingTranslation(
          item
        )}</textarea>
        <div class="hk-edit-modal-buttons">
          <button class="hk-cancel-btn" onclick="closeEditModal()">Cancel</button>
          <button class="hk-save-btn" onclick="saveManualTranslation(${elementIndex})">Save Translation</button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);

    // Focus on textarea
    setTimeout(() => {
      document.getElementById("manualTranslationText").focus();
    }, 100);
  }

  /**
   * Get existing translation for element
   */
  function getExistingTranslation(item) {
    if (manualTranslations.has(item.textHash)) {
      return manualTranslations.get(item.textHash).manual_translation || "";
    }
    return "";
  }

  /**
   * Close edit modal
   */
  window.closeEditModal = function () {
    document.querySelector(".hk-edit-modal")?.remove();
  };

  /**
   * Save manual translation
   */
  window.saveManualTranslation = function (elementIndex) {
    const item = editableElements[elementIndex];
    const textarea = document.getElementById("manualTranslationText");
    const translation = textarea.value.trim();

    if (!translation) {
      alert("Please enter a translation or cancel the dialog.");
      return;
    }

    // Save to server
    saveTranslationToServer(item, translation);

    // Update UI
    item.element.classList.add("hk-manual-translated");

    // Close modal
    closeEditModal();

    console.log("💾 Saved translation:", translation);
  };

  /**
   * Save translation to server
   */
  function saveTranslationToServer(item, translation) {
    const data = new FormData();
    data.append("action", "hk_translate_save_manual_translation");
    data.append("page_url", window.location.href);
    data.append("source_language", currentEditLanguage);
    data.append("target_language", currentTargetLanguage);
    data.append("original_text", item.originalText);
    data.append("manual_translation", translation);
    data.append("text_hash", item.textHash);
    data.append("element_selector", item.selector);
    data.append("nonce", hk_translate_settings.nonce);

    console.log("💾 Saving translation to server...");

    fetch(hk_translate_settings.ajax_url, {
      method: "POST",
      body: data,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          console.log("✅ Translation saved successfully:", result.data);

          // Update local cache
          manualTranslations.set(item.textHash, {
            manual_translation: translation,
            is_excluded: false,
            original_text: item.originalText,
          });

          // Increment edit session counter
          editSession.edits++;

          // Show success notification
          showNotification("✅ Translation saved successfully!", "success");
        } else {
          console.error("❌ Failed to save translation:", result.data.message);
          showNotification(
            "❌ Failed to save translation: " + result.data.message,
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("❌ AJAX error saving translation:", error);
        showNotification("❌ Network error while saving translation", "error");
      });
  }

  /**
   * Save exclusion status to server
   */
  function saveExclusionStatus(item, isExcluded) {
    const data = new FormData();
    data.append("action", "hk_translate_save_exclusion");
    data.append("page_url", window.location.href);
    data.append("target_language", currentTargetLanguage);
    data.append("text_hash", item.textHash);
    data.append("is_excluded", isExcluded ? "1" : "0");
    data.append("nonce", hk_translate_settings.nonce);

    console.log("🚫 Saving exclusion status...");

    fetch(hk_translate_settings.ajax_url, {
      method: "POST",
      body: data,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          console.log("✅ Exclusion status saved:", result.data);

          // Update local cache
          if (manualTranslations.has(item.textHash)) {
            manualTranslations.get(item.textHash).is_excluded = isExcluded;
          } else {
            manualTranslations.set(item.textHash, {
              manual_translation: "",
              is_excluded: isExcluded,
              original_text: item.originalText,
            });
          }

          // Show notification
          const message = isExcluded
            ? "🚫 Element excluded from translation"
            : "✅ Element inclusion restored";
          showNotification(message, "success");
        } else {
          console.error("❌ Failed to save exclusion:", result.data.message);
          showNotification(
            "❌ Failed to save exclusion: " + result.data.message,
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("❌ AJAX error saving exclusion:", error);
        showNotification("❌ Network error while saving exclusion", "error");
      });
  }

  /**
   * Helper function to get current language from Google Translate
   */
  function GTranslateGetCurrentLang() {
    try {
      const keyValue = document.cookie.match("(^|;) ?googtrans=([^;]*)(;|$)");
      return keyValue ? keyValue[2].split("/")[2] : null;
    } catch (e) {
      return null;
    }
  }

  /**
   * Initialize when DOM is ready
   */
  document.addEventListener("DOMContentLoaded", function () {
    if (checkEditModeActivation()) {
      console.log("🚀 Edit mode parameter detected");

      // Wait a bit for other scripts to load
      setTimeout(() => {
        initEditMode();
      }, 1000);
    }
  });

  // Export functions for testing
  window.HKTranslateEditMode = {
    initEditMode,
    checkEditModeActivation,
    isEditModeActive: () => isEditModeActive,
  };
})();
