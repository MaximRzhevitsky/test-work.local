"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(globalThis["webpackChunkmarkup_template"] = globalThis["webpackChunkmarkup_template"] || []).push([["base_defaults_scripts_disabled-visual-focus_js"],{

/***/ "./base/defaults/scripts/disabled-visual-focus.js":
/*!********************************************************!*\
  !*** ./base/defaults/scripts/disabled-visual-focus.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _libs_live_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./libs/live-dom */ \"./base/defaults/scripts/libs/live-dom.js\");\n\nconst disabledVisualFocus = 'disabled-visual-focus';\n// Don't use it here input:not(:where(...))\n// Since older browsers do not support where and break everything :(\nconst excludedTypes = ['button', 'checkbox', 'file', 'hidden', 'image', 'radio', 'range', 'reset', 'submit'];\nlet selector = 'textarea,input';\nexcludedTypes.forEach(excludedType => {\n  selector += `:not([type=\"${excludedType}\"])`;\n});\n(0,_libs_live_dom__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(selector).init(function () {\n  /**\n   * @type {HTMLInputElement|HTMLTextAreaElement}\n   */\n  const input = this;\n  input.addEventListener('mousedown', () => {\n    if (input !== document.activeElement) {\n      input.classList.add(disabledVisualFocus);\n    }\n  });\n  input.addEventListener('blur', () => {\n    input.classList.remove(disabledVisualFocus);\n  });\n});\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/disabled-visual-focus.js?");

/***/ })

}]);