/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
/*global define*/
define(
    [
        'Swarming_StoreCredit/js/view/actions',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/cart/cache',
        'Magento_Checkout/js/model/cart/totals-processor/default'
    ],
    function (Component, quote, cartCache, totalsDefaultProvider) {
        'use strict';
        return Component.extend({
            applyCreditsCallback: function() {
                cartCache.clear('totals');
                totalsDefaultProvider.estimateTotals(quote.shippingAddress());
            }
        });
    }
);
