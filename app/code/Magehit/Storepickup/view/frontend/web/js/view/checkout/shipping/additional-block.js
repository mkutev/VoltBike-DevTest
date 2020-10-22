define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
	'mage/url'
], function ($, ko, Component, quote, storage, url) {
    'use strict';
    ko.bindingHandlers['triggerChange'] = {
        'init': function(element, valueAccessor, allBindingsAccessor) {
			var storePickupData = window.storepickupData ? JSON.parse(window.storepickupData) : null;
			if(storePickupData != null){
				$('#storepickup_store').trigger('change');
			}
        }
    };
	
    ko.bindingHandlers['datepicker'] = {
        'init': function(element, valueAccessor, allBindingsAccessor) {
            /* Initialize datepicker with some optional options */
            var options = allBindingsAccessor().datePickeroptions || {},
            prop = valueAccessor(),
            $elem = $(element);

            prop($elem.val());

            $elem.datepicker(options);

            /* Handle the field changing */
            ko.utils.registerEventHandler(element, "change", function () {
                prop($elem.datepicker("getDate"));
            });

            /* Handle disposal (if KO removes by the template binding) */
            ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
                $elem.datepicker("destroy");
            });
        },
        'update': function(element, valueAccessor) {
            var value = ko.utils.unwrapObservable(valueAccessor()),
            $elem = $(element),
            current = $elem.datepicker("getDate");

            if (value - current !== 0) {
                $elem.datepicker("setDate", value);
            }
        }
    };
  
    return Component.extend({
        defaults: {
            template: 'Magehit_Storepickup/checkout/shipping/additional-block'
        },
		defaulDate : ko.observable(new Date()),
		selectedValue: ko.observable(),
        initialize: function() {
			this._super();
			this.dob = ko.observable(new Date());
			var storePickupData = window.storepickupData ? JSON.parse(window.storepickupData) : null;
        },
        initObservable: function () {
			var storePickupData = window.storepickupData ? JSON.parse(window.storepickupData) : null;
            this.selectedMethod = ko.computed(function() {
				var selectedStoreId =  '';
				if(storePickupData){
					selectedStoreId =  storePickupData.id;
				}
				this.selectedValue(selectedStoreId);
                var method = quote.shippingMethod();
                // console.log('yyy');
                // console.log(selectedStoreId);
               
                var selectedMethod;
             
                
                //var listOptions = window.storepickupAvaiable ? JSON.parse(window.storepickupAvaiable) : null;
				selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                var listOptions = method != null ? method.extension_attributes.storepickup_id : null;
                if(selectedStoreId && listOptions){
                	console.log('--->');
                	$( "#storepickup_store" ).trigger( "change" );
                }
                if(listOptions){
					$.each( listOptions, function( key, value ) {
						if(typeof listOptions[key] != 'object')
						listOptions[key] = JSON.parse(value);
					})
					return {'name':selectedMethod, 'list_options':listOptions, 'selectedStoreId': selectedStoreId};
				} 
                return {'name':selectedMethod, 'selectedStoreId': selectedStoreId};
            }, this);
			
            return this;
        },
        getAjaxinfo: function () {
           var self = this;
           var storeId = this.selectedValue();
          
           $('.content-storepickup').html('');
           if(storeId){
				var serviceUrl = 'storepickup/index/estimateStorePickup?store='+storeId ;
               	return storage.post(
                   serviceUrl,''
              	 ).done(
                   	function (response) {
		                if(response.success == true){
							var pickupdate ='', pickuptime ='';
							if(response.pickupData){
								pickupdate = response.pickupData.date ? response.pickupData.date : '';
								pickuptime = response.pickupData.time ? response.pickupData.time : '';
							}
							var html = response.html;
							if(window.showpickupdate){
								html += '<div class="group-field group-field-date"><label class="label" for="date-store"><strong>Pickup Date:</strong></label>';
								html += '<input class="input-text date-store" value="'+ pickupdate +'" placeholder="Select Pickup Date" type="text" id="date-store" name="date-store" data-validate="{\'required-entry\':true}" autocomplete="off"/> </div>';
								$('.content-storepickup').html(html);
								$('#date-store').datepicker({
									dateFormat:'dd/mm/yy',
									minDate:+1,
									disableTouchKeyboard:true,
									beforeShowDay: function(date) {
										var day = date.getDay();
										return [($.inArray(day, response.day) == -1)]; 
									},
									onSelect: function(d,i) {
										var day = new Date(i.selectedYear,i.selectedMonth,i.selectedDay);
										$('.group-field-time').remove();
										var time = response.time;
										var to = [23];
										var from = [0];
										for(var k in time){
											if(k == day.getDay()){
												to = time[k].to;
												from = time[k].from;
											}
										}
										
										// ajax save date when selected
										$.ajax({
											url: url.build('storepickup/index/SaveQuote'),
											type: "POST",
											data: {store: storeId, date:  $(this).val()},
											success: function(response){}
										});
										var dateSelected = $(this).val();
										
										//apend time option
										var html2 = '<div class="group-field group-field-time"  style="display:none !important"><label class="label" for="time-store"><strong>Pickup Time:</strong></label>';
										html2 += '<input class="input-text time-store" placeholder="Select Pickup Time" type="text" id="time-store" name="time-store" autocomplete="off"/> </div>';
										if( $('#time-store').length == 0){
											$('.content-storepickup').append(html2);
										}
										
										$('#time-store').timepicker({
											timeFormat: 'HH:mm',
											hourMin:parseInt(from[0]),
											hourMax:parseInt(to[0]),
											disableTouchKeyboard:true,
											showButtonPanel:false,
											onSelect: function(d,i) {
												$.ajax({
													url: url.build('storepickup/index/SaveQuote'),
													type: "POST",
													data: {store: storeId, date:  dateSelected, time: $(this).val()},
													success: function(response){}
												});
											}
										});
										$(this).change();
									  
									}
								});
								if(pickupdate != ''){ 
									//apend time option
									var html2 = '<div class="group-field group-field-time"  style="display:none !important"><label class="label" for="time-store"><strong>Pickup Time:</strong></label>';
									html2 += '<input class="input-text time-store" value="'+ pickuptime +'" placeholder="Select Pickup Time" type="text" id="time-store" name="time-store" autocomplete="off"/> </div>';
									if( $('#time-store').length == 0){
										$('.content-storepickup').append(html2);
									}
									
									$('#time-store').timepicker({
										timeFormat: 'HH:mm',
										/* hourMin:parseInt(from[0]),
										hourMax:parseInt(to[0]), */
										disableTouchKeyboard:true,
										showButtonPanel:false,
										onSelect: function(d,i) {
											$.ajax({
												url: url.build('storepickup/index/SaveQuote'),
												type: "POST",
												data: {store: storeId, date:  pickupdate, time: $(this).val()},
												success: function(response){}
											});
										}
									});
								}
							}else{
								$('.content-storepickup').html(html);
							}
						}
					}
				).fail(
                   function (response) {
                       alert(response);
                   }
               );
           }
           return false;
       },
    });

});