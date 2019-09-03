define(
    [
        'Magento_Checkout/js/model/quote'
    ],
    function (quote) {
    'use strict';

    var mixin = {

        defaults: {
            template: 'Notime_Shipping/shipping-information/address-renderer/default'
        },
        /**
         *
         * @param {Column} elem
         */
        getShippingTime: function () {
            var method = quote.shippingMethod();
            var selectedMethod = method != null ? method.carrier_code : null;
            var shippingTime = checkoutConfig.quoteData.notime_shipment_time;

            return ((shippingTime != undefined) && (selectedMethod == 'notime')) ? shippingTime : "";
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});