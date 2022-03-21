/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Checkout/js/model/url-builder',
    ],
    function(urlBuilder) {
        "use strict";
        return {
            getUrlCreditsApply: function() {
                return urlBuilder.createUrl('/swarming/credits/apply', {});
            }
        };
    }
);
