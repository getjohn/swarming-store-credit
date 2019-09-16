/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
require([
    "jquery",
    "jquery/ui"
], function ($) {
    $(function () {
        "use strict";

        $.widget("swarming.orderCredits", {
            options: {
                amount: 0,
                maxAmount: 0,
                callback: function() {},
                amountSelector: ".swarming-credits-amount",
                maxSelector: ".swarming-credits-max",
                applySelector: ".action-apply",
                cancelSelector: ".action-cancel"
            },

            _create: function() {
                this.element.on("input", this.options.amountSelector, $.proxy(this._processAmount, this));
                this.element.on("change", this.options.maxSelector, $.proxy(this._processMax, this));
                this.element.on("click", this.options.applySelector, $.proxy(this._processApply, this));
                this.element.on("click", this.options.cancelSelector, $.proxy(this._processCancel, this));

                this._init();
            },

            _init: function() {
                this._setAmount(this.options.amount);
                this._processAmount();
            },

            _processAmount: function() {
                var amount = this._getAmount();
                if (amount >= this.options.maxAmount) {
                    this._setMax();
                } else {
                    this._processApplyButton(amount);
                    this._processCancelButton(amount);
                }
                return amount;
            },

            _processApply: function() {
                var amount = this._processAmount();
                if (amount) {
                    this.options.callback(amount);
                }
            },

            _processApplyButton: function(amount) {
                if (amount == this.options.amount) {
                    this._applyDisable(true);
                } else {
                    this._applyDisable(false);
                }
            },

            _processCancel: function() {
                this._unsetMax();
                this._setAmount(0);
                if (this.options.amount > 0) {
                    this.options.callback(0);
                } else {
                    this._processAmount();
                }
            },

            _processCancelButton: function(amount) {
                if (amount == 0) {
                    this._cancelDisable(true);
                } else {
                    this._cancelDisable(false);
                }
            },

            _processMax: function(event) {
                if ($(event.target).is(":checked")) {
                    this._setMax();
                } else {
                    this._unsetMax();
                }
            },

            _setMax: function(){
                this._setAmount(this.options.maxAmount);
                this._amountDisable(true);
                this._maxCheck(true);
                this._processApplyButton(this.options.maxAmount);
                this._processCancelButton(this.options.maxAmount);
            },

            _unsetMax: function() {
                this._amountDisable(false);
                this._maxCheck(false);
            },

            _setAmount: function(amount) {
                $(this.element).find(this.options.amountSelector).val(amount);
            },

            _getAmount: function() {
                return $(this.element).find(this.options.amountSelector).val();
            },

            _maxCheck: function(checked) {
                $(this.element).find(this.options.maxSelector).prop("checked", checked);
            },

            _amountDisable: function(disable) {
                $(this.element).find(this.options.amountSelector).prop("disabled", disable);
            },

            _applyDisable: function(disable) {
                $(this.element).find(this.options.applySelector).prop("disabled", disable);
            },

            _cancelDisable: function(disable) {
                $(this.element).find(this.options.cancelSelector).prop("disabled", disable);
            }
        });
    });
});
