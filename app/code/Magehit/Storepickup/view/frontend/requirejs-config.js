var config = {
    "map": {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default" : "Magehit_Storepickup/js/shipping-save-processor-default-override",
            "Magento_Checkout/js/model/shipping-service":"Magehit_Storepickup/js/model/shipping-service",
            'Magento_Checkout/js/model/checkout-data-resolver': 'Magehit_Storepickup/js/model/checkout-data-resolver'
            
        }
    },
    config: {
            mixins: {
                'Magento_Checkout/js/view/shipping': {
                    'Magehit_Storepickup/js/view/shipping': true
                }
            }
    }
};
// var config = {
//     config: {
//         mixins: {
//             'Magento_Checkout/js/model/shipping-service': {
//                 'Magehit_Storepickup/js/model/shipping-service': true
//             }
//         }
//     }
// };
// 'Magento_Checkout/js/model/shipping-service': {
//                     'Magehit_Storepickup/js/model/shipping-service': true
//                 }
// var config = {
//     config: {
//         mixins: {
//             'Magento_Checkout/js/action/set-shipping-information': {
//                 'Magehit_Storepickup/js/action/set-shipping-information-mixin': true
//             }
//         }
//     }
// };