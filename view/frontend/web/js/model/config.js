/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
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
