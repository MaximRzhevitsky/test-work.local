"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkmarkup_template"] = self["webpackChunkmarkup_template"] || []).push([["src_scripts_maps_js"],{

/***/ "./base/defaults/scripts/libs/map.js":
/*!*******************************************!*\
  !*** ./base/defaults/scripts/libs/map.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"libraries\": () => (/* binding */ libraries),\n/* harmony export */   \"map\": () => (/* binding */ map)\n/* harmony export */ });\n/* harmony import */ var _live_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./live-dom */ \"./base/defaults/scripts/libs/live-dom.js\");\n\nlet googleMapsLoaded = 'not-init';\nconst libraries = [];\n\nfunction dependency(done, error) {\n  if (theme.googleMapsApiKey) {\n    if ('not-init' === googleMapsLoaded) {\n      googleMapsLoaded = 'progress';\n      const params = [\"key=\".concat(theme.googleMapsApiKey)];\n\n      if (libraries.length) {\n        params.push(\"libraries=\".concat(libraries.join(',')));\n      }\n\n      const script = document.createElement('script');\n      const src = 'https://maps.googleapis.com/maps/api/js?' + params.join('&');\n      script.setAttribute('src', src);\n      script.async = true;\n\n      script.onload = function () {\n        googleMapsLoaded = 'done';\n        done();\n      };\n\n      script.onerror = function () {\n        googleMapsLoaded = 'error';\n        error();\n      };\n\n      document.body.appendChild(script);\n    } else if ('progress' === googleMapsLoaded) {\n      setTimeout(() => dependency(done, error), 500);\n    } else if ('done' === googleMapsLoaded) {\n      done();\n    } else {\n      error();\n    }\n  } else {\n    // eslint-disable-next-line no-console\n    console.error('There is a map on the page with no API key configured.');\n    error();\n  }\n}\n\nfunction map(selector, callback) {\n  return (0,_live_dom__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(selector).dependency(dependency).firstShow(callback);\n}\n\n//# sourceURL=webpack://markup-template/./base/defaults/scripts/libs/map.js?");

/***/ }),

/***/ "./src/scripts/maps.js":
/*!*****************************!*\
  !*** ./src/scripts/maps.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _base_defaults_scripts_libs_map__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../base/defaults/scripts/libs/map */ \"./base/defaults/scripts/libs/map.js\");\n// noinspection JSCheckFunctionSignatures, JSUnresolvedVariable, JSUnresolvedFunction, JSUnresolvedVariable\n\n(0,_base_defaults_scripts_libs_map__WEBPACK_IMPORTED_MODULE_0__.map)('.map', function () {\n  /**\n   * @type {HTMLDivElement}\n   */\n  const mapCanvas = this;\n  const position = new google.maps.LatLng(mapCanvas.dataset.latitude, mapCanvas.dataset.longitude);\n  const markerIcon = mapCanvas.dataset.icon;\n  const mapZoom = mapCanvas.dataset.zoom;\n  const mapObject = new google.maps.Map(mapCanvas, {\n    zoom: mapZoom || 17,\n    center: position,\n    disableDefaultUI: true\n  });\n  new google.maps.Marker({\n    position,\n    mapObject,\n    icon: markerIcon || ''\n  });\n});\n\n//# sourceURL=webpack://markup-template/./src/scripts/maps.js?");

/***/ })

}]);