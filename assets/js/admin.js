/*! For license information please see admin.js.LICENSE.txt */
!function(n){var e={};function r(a){if(e[a])return e[a].exports;var t=e[a]={i:a,l:!1,exports:{}};return n[a].call(t.exports,t,t.exports,r),t.l=!0,t.exports}r.m=n,r.c=e,r.d=function(n,e,a){r.o(n,e)||Object.defineProperty(n,e,{enumerable:!0,get:a})},r.r=function(n){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(n,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(n,"__esModule",{value:!0})},r.t=function(n,e){if(1&e&&(n=r(n)),8&e)return n;if(4&e&&"object"==typeof n&&n&&n.__esModule)return n;var a=Object.create(null);if(r.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:n}),2&e&&"string"!=typeof n)for(var t in n)r.d(a,t,function(e){return n[e]}.bind(null,t));return a},r.n=function(n){var e=n&&n.__esModule?function(){return n.default}:function(){return n};return r.d(e,"a",e),e},r.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},r.p="",r(r.s=1)}({"./node_modules/url-search-params-polyfill/index.js":function(module,exports,__webpack_require__){eval("/* WEBPACK VAR INJECTION */(function(global) {/**\n *\n *\n * @author Jerry Bendy <jerry@icewingcc.com>\n * @licence MIT\n *\n */\n\n(function(self) {\n    'use strict';\n\n    var nativeURLSearchParams = (function() {\n            // #41 Fix issue in RN\n            try {\n                if (self.URLSearchParams && (new self.URLSearchParams('foo=bar')).get('foo') === 'bar') {\n                    return self.URLSearchParams;\n                }\n            } catch (e) {}\n            return null;\n        })(),\n        isSupportObjectConstructor = nativeURLSearchParams && (new nativeURLSearchParams({a: 1})).toString() === 'a=1',\n        // There is a bug in safari 10.1 (and earlier) that incorrectly decodes `%2B` as an empty space and not a plus.\n        decodesPlusesCorrectly = nativeURLSearchParams && (new nativeURLSearchParams('s=%2B').get('s') === '+'),\n        __URLSearchParams__ = \"__URLSearchParams__\",\n        // Fix bug in Edge which cannot encode ' &' correctly\n        encodesAmpersandsCorrectly = nativeURLSearchParams ? (function() {\n            var ampersandTest = new nativeURLSearchParams();\n            ampersandTest.append('s', ' &');\n            return ampersandTest.toString() === 's=+%26';\n        })() : true,\n        prototype = URLSearchParamsPolyfill.prototype,\n        iterable = !!(self.Symbol && self.Symbol.iterator);\n\n    if (nativeURLSearchParams && isSupportObjectConstructor && decodesPlusesCorrectly && encodesAmpersandsCorrectly) {\n        return;\n    }\n\n\n    /**\n     * Make a URLSearchParams instance\n     *\n     * @param {object|string|URLSearchParams} search\n     * @constructor\n     */\n    function URLSearchParamsPolyfill(search) {\n        search = search || \"\";\n\n        // support construct object with another URLSearchParams instance\n        if (search instanceof URLSearchParams || search instanceof URLSearchParamsPolyfill) {\n            search = search.toString();\n        }\n        this [__URLSearchParams__] = parseToDict(search);\n    }\n\n\n    /**\n     * Appends a specified key/value pair as a new search parameter.\n     *\n     * @param {string} name\n     * @param {string} value\n     */\n    prototype.append = function(name, value) {\n        appendTo(this [__URLSearchParams__], name, value);\n    };\n\n    /**\n     * Deletes the given search parameter, and its associated value,\n     * from the list of all search parameters.\n     *\n     * @param {string} name\n     */\n    prototype['delete'] = function(name) {\n        delete this [__URLSearchParams__] [name];\n    };\n\n    /**\n     * Returns the first value associated to the given search parameter.\n     *\n     * @param {string} name\n     * @returns {string|null}\n     */\n    prototype.get = function(name) {\n        var dict = this [__URLSearchParams__];\n        return this.has(name) ? dict[name][0] : null;\n    };\n\n    /**\n     * Returns all the values association with a given search parameter.\n     *\n     * @param {string} name\n     * @returns {Array}\n     */\n    prototype.getAll = function(name) {\n        var dict = this [__URLSearchParams__];\n        return this.has(name) ? dict [name].slice(0) : [];\n    };\n\n    /**\n     * Returns a Boolean indicating if such a search parameter exists.\n     *\n     * @param {string} name\n     * @returns {boolean}\n     */\n    prototype.has = function(name) {\n        return hasOwnProperty(this [__URLSearchParams__], name);\n    };\n\n    /**\n     * Sets the value associated to a given search parameter to\n     * the given value. If there were several values, delete the\n     * others.\n     *\n     * @param {string} name\n     * @param {string} value\n     */\n    prototype.set = function set(name, value) {\n        this [__URLSearchParams__][name] = ['' + value];\n    };\n\n    /**\n     * Returns a string containg a query string suitable for use in a URL.\n     *\n     * @returns {string}\n     */\n    prototype.toString = function() {\n        var dict = this[__URLSearchParams__], query = [], i, key, name, value;\n        for (key in dict) {\n            name = encode(key);\n            for (i = 0, value = dict[key]; i < value.length; i++) {\n                query.push(name + '=' + encode(value[i]));\n            }\n        }\n        return query.join('&');\n    };\n\n    // There is a bug in Safari 10.1 and `Proxy`ing it is not enough.\n    var forSureUsePolyfill = !decodesPlusesCorrectly;\n    var useProxy = (!forSureUsePolyfill && nativeURLSearchParams && !isSupportObjectConstructor && self.Proxy);\n    /*\n     * Apply polifill to global object and append other prototype into it\n     */\n    Object.defineProperty(self, 'URLSearchParams', {\n        value: (useProxy ?\n            // Safari 10.0 doesn't support Proxy, so it won't extend URLSearchParams on safari 10.0\n            new Proxy(nativeURLSearchParams, {\n                construct: function(target, args) {\n                    return new target((new URLSearchParamsPolyfill(args[0]).toString()));\n                }\n            }) :\n            URLSearchParamsPolyfill)\n    });\n\n    var USPProto = self.URLSearchParams.prototype;\n\n    USPProto.polyfill = true;\n\n    /**\n     *\n     * @param {function} callback\n     * @param {object} thisArg\n     */\n    USPProto.forEach = USPProto.forEach || function(callback, thisArg) {\n        var dict = parseToDict(this.toString());\n        Object.getOwnPropertyNames(dict).forEach(function(name) {\n            dict[name].forEach(function(value) {\n                callback.call(thisArg, value, name, this);\n            }, this);\n        }, this);\n    };\n\n    /**\n     * Sort all name-value pairs\n     */\n    USPProto.sort = USPProto.sort || function() {\n        var dict = parseToDict(this.toString()), keys = [], k, i, j;\n        for (k in dict) {\n            keys.push(k);\n        }\n        keys.sort();\n\n        for (i = 0; i < keys.length; i++) {\n            this['delete'](keys[i]);\n        }\n        for (i = 0; i < keys.length; i++) {\n            var key = keys[i], values = dict[key];\n            for (j = 0; j < values.length; j++) {\n                this.append(key, values[j]);\n            }\n        }\n    };\n\n    /**\n     * Returns an iterator allowing to go through all keys of\n     * the key/value pairs contained in this object.\n     *\n     * @returns {function}\n     */\n    USPProto.keys = USPProto.keys || function() {\n        var items = [];\n        this.forEach(function(item, name) {\n            items.push(name);\n        });\n        return makeIterator(items);\n    };\n\n    /**\n     * Returns an iterator allowing to go through all values of\n     * the key/value pairs contained in this object.\n     *\n     * @returns {function}\n     */\n    USPProto.values = USPProto.values || function() {\n        var items = [];\n        this.forEach(function(item) {\n            items.push(item);\n        });\n        return makeIterator(items);\n    };\n\n    /**\n     * Returns an iterator allowing to go through all key/value\n     * pairs contained in this object.\n     *\n     * @returns {function}\n     */\n    USPProto.entries = USPProto.entries || function() {\n        var items = [];\n        this.forEach(function(item, name) {\n            items.push([name, item]);\n        });\n        return makeIterator(items);\n    };\n\n\n    if (iterable) {\n        USPProto[self.Symbol.iterator] = USPProto[self.Symbol.iterator] || USPProto.entries;\n    }\n\n\n    function encode(str) {\n        var replace = {\n            '!': '%21',\n            \"'\": '%27',\n            '(': '%28',\n            ')': '%29',\n            '~': '%7E',\n            '%20': '+',\n            '%00': '\\x00'\n        };\n        return encodeURIComponent(str).replace(/[!'\\(\\)~]|%20|%00/g, function(match) {\n            return replace[match];\n        });\n    }\n\n    function decode(str) {\n        return str\n            .replace(/[ +]/g, '%20')\n            .replace(/(%[a-f0-9]{2})+/ig, function(match) {\n                return decodeURIComponent(match);\n            });\n    }\n\n    function makeIterator(arr) {\n        var iterator = {\n            next: function() {\n                var value = arr.shift();\n                return {done: value === undefined, value: value};\n            }\n        };\n\n        if (iterable) {\n            iterator[self.Symbol.iterator] = function() {\n                return iterator;\n            };\n        }\n\n        return iterator;\n    }\n\n    function parseToDict(search) {\n        var dict = {};\n\n        if (typeof search === \"object\") {\n            // if `search` is an array, treat it as a sequence\n            if (isArray(search)) {\n                for (var i = 0; i < search.length; i++) {\n                    var item = search[i];\n                    if (isArray(item) && item.length === 2) {\n                        appendTo(dict, item[0], item[1]);\n                    } else {\n                        throw new TypeError(\"Failed to construct 'URLSearchParams': Sequence initializer must only contain pair elements\");\n                    }\n                }\n\n            } else {\n                for (var key in search) {\n                    if (search.hasOwnProperty(key)) {\n                        appendTo(dict, key, search[key]);\n                    }\n                }\n            }\n\n        } else {\n            // remove first '?'\n            if (search.indexOf(\"?\") === 0) {\n                search = search.slice(1);\n            }\n\n            var pairs = search.split(\"&\");\n            for (var j = 0; j < pairs.length; j++) {\n                var value = pairs [j],\n                    index = value.indexOf('=');\n\n                if (-1 < index) {\n                    appendTo(dict, decode(value.slice(0, index)), decode(value.slice(index + 1)));\n\n                } else {\n                    if (value) {\n                        appendTo(dict, decode(value), '');\n                    }\n                }\n            }\n        }\n\n        return dict;\n    }\n\n    function appendTo(dict, name, value) {\n        var val = typeof value === 'string' ? value : (\n            value !== null && value !== undefined && typeof value.toString === 'function' ? value.toString() : JSON.stringify(value)\n        );\n\n        // #47 Prevent using `hasOwnProperty` as a property name\n        if (hasOwnProperty(dict, name)) {\n            dict[name].push(val);\n        } else {\n            dict[name] = [val];\n        }\n    }\n\n    function isArray(val) {\n        return !!val && '[object Array]' === Object.prototype.toString.call(val);\n    }\n\n    function hasOwnProperty(obj, prop) {\n        return Object.prototype.hasOwnProperty.call(obj, prop);\n    }\n\n})(typeof global !== 'undefined' ? global : (typeof window !== 'undefined' ? window : this));\n\n/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../webpack/buildin/global.js */ \"./node_modules/webpack/buildin/global.js\")))\n\n//# sourceURL=webpack:///./node_modules/url-search-params-polyfill/index.js?")},"./node_modules/webpack/buildin/global.js":function(module,exports){eval('var g;\n\n// This works in non-strict mode\ng = (function() {\n\treturn this;\n})();\n\ntry {\n\t// This works if eval is allowed (see CSP)\n\tg = g || new Function("return this")();\n} catch (e) {\n\t// This works if the window reference is available\n\tif (typeof window === "object") g = window;\n}\n\n// g can still be undefined, but nothing to do about it...\n// We return undefined, instead of nothing here, so it\'s\n// easier to handle this case. if(!global) { ...}\n\nmodule.exports = g;\n\n\n//# sourceURL=webpack:///(webpack)/buildin/global.js?')},"./src/js/admin.js":function(module,__webpack_exports__,__webpack_require__){"use strict";eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _components_tabs_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/_tabs.js */ \"./src/js/components/_tabs.js\");\n/* harmony import */ var url_search_params_polyfill__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! url-search-params-polyfill */ \"./node_modules/url-search-params-polyfill/index.js\");\n/* harmony import */ var url_search_params_polyfill__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(url_search_params_polyfill__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _components_accordion__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/_accordion */ \"./src/js/components/_accordion.js\");\n/**!\n * SaleXpresso Admin Scripts\n *\n * @author SaleXpresso <support@salexpresso.com>\n * @package SaleXpresso\n * @version 1.0.0\n * @since 1.0.0\n */\n\n\n // import $ from 'jquery';\n// import { sprintf, _n } from '@wordpress/i18n';\n\n(function ($, window, document, wp, pagenow, SaleXpresso) {\n  var params = new URLSearchParams(location.search);\n  var sxp_page = 0 === pagenow.indexOf('salexpresso_page_') ? params.get('page') : false;\n  var sxp_sub_page = sxp_page ? params.get('tab') : false;\n  $(window).on('load', function () {\n    var sxhWrapper = $('.sxp-wrapper');\n    $(document).on('change', '.selector', function (event) {\n      event.preventDefault();\n    });\n\n    if (sxhWrapper.hasClass('sxp-has-tabs')) {\n      Object(_components_tabs_js__WEBPACK_IMPORTED_MODULE_0__[\"tabs\"])();\n    }\n  }); // date range picker\n\n  $(function () {\n    var start = moment().subtract(29, 'days');\n    var end = moment();\n\n    function cb(start, end) {\n      $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));\n    }\n\n    $('#reportrange').daterangepicker({\n      startDate: start,\n      endDate: end,\n      ranges: {\n        Today: [moment(), moment()],\n        Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],\n        'Last 7 Days': [moment().subtract(6, 'days'), moment()],\n        'Last 30 Days': [moment().subtract(29, 'days'), moment()],\n        'This Month': [moment().startOf('month'), moment().endOf('month')],\n        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]\n      }\n    }, cb);\n    cb(start, end);\n  }); // Accordion Table\n\n  $(function () {\n    $(\".sxp-table tr.has-fold\").on(\"click\", function () {\n      if ($(this).hasClass(\"open\")) {\n        $(this).removeClass(\"open\").next(\".fold\").removeClass(\"open\");\n      } else {\n        $(\".sxp-table tr.has-fold\").removeClass(\"open\").next(\".fold\").removeClass(\"open\");\n        $(this).addClass(\"open\").next(\".fold\").addClass(\"open\");\n      }\n    });\n  }); // Initiate Feather Icon\n\n  feather.replace({\n    'stroke-width': 2,\n    'width': 16,\n    'height': 16\n  }); // customer profile tab\n\n  $('ul.tabs li').click(function () {\n    var tab_id = $(this).attr('data-tab');\n    $('ul.tabs li').removeClass('current');\n    $('.tab-content').removeClass('current');\n    $(this).addClass('current');\n    $(\"#\" + tab_id).addClass('current');\n  });\n})(jQuery, window, document, wp, pagenow, SaleXpresso);\n\n//# sourceURL=webpack:///./src/js/admin.js?")},"./src/js/components/_accordion.js":function(module,__webpack_exports__,__webpack_require__){"use strict";eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Accordion", function() { return Accordion; });\n/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");\n/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);\n\nvar CLASS_NAME_HAS_FOLD = \'has-fold\';\nvar CLASS_NAME_FOLD = \'fold\';\nvar CLASS_NAME_OPEN = \'open\';\n\nfunction Accordion(el) {\n  if (!el.find(\'tr\').hasClass(CLASS_NAME_HAS_FOLD) || !el.find(\'tr\').hasClass(CLASS_NAME_FOLD)) {\n    return;\n  }\n\n  var hide = function hide(elem) {\n    return elem.removeClass(CLASS_NAME_OPEN).next(".".concat(CLASS_NAME_FOLD)).removeClass(CLASS_NAME_OPEN);\n  };\n\n  var show = function show(elem) {\n    return elem.addClass(CLASS_NAME_OPEN).next(".".concat(CLASS_NAME_FOLD)).addClass(CLASS_NAME_OPEN);\n  };\n\n  return el.find("tr.".concat(CLASS_NAME_HAS_FOLD)).on(\'click\', function (e) {\n    e.preventDefault();\n    e.stopPropagation();\n    var row = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this);\n\n    if (row.hasClass(CLASS_NAME_OPEN)) {\n      hide(row);\n    } else {\n      hide(el.find("tr.".concat(CLASS_NAME_FOLD)));\n      show(row);\n      console.log(".".concat(CLASS_NAME_FOLD));\n    }\n  });\n}\n\njquery__WEBPACK_IMPORTED_MODULE_0___default.a.fn.tableAccordion = function () {\n  return jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).each(function () {\n    return Accordion(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this));\n  });\n};\n\n\n\n//# sourceURL=webpack:///./src/js/components/_accordion.js?')},"./src/js/components/_tabs.js":function(module,__webpack_exports__,__webpack_require__){"use strict";eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"tabs\", function() { return tabs; });\n/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ \"jquery\");\n/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);\n\n\nfunction tabs() {\n  return jquery__WEBPACK_IMPORTED_MODULE_0___default()(document).on('click', '[data-target]', function (event) {\n    var self = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this),\n        tab = self.closest('.tab-item'),\n        target = jquery__WEBPACK_IMPORTED_MODULE_0___default()(\"#\".concat(self.data('target')));\n\n    if (target.length) {\n      // Switch to the tab.\n      event.preventDefault();\n\n      if (!tab.hasClass('is-active')) {\n        jquery__WEBPACK_IMPORTED_MODULE_0___default()('.tab-item').removeClass('is-active');\n        tab.addClass('is-active');\n        jquery__WEBPACK_IMPORTED_MODULE_0___default()('.tab-content').removeClass('is-active');\n        target.addClass('is-active');\n      }\n\n      self.trigger('shown');\n    }\n  });\n}\n\n\n\n//# sourceURL=webpack:///./src/js/components/_tabs.js?")},"./src/scss/admin.scss":function(module,__webpack_exports__,__webpack_require__){"use strict";eval('__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__["default"] = (__webpack_require__.p + "./assets/css/admin.css");\n\n//# sourceURL=webpack:///./src/scss/admin.scss?')},1:function(module,exports,__webpack_require__){eval('__webpack_require__(/*! ./src/js/admin.js */"./src/js/admin.js");\nmodule.exports = __webpack_require__(/*! ./src/scss/admin.scss */"./src/scss/admin.scss");\n\n\n//# sourceURL=webpack:///multi_./src/js/admin.js_./src/scss/admin.scss?')},jquery:function(module,exports){eval("module.exports = jQuery;\n\n//# sourceURL=webpack:///external_%22jQuery%22?")}});