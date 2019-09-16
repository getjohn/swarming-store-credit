/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
/*global define*/
define(
    [
        'underscore',
        'uiComponent',
        'Magento_Checkout/js/model/totals',
        'Swarming_StoreCredit/js/model/credits',
        'Swarming_StoreCredit/js/model/credits/formatter'
    ],
    function (_, Component, totals, credits, creditsFormatter) {
        "use strict";
        return Component.extend({
            getUnitName: function() {
                return creditsFormatter.getUnitName();
            },

            getValue: function() {
                var segmentData = totals.getSegment("swarming_credits");
                if (segmentData && _.isNumber(segmentData.value) && segmentData.value !== 0) {
                    return creditsFormatter.formatTotal(credits.getApplied(), segmentData.value);
                }
                return false;
            },

            isDisplayed: function () {
                return this.getValue() !== false;
            }
        });
    }
);
