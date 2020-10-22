/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/checkout-data-resolver'
], function ($,ko, checkoutDataResolver) {
    'use strict';

    var shippingRates = ko.observableArray([]);

    return {
        isLoading: ko.observable(false),

        /**
         * Set shipping rates
         *
         * @param {*} ratesData
         */
        setShippingRates: function (ratesData) {
            //console.log(ratesData);
            
            function getAjaxinfo(country_id,region_id,region,list,vt) {
                
                    var data = [];

                    //data['data'] = data;
                    var serviceUrl = '/storepickup/index/check' ;
                    $.ajax({
                        showLoader: true,
                        data:{country_id:country_id,region_id:region_id,region:region,list:list},
                        url: serviceUrl,
                        type: "POST",
                        async:false,
                        dataType: 'json'
                    }).done(function (resuls) {
                        if(resuls.success == true){
                            ratesData[vt].extension_attributes.storepickup_id = resuls.data;
                        }else{
                            ratesData[vt].error_message = "storepickup_null";
                            $('#storepickup-carrier').hide();
                             //ratesData.splice(vt, 1); 
                        }
                       
                    });
                   
                return ratesData;
            };
           // console.log(ratesData);
            var vt = 0;var storepickup_arr = [];var list;var data='';
            for( var i = 0; i < ratesData.length; i++){
               if ( ratesData[i].carrier_code === "storepickup") {
                 vt = i;
                 storepickup_arr = ratesData[i].extension_attributes.storepickup_id;
               }
            }
            $.each( storepickup_arr, function( key, value ) {
                        if(typeof storepickup_arr[key] != 'object')
                        // console.log(key);
                        //console.log(JSON.parse(value));
                        data = data+','+JSON.parse(value).value;
            })
           
            if(data && ratesData[vt].carrier_code === "storepickup"){
               // console.log(storepickup_arr);
               // console.log(data);
                ratesData =  getAjaxinfo($('[name="country_id"]').val(),$('[name="region_id"]').val(),$('[name="region"]').val(),data,vt);
            } 
            
           
            shippingRates(ratesData);
            shippingRates.valueHasMutated();
            checkoutDataResolver.resolveShippingRates(ratesData);
        },

        /**
         * Get shipping rates
         *
         * @returns {*}
         */
        getShippingRates: function () {
            
            return shippingRates;
        }
    };
});
