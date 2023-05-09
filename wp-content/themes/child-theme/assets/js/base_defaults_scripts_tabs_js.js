"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(globalThis["webpackChunkmarkup_template"] = globalThis["webpackChunkmarkup_template"] || []).push([["base_defaults_scripts_tabs_js"],{

/***/ "./base/defaults/scripts/libs/accessibility-tabs.js":
/*!**********************************************************!*\
  !*** ./base/defaults/scripts/libs/accessibility-tabs.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ accessibilityTabs)\n/* harmony export */ });\nfunction accessibilityTabs(tabListNode) {\n  const tabs = [];\n  const tabPanels = [];\n  let firstTab = null;\n  let lastTab = null;\n  function setSelectedTab(currentTab) {\n    const tabsLength = tabs.length;\n    for (let i = 0; i < tabsLength; i += 1) {\n      const tab = tabs[i];\n      if (currentTab === tab) {\n        tab.setAttribute('aria-selected', 'true');\n        tab.removeAttribute('tabindex');\n        tabPanels[i].classList.remove('is-hidden');\n      } else {\n        tab.setAttribute('aria-selected', 'false');\n        tab.tabIndex = -1;\n        tabPanels[i].classList.add('is-hidden');\n      }\n    }\n  }\n  function moveFocusToTab(currentTab) {\n    currentTab.focus();\n  }\n  function moveFocusToPreviousTab(currentTab) {\n    let index;\n    if (currentTab === firstTab) {\n      moveFocusToTab(lastTab);\n    } else {\n      index = tabs.indexOf(currentTab);\n      moveFocusToTab(tabs[index - 1]);\n    }\n  }\n  function moveFocusToNextTab(currentTab) {\n    let index;\n    if (currentTab === lastTab) {\n      moveFocusToTab(firstTab);\n    } else {\n      index = tabs.indexOf(currentTab);\n      moveFocusToTab(tabs[index + 1]);\n    }\n  }\n  function onKeydown(event) {\n    const tgt = event.currentTarget;\n    let flag = false;\n    switch (event.key) {\n      case 'ArrowLeft':\n        moveFocusToPreviousTab(tgt);\n        flag = true;\n        break;\n      case 'ArrowRight':\n        moveFocusToNextTab(tgt);\n        flag = true;\n        break;\n      case 'Home':\n        moveFocusToTab(firstTab);\n        flag = true;\n        break;\n      case 'End':\n        moveFocusToTab(lastTab);\n        flag = true;\n        break;\n      default:\n        break;\n    }\n    if (flag) {\n      event.stopPropagation();\n      event.preventDefault();\n    }\n  }\n  function onClick(event) {\n    setSelectedTab(event.currentTarget);\n  }\n  function init() {\n    tabs.push(...Array.from(tabListNode.querySelectorAll('[role=tab]')));\n    const tabsLength = tabs.length;\n    for (let i = 0; i < tabsLength; i += 1) {\n      const tab = tabs[i];\n      const tabPanel = document.getElementById(tab.getAttribute('aria-controls'));\n      tab.tabIndex = -1;\n      tab.setAttribute('aria-selected', 'false');\n      tabPanels.push(tabPanel);\n      tab.addEventListener('keydown', onKeydown);\n      tab.addEventListener('click', onClick);\n      if (!firstTab) {\n        firstTab = tab;\n      }\n      lastTab = tab;\n    }\n    setSelectedTab(firstTab);\n  }\n  init();\n}\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/libs/accessibility-tabs.js?");

/***/ }),

/***/ "./base/defaults/scripts/tabs.js":
/*!***************************************!*\
  !*** ./base/defaults/scripts/tabs.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _libs_live_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./libs/live-dom */ \"./base/defaults/scripts/libs/live-dom.js\");\n/* harmony import */ var _libs_accessibility_tabs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./libs/accessibility-tabs */ \"./base/defaults/scripts/libs/accessibility-tabs.js\");\n\n\n(0,_libs_live_dom__WEBPACK_IMPORTED_MODULE_0__[\"default\"])('.tabs').firstShow(function () {\n  (0,_libs_accessibility_tabs__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(this);\n});\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/tabs.js?");

/***/ })

}]);