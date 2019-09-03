define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/checkout-data'
    ],function ($, ko, Component, selectShippingAddressAction, quote, formPopUpState, checkoutData) {
        'use strict';

        var mixin = {

            selectAddress: function() {

                selectShippingAddressAction(this.address());
                checkoutData.setSelectedShippingAddress(this.address().getKey());

                $("#notimepostcode").val(quote.shippingAddress._latestValue.postcode);
                $('#notimeWidgetButton-container').html(checkoutConfig.quoteData.notimeWidgetButton);
                initNotmeWidget();
            }
        };

        return function (target) { // target == Result that Magento_Ui/.../default returns.
            return target.extend(mixin); // new result that all other modules receive
        };
    });