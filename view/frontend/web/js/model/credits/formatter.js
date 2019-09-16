/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
define(
    [
        'underscore',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/quote',
        'Swarming_StoreCredit/js/model/config'
    ],
    function (_, priceUtils, quote, config) {
        'use strict';

        var preparePlaceholders = function (credits, currencyAmount) {
            return {
                '{{name}}': config.getValue('name'),
                '{{icon}}': config.getValue('icon'),
                '{{symbol}}': config.getValue('symbol'),
                '{{credits}}': credits,
                '{{currency_amount}}': currencyAmount
            };
        };

        return {
            getBlockTitle: function() {
              return config.getValue('block_title');
            },
            getUnitName: function() {
                return config.getValue('name');
            },
            formatBase: function (credits, currencyAmount) {
                return this.processFormat(config.getValue('base_format'), credits, currencyAmount);
            },
            formatTotal: function (credits, currencyAmount) {
                return this.processFormat(config.getValue('total_format'), credits, currencyAmount);
            },
            processFormat: function (format, credits, currencyAmount) {
                var priceFormat = _.clone(quote.getPriceFormat());
                priceFormat.requiredPrecision = config.getValue('precision');
                priceFormat.precision = config.getValue('precision');
                priceFormat.pattern = "%s";

                credits = priceUtils.formatPrice(credits, priceFormat, false);
                currencyAmount = priceUtils.formatPrice(currencyAmount, quote.getPriceFormat());

                var placeholders = preparePlaceholders(credits, currencyAmount);
                for (var index in placeholders) {
                    if (placeholders.hasOwnProperty(index)) {
                        format = format.replace(index, placeholders[index]);
                    }
                }
                return format;
            }
        };
    }
);
