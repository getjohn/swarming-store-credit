/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
define(
    ['underscore', 'ko', 'Magento_Checkout/js/model/totals'],
    function (_, ko, totals) {
        'use strict';

        if (window.checkoutConfig.swarming && window.checkoutConfig.swarming.creditsCustomerData) {
            var creditsCustomerData = window.checkoutConfig.swarming.creditsCustomerData;
        }

        function getTotalDetails (code) {
            var totalsData = totals.totals();
            var details = totalsData.extension_attributes && totalsData.extension_attributes.credits_total_details
                ? totalsData.extension_attributes.credits_total_details
                : {};
            return code ? parseFloat(details[code]) : details;
        }

        return {
            isActive: function() {
                return !_.isEmpty(creditsCustomerData);
            },
            getBalance: function () {
                return this.isActive() ? parseFloat(creditsCustomerData.balance) : 0;
            },
            getBalanceAmount: function () {
                return this.isActive() ? parseFloat(creditsCustomerData.amount) : 0;
            },
            getBalanceBaseAmount: function () {
                return this.isActive() ? parseFloat(creditsCustomerData.base_amount) : 0;
            },
            getMaxAllowedCredits: function() {
                return this.getBalance() < getTotalDetails('max_allowed_credits')
                    ? this.getBalance()
                    : getTotalDetails('max_allowed_credits');
            },
            getApplied: function() {
                return getTotalDetails('credits');
            },
            getAppliedAmount: function() {
                return getTotalDetails('credits_amount');
            },
            getAppliedBaseAmount: function() {
                return getTotalDetails('base_credits_amount');
            },
            getAvailable: function() {
                return this.getBalance() - this.getApplied();
            },
            getAvailableAmount: function() {
                return this.getBalanceAmount() - this.getAppliedAmount();
            },
            getAvailableBaseAmount: function() {
                return this.getBalanceBaseAmount - this.getAppliedBaseAmount();
            }
        };
    }
);
