/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
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
            getAvailable: function () {
                return creditsFormatter.formatBase(credits.getBalance(), credits.getBalanceAmount());
            }
        });
    }
);
