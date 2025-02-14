/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
/*global define,alert*/
define(
    [
        'jquery',
        'Swarming_StoreCredit/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/model/totals'
    ],
    function ($, resourceUrlManager, errorProcessor, storage, totals) {
        "use strict";
        return function (amount, callback) {
            totals.isLoading(true);
            return storage.post(
                resourceUrlManager.getUrlCreditsApply(),
                JSON.stringify({amount: amount}),
                false
            ).done(
                function () {
                    var deferred = $.Deferred();
                    callback(deferred);
                    $.when(deferred).done(function () {
                        totals.isLoading(false);
                    });
                }
            ).fail(
                function (response) {
                    totals.isLoading(false);
                    errorProcessor.process(response);
                }
            );
        };
    }
);
