define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor'
    ],
    function (ko, $, quote, resourceUrlManager, storage, paymentService, methodConverter, errorProcessor) {
        'use strict';
        return {
            saveShippingInformation: function() {
                var payload = {

                addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code,
                        extension_attributes: {
                            storepickup_store: $('[name="storepickup_store"]').val(),
                            storepickup_date: $('[name="date-store"]').val(),
                            storepickup_time: $('[name="time-store"]').val()
                        }
                    }
                };
                // console.log(quote.shippingMethod());
                // console.log(quote.shippingMethod().error_message);
                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(

                    function (response) {
                        quote.setTotals(response.totals);
                        //console.log(quote);
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    }
                ).fail(
                    function (response) {
                        console.log(response);
                        errorProcessor.process(response);
                         
                    }
                );
            }
        };
    }
);