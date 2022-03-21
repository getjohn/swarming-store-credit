/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
/*global define*/
define(
    [
        'uiComponent',
        'Swarming_StoreCredit/js/model/credits',
        'Swarming_StoreCredit/js/model/credits/formatter'
    ],
    function (Component, credits, creditsFormatter) {
        'use strict';

        return Component.extend({
            isAvailable: function() {
                return credits.isActive() && credits.getBalance() > 0;
            },
            getBlockTitle: function() {
                return creditsFormatter.getBlockTitle();
            }
        });
    }
);
