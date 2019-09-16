/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
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
