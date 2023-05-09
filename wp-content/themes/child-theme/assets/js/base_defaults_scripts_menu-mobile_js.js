"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(globalThis["webpackChunkmarkup_template"] = globalThis["webpackChunkmarkup_template"] || []).push([["base_defaults_scripts_menu-mobile_js"],{

/***/ "./base/defaults/scripts/libs/accessibility-set-modal-focus.js":
/*!*********************************************************************!*\
  !*** ./base/defaults/scripts/libs/accessibility-set-modal-focus.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ accessibilitySetModalFocus)\n/* harmony export */ });\nfunction accessibilitySetModalFocus(event, panel, closeButton) {\n  const {\n    shiftKey\n  } = event;\n  const elements = panel.querySelectorAll('input, a:not(.menu-cart-link):not(.menu-cart__close), button');\n  const tabKey = event.keyCode === 9;\n  const escKey = event.keyCode === 27;\n  const lastEl = elements.item(elements.length - 1);\n  const firstEl = closeButton;\n  const activeEl = document.activeElement;\n  if (escKey) {\n    event.preventDefault();\n    closeButton.click();\n  } else if (!shiftKey && tabKey && lastEl === activeEl) {\n    event.preventDefault();\n    firstEl.focus();\n  } else if (!shiftKey && tabKey && firstEl === activeEl) {\n    event.preventDefault();\n    elements.item(0).focus();\n  } else if (shiftKey && tabKey && firstEl === activeEl) {\n    event.preventDefault();\n    lastEl.focus();\n  } else if (shiftKey && tabKey && firstEl === activeEl) {\n    event.preventDefault();\n    lastEl.focus();\n  } else if (shiftKey && tabKey && elements.item(0) === activeEl) {\n    event.preventDefault();\n    firstEl.focus();\n  } else if (tabKey && firstEl === lastEl) {\n    event.preventDefault();\n  }\n}\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/libs/accessibility-set-modal-focus.js?");

/***/ }),

/***/ "./base/defaults/scripts/menu-mobile.js":
/*!**********************************************!*\
  !*** ./base/defaults/scripts/menu-mobile.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _libs_live_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./libs/live-dom */ \"./base/defaults/scripts/libs/live-dom.js\");\n/* harmony import */ var _libs_accessibility_set_modal_focus__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./libs/accessibility-set-modal-focus */ \"./base/defaults/scripts/libs/accessibility-set-modal-focus.js\");\n\n\n(0,_libs_live_dom__WEBPACK_IMPORTED_MODULE_0__[\"default\"])('.open-mobile-menu-button').firstShow(function () {\n  let isOpen = false;\n  const openButton = this;\n  const mobileMenu = document.querySelector('.mobile-menu');\n  const animationDuration = 400;\n  function setModalFocus(e) {\n    (0,_libs_accessibility_set_modal_focus__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(e, mobileMenu, openButton);\n  }\n  function toggleMenu() {\n    if (isOpen) {\n      openButton.setAttribute('aria-expanded', 'false');\n      mobileMenu.setAttribute('aria-hidden', 'true');\n      document.body.removeEventListener('keydown', setModalFocus);\n      openButton.focus();\n      setTimeout(() => {\n        document.body.style.paddingRight = '';\n        document.body.style.overflow = '';\n      }, animationDuration);\n    } else {\n      openButton.setAttribute('aria-expanded', 'true');\n      mobileMenu.setAttribute('aria-hidden', 'false');\n      document.body.addEventListener('keydown', setModalFocus);\n    }\n    isOpen = !isOpen;\n  }\n  openButton.addEventListener('click', toggleMenu);\n});\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/menu-mobile.js?");

/***/ })

}]);