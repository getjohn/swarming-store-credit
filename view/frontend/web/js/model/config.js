/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
define(
    [],
    function () {
        'use strict';

        var creditsConfigData = {};
        if (window.checkoutConfig.swarming && window.checkoutConfig.swarming.creditsConfigData) {
            creditsConfigData = window.checkoutConfig.swarming.creditsConfigData;
        }

        return {
            getValue: function(code) {
                return creditsConfigData[code];
            }
        };
    }
);
