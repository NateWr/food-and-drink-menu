/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(2);


/***/ }),
/* 1 */
/***/ (function(module, exports) {

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var _wp$components = wp.components,
    SelectControl = _wp$components.SelectControl,
    PanelBody = _wp$components.PanelBody,
    ServerSideRender = _wp$components.ServerSideRender,
    Disabled = _wp$components.Disabled;
var InspectorControls = wp.editor.InspectorControls;
var _fdm_blocks = fdm_blocks,
    menuOptions = _fdm_blocks.menuOptions;


registerBlockType('food-and-drink-menu/menu', {
	title: __('Menu', 'food-and-drink-menu'),
	category: 'widgets',
	icon: wp.element.createElement(
		'svg',
		{ viewBox: '0 0 32 32', xmlns: 'http://www.w3.org/2000/svg' },
		wp.element.createElement('path', { d: 'M7 0c-3.314 0-6 3.134-6 7 0 3.31 1.969 6.083 4.616 6.812l-0.993 16.191c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.993-16.191c2.646-0.729 4.616-3.502 4.616-6.812 0-3.866-2.686-7-6-7zM27.167 0l-1.667 10h-1.25l-0.833-10h-0.833l-0.833 10h-1.25l-1.667-10h-0.833v13c0 0.552 0.448 1 1 1h2.604l-0.982 16.004c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.982-16.004h2.604c0.552 0 1-0.448 1-1v-13h-0.833z' })
	),
	attributes: {
		id: {
			type: 'number',
			default: 0
		}
	},
	supports: {
		html: false
	},
	edit: function edit(_ref) {
		var attributes = _ref.attributes,
		    setAttributes = _ref.setAttributes;
		var id = attributes.id;


		function setId(id) {
			setAttributes({ id: id });
		}

		return wp.element.createElement(
			'div',
			null,
			wp.element.createElement(
				InspectorControls,
				null,
				wp.element.createElement(
					PanelBody,
					null,
					wp.element.createElement(SelectControl, {
						label: __('Select a Menu', 'food-and-drink-menu'),
						value: id,
						onChange: setId,
						options: menuOptions
					})
				)
			),
			id && id !== '0' ? wp.element.createElement(
				Disabled,
				null,
				wp.element.createElement(ServerSideRender, { block: 'food-and-drink-menu/menu', attributes: attributes })
			) : wp.element.createElement(SelectControl, {
				label: __('Select a Menu'),
				value: 0,
				onChange: setId,
				options: menuOptions,
				className: 'fdm-block-select'
			})
		);
	},
	save: function save() {
		return null;
	}
});

/***/ }),
/* 2 */
/***/ (function(module, exports) {

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var _wp$components = wp.components,
    SelectControl = _wp$components.SelectControl,
    PanelBody = _wp$components.PanelBody,
    ServerSideRender = _wp$components.ServerSideRender,
    Disabled = _wp$components.Disabled;
var InspectorControls = wp.editor.InspectorControls;
var _fdm_blocks = fdm_blocks,
    menuItemOptions = _fdm_blocks.menuItemOptions;


registerBlockType('food-and-drink-menu/menu-item', {
	title: __('Menu Item', 'food-and-drink-menu'),
	category: 'widgets',
	icon: wp.element.createElement(
		'svg',
		{ viewBox: '0 0 32 32', xmlns: 'http://www.w3.org/2000/svg' },
		wp.element.createElement('path', { d: 'M7 0c-3.314 0-6 3.134-6 7 0 3.31 1.969 6.083 4.616 6.812l-0.993 16.191c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.993-16.191c2.646-0.729 4.616-3.502 4.616-6.812 0-3.866-2.686-7-6-7zM27.167 0l-1.667 10h-1.25l-0.833-10h-0.833l-0.833 10h-1.25l-1.667-10h-0.833v13c0 0.552 0.448 1 1 1h2.604l-0.982 16.004c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.982-16.004h2.604c0.552 0 1-0.448 1-1v-13h-0.833z' })
	),
	attributes: {
		id: {
			type: 'number',
			default: 0
		}
	},
	supports: {
		html: false
	},
	edit: function edit(_ref) {
		var attributes = _ref.attributes,
		    setAttributes = _ref.setAttributes;
		var id = attributes.id;


		function setId(id) {
			setAttributes({ id: id });
		}

		return wp.element.createElement(
			'div',
			{ className: 'fdm-block-menu-item-outline' },
			wp.element.createElement(
				InspectorControls,
				null,
				wp.element.createElement(
					PanelBody,
					null,
					wp.element.createElement(SelectControl, {
						label: __('Select a Menu Item', 'food-and-drink-menu'),
						value: id,
						onChange: setId,
						options: menuItemOptions
					})
				)
			),
			id && id !== '0' ? wp.element.createElement(
				Disabled,
				null,
				wp.element.createElement(ServerSideRender, { block: 'food-and-drink-menu/menu-item', attributes: attributes })
			) : wp.element.createElement(SelectControl, {
				label: __('Select a Menu Item'),
				value: 0,
				onChange: setId,
				options: menuItemOptions,
				className: 'fdm-block-select'
			})
		);
	},
	save: function save() {
		return null;
	}
});

/***/ })
/******/ ]);