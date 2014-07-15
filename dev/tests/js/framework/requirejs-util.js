/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

(function ($, window) {
    "use strict";

    // List of define() calls with arguments and call stack
    var defineCalls = [];

    // Get current call stack, including script path information
    var getFileStack = function() {
        try {
            throw new Error();
        } catch (e) {
            if (!e.stack) {
                throw new Error('The browser needs to support Error.stack property');
            }
            return e.stack;
        }
    };

    // Intercept RequireJS define() calls, which are performed by AMD scripts upon loading
    window.define = function () {
        var stack = getFileStack();
        defineCalls.push({
            stack: stack,
            args: arguments
        });
    };

    // Exposed interface
    var requirejsUtil = {
        getDefineArgsInScript: function (scriptPath) {
            var result;
            for (var i = 0; i < defineCalls.length; i++) {
                if (defineCalls[i].stack.indexOf(scriptPath) >= 0) {
                    result = defineCalls[i].args;
                    break;
                }
            }
            return result;
        }
    };

    window.jsunit = window.jsunit || {};
    $.extend(window.jsunit, {requirejsUtil: requirejsUtil});
})(jQuery, window);
