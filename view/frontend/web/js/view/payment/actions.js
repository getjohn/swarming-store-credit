/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
/*global define*/
define(
    [
        'Swarming_StoreCredit/js/view/actions',
        'Magento_Checkout/js/action/get-payment-information'
    ],
    function (Component, getPaymentInformationAction) {
        'use strict';
        return Component.extend({
            applyCreditsCallback: function(deferred) {
                getPaymentInformationAction(deferred);
            }
        });
    }
);
