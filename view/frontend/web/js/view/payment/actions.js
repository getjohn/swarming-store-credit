/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
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
