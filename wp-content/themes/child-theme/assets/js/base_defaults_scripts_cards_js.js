"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(globalThis["webpackChunkmarkup_template"] = globalThis["webpackChunkmarkup_template"] || []).push([["base_defaults_scripts_cards_js"],{

/***/ "./base/defaults/scripts/cards.js":
/*!****************************************!*\
  !*** ./base/defaults/scripts/cards.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _libs_accessibility_card__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./libs/accessibility-card */ \"./base/defaults/scripts/libs/accessibility-card.js\");\n/* harmony import */ var _libs_live_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./libs/live-dom */ \"./base/defaults/scripts/libs/live-dom.js\");\n\n\n(0,_libs_live_dom__WEBPACK_IMPORTED_MODULE_1__[\"default\"])('.accessibility-card').firstShow(function () {\n  (0,_libs_accessibility_card__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(this);\n});\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/cards.js?");

/***/ }),

/***/ "./base/defaults/scripts/libs/accessibility-card.js":
/*!**********************************************************!*\
  !*** ./base/defaults/scripts/libs/accessibility-card.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ accessibilityCard)\n/* harmony export */ });\nfunction accessibilityCard(cardNode) {\n  let down;\n  let up;\n  const link = cardNode.querySelector('.title a');\n  cardNode.style.cursor = 'pointer';\n  cardNode.onmousedown = event => {\n    if (event.button === 0) {\n      down = +new Date();\n    }\n  };\n  cardNode.onmouseup = event => {\n    if (event.button === 0) {\n      up = +new Date();\n      if (up - down < 200) {\n        link.click();\n      }\n    }\n  };\n}\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/libs/accessibility-card.js?");

/***/ })

}]);