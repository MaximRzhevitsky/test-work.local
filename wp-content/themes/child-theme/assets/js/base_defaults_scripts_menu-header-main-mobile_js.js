"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(globalThis["webpackChunkmarkup_template"] = globalThis["webpackChunkmarkup_template"] || []).push([["base_defaults_scripts_menu-header-main-mobile_js"],{

/***/ "./base/defaults/scripts/menu-header-main-mobile.js":
/*!**********************************************************!*\
  !*** ./base/defaults/scripts/menu-header-main-mobile.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _libs_live_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./libs/live-dom */ \"./base/defaults/scripts/libs/live-dom.js\");\n\n(0,_libs_live_dom__WEBPACK_IMPORTED_MODULE_0__[\"default\"])('.menu-header-main-mobile').firstShow(function () {\n  this.querySelectorAll('.menu-header-main-mobile__item_has-sub-items').forEach(itemHasSubItems => {\n    const expandWrapper = document.createElement('div');\n    const expandButton = document.createElement('button');\n    expandWrapper.classList.add('menu-header-main-mobile__expand-wrapper');\n    expandButton.classList.add('menu-header-main-mobile__expand-btn');\n    expandButton.insertAdjacentHTML('afterbegin', '<span class=\"screen-reader-text\">Expand</span>');\n    expandButton.addEventListener('click', event => {\n      const item = event.currentTarget.closest('.menu-header-main-mobile__item');\n      item.querySelector('.menu-header-main-mobile__sub-items').classList.toggle('menu-header-main-mobile__sub-items_open');\n      item.querySelector('.menu-header-main-mobile__expand-btn').classList.toggle('menu-header-main-mobile__expand-btn_open');\n    });\n    itemHasSubItems.prepend(expandWrapper);\n    expandWrapper.append(itemHasSubItems.querySelector('a'), expandButton);\n  });\n});\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/menu-header-main-mobile.js?");

/***/ })

}]);