define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Notime_Shipping/js/model/shipping-save-processor/default',
    'Magento_Checkout/js/model/shipping-save-processor',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'mage/url'
], function (
    $,
    ko,
    Component,
    quote,
    defaultSaveProcessor,
    shippingSaveProcessor,
    rateRegistry,
    url
){
    'use strict';

    shippingSaveProcessor.registerProcessor('default', defaultSaveProcessor);

    ko.bindingHandlers.initNotimeWidget = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            // This will be called when the binding is first applied to an element
            // Set up any initial state, event handlers, etc. here
            $('#notimeWidgetButton-container').html(checkoutConfig.quoteData.notimeWidgetButton);

            if ($('#notime_shipment_id').length > 0) {
                // console.log(checkoutConfig.quoteData);
                initNotmeWidget();
            }

            var timeWindowConfirmedHandler = function(event){
                var confirmedTimeWindowDescription = event.detail.selectedTimeWindowDescription;
                checkoutConfig.quoteData.notime_shipment_time = confirmedTimeWindowDescription;
            };

            var shipmentGeneratedHandler = function (event) {
                var shipmentId = event.detail.generatedShippmentGuid;
                var serviceId = event.detail.selectedServiceGuid;
                var timeWindowDate = event.detail.selectedTimeWindowDate;
                var shipmentFee = event.detail.fee;

                if (shipmentId) {
                    checkoutConfig.quoteData.notime_shipment_id = shipmentId;
                    checkoutConfig.quoteData.notime_timewindow_date = timeWindowDate;
                    checkoutConfig.quoteData.notime_service_id = serviceId;

                    var shipmentGenerateFinished = new CustomEvent('shipmentGenerateFinished');
                    document.body.dispatchEvent(shipmentGenerateFinished);

                    $.ajax({
                        url: url.build('notime/rate/update'),
                        type: 'POST',
                        data: {
                            shipmentId: shipmentId,
                            timeWindowDate: timeWindowDate,
                            serviceId: serviceId,
                            shipmentTime: checkoutConfig.quoteData.notime_shipment_time,
                            fee: shipmentFee
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
            };
            document.body.addEventListener("notime_widget:shipmentGenerated", shipmentGeneratedHandler);
            document.body.addEventListener("notime_widget:timeWindowConfirmed",timeWindowConfirmedHandler);
        }
    };

    return Component.extend({
        defaults: {
            template: 'Notime_Shipping/checkout/shipping/additional-block'
        },
        flag: ko.observable(false),
        time: ko.observable(''),

        initialize: function () {
            var self = this;
            window.widget = this;
            this.isActive();
            document.body.addEventListener("shipmentGenerateFinished", this.isActive);
            this._super();
        },

        initObservable: function () {
            this._super();
            this.selectedMethod = ko.computed(function() {
                var method = quote.shippingMethod();
                var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                return selectedMethod;
            }, this);

            return this;
        },

        getNotimeShippingPostcode:function(){
            return quote.shippingAddress._latestValue.postcode;
        },

        getNotimeShippingId: function(){
            if(checkoutConfig.quoteData.notime_shipment_id == null){
                return false;
            } else {
                return true;
            }
        },

        isActive: function(){
            if ((checkoutConfig.quoteData.notime_shipment_id !== null) && ($('#notimeWidgetNotSupportedPostcodeContainer').is(":visible") === false)) {
                window.widget.flag(true);
                window.widget.time(checkoutConfig.quoteData.notime_shipment_time);
            } else {
                window.widget.flag(false);
                checkoutConfig.quoteData.notime_shipment_time = null;
                checkoutConfig.quoteData.notime_service_id = null;
                checkoutConfig.quoteData.notime_timewindow_date = null;
            }
        }

    });
});