/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Faqs
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
    	mpFaqCollapsible: 'Mageplaza_Faqs/js/collapsible',
        materialize: 'Mageplaza_Faqs/js/materialize.min',
        search: 'Mageplaza_Faqs/js/search',
        helpful_rate: 'Mageplaza_Faqs/js/helpful-rate',
        form_submit: 'Mageplaza_Faqs/js/form/submit',
        magnific: 'Mageplaza_Core/js/jquery.magnific-popup.min'
    },
    shim:{
    	mpFaqCollapsible:['jquery'],
        materialize:['jquery'],
        form_submit:['jquery']
    }
};