"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

var _react = _interopRequireDefault(require("react"));

var _reactDom = _interopRequireDefault(require("react-dom"));

var _nanoid = _interopRequireDefault(require("nanoid"));

var _immer = _interopRequireDefault(require("immer"));

var apiFetch = _interopRequireWildcard(require("@wordpress/api-fetch"));

var compose = _interopRequireWildcard(require("@wordpress/compose"));

var element = _interopRequireWildcard(require("@wordpress/element"));

var hooks = _interopRequireWildcard(require("@wordpress/hooks"));

var data = _interopRequireWildcard(require("@wordpress/data"));

var i18n = _interopRequireWildcard(require("@wordpress/i18n"));

var classnames = _interopRequireWildcard(require("classnames"));

var _ = _interopRequireWildcard(require("lodash"));

var refractCallbag = _interopRequireWildcard(require("refract-callbag"));

var callbagBasics = _interopRequireWildcard(require("callbag-basics"));

function _getRequireWildcardCache() { if (typeof WeakMap !== "function") return null; var cache = new WeakMap(); _getRequireWildcardCache = function _getRequireWildcardCache() { return cache; }; return cache; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) { return; } var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

/**
 * Prevent the conflicts with WordPress's Underscore lib and other 3rd party plugins.
 */
_.noConflict();
/**
 * Setup the vendor variables used by Carbon Fields.
 */


window.cf = window.cf || {};
window.cf.vendor = [['react', _react["default"]], ['react-dom', _reactDom["default"]], ['nanoid', _nanoid["default"]], ['immer', _immer["default"]], ['@wordpress/api-fetch', apiFetch], ['@wordpress/compose', compose], ['@wordpress/element', element], ['@wordpress/hooks', hooks], ['@wordpress/data', data], ['@wordpress/i18n', i18n], ['classnames', classnames], ['lodash', _], ['refract-callbag', refractCallbag], ['callbag-basics', callbagBasics]].reduce(function (vendors, _ref) {
  var _ref2 = _slicedToArray(_ref, 2),
      key = _ref2[0],
      implementation = _ref2[1];

  vendors[key] = implementation;
  return vendors;
}, {});
/**
 * Setup the enviroment variables used by Carbon Fields.
 */

window.cf.hooks = hooks;
window.cf.element = element;