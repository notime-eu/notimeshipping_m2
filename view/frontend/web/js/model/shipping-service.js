/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'mage/url'
    ],
    function ($, ko, checkoutDataResolver,quote, rateRegistry , url) {
        "use strict";
        var shippingRates = ko.observableArray([]);
        return {
            isLoading: ko.observable(false),
            /**
             * Set shipping rates
             *
             * @param ratesData
             */
            setShippingRates: function(ratesData) {
                shippingRates(ratesData);
                shippingRates.valueHasMutated();
                checkoutDataResolver.resolveShippingRates(ratesData);

                $("#notimepostcode").val(quote.shippingAddress._latestValue.postcode);

                var newValue = quote.shippingAddress._latestValue.postcode,
                    oldValue = $('#notimeButtonZipCode').val();
                if ((oldValue !== undefined) && (newValue !== oldValue)) {
                    window.widget.flag(false);
                    checkoutConfig.quoteData.notime_shipment_id = null;
                    checkoutConfig.quoteData.notime_service_id = null;
                    checkoutConfig.quoteData.notime_timewindow_date = null;
                    $.ajax({
                        url: url.build('notime/rate/update'),
                        type: 'POST',
                        data: {
                            shipmentId: null,
                            timeWindowDate: null,
                            serviceId: null,
                            shipmentTime: null,
                            fee: null
                        },
                        showLoader: false,
                        complete: function(data) {
                            var address = quote.shippingAddress();

                            // address.trigger_reload = new Date().getTime();
                            rateRegistry.set(address.getKey(), null);
                            rateRegistry.set(address.getCacheKey(), null);
                            quote.shippingAddress(address);
                        }
                    });
                }
                $('#notimeWidgetButton-container').html(checkoutConfig.quoteData.notimeWidgetButton);
                initNotmeWidget();
            },

            /**
             * Get shipping rates
             *
             * @returns {*}
             */
            getShippingRates: function() {
                return shippingRates;
            }
        };
    }
);
