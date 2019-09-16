/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
/*global define*/
define(
    [
        'uiComponent',
        'jquery',
        'ko',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/totals',
        'Swarming_StoreCredit/js/model/credits',
        'Swarming_StoreCredit/js/model/config',
        'Swarming_StoreCredit/js/action/apply-credits',
        'mage/translate'
    ],
    function (Component, $, ko, customerData, quoteTotals, credits, config, applyCreditsAction, $t) {
        'use strict';
        return Component.extend({
            amount: ko.observable(credits.getApplied()),
            maxAmount: ko.observable(false),
            errorMessage: ko.observable(''),
            isDisabled: ko.observable(false),

            initialize: function (options) {
                this._super(options);
                if (credits.isActive()) {
                    this.initializeObservers();
                }
            },

            initializeObservers: function () {
                var self = this;
                var amountObserver = function(value){
                    if (!$.isNumeric(value) && '' !== value) {
                        self.maxAmount(false);
                        self.isDisabled(false);
                        self.amount('');
                        self.showErrorMessage($t('Amount should be a number equal or greater than 0.'));
                        return;
                    }

                    if (value < credits.getMaxAllowedCredits()) {
                        self.maxAmount(false);
                        self.isDisabled(false);
                    } else {
                        self.maxAmount(true);
                        self.isDisabled(true);
                    }

                    if (value > credits.getMaxAllowedCredits()) {
                        self.showErrorMessage();
                    }
                };
                var maxAmountObserver = function(isMax){
                    if (isMax) {
                        self.amount(credits.getMaxAllowedCredits());
                        self.isDisabled(true);
                    } else {
                        self.isDisabled(false);
                    }
                };
                var totalsObserver = function() {
                    self.amount(credits.getApplied());
                    amountObserver(credits.getApplied());
                };
                self.amount.subscribe(amountObserver);
                self.maxAmount.subscribe(maxAmountObserver);
                quoteTotals.totals.subscribe(totalsObserver);
            },

            isApplyDisabled: function() {
                return this.amount() == credits.getApplied();
            },

            showErrorMessage: function (message) {
                var errorMessage = message || config.getValue('error_message');
                this.errorMessage(errorMessage);

                var self = this;
                setTimeout(function() {
                    self.errorMessage('');
                }, 4000);
            },

            applyCredits: function(event) {
                if (event && event.preventDefault) {
                    event.preventDefault();
                }

                var amount = parseFloat(this.amount()) || 0;
                applyCreditsAction(amount, this.applyCreditsCallback);
                customerData.invalidate(['cart', 'checkout-data']);
            },

            applyCreditsCallback: function() {},

            isApplied: function() {
                return this.amount() > 0;
            },

            cancelCredits: function() {
                this.amount(0);
                if (credits.getApplied() > 0) {
                    this.applyCredits();
                }
            }
        });
    }
);
