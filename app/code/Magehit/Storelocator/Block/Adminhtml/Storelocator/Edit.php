<?php
namespace Magehit\Storelocator\Block\Adminhtml\Storelocator;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_coreRegistry = null;
    protected $dataHelper;
    //protected $_storeManager;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magehit\Storelocator\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->dataHelper = $dataHelper;
        //$this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        $this->_objectId = 'storelocator_id';
        $this->_controller = 'adminhtml_storelocator';
        $this->_blockGroup = 'Magehit_Storelocator';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }


    protected function _toHtml()
    {
        $javascript = '<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key='.trim($this->dataHelper->getConfig('map/api_key' ,$this->_storeManager->getStore()->getId())).'" type="text/javascript"></script>';
        $javascript .= <<<EOD
            
            <script type="text/javascript">
            require([
                'jquery',
                'jquery/ui', 
                'jquery/validate',
                'mage/translate'
            ], function($){ 

               $('.validate-address').keyup(function(){
                            searchLocation();
                });
                function searchLocation() {
                            var address = document.getElementById("item_street").value + ", "
                            + document.getElementById("item_city").value + ", "
                            + document.getElementById("item_region").value + ", "
                            + document.getElementById("item_country").value;
                
                            var geocoder = new google.maps.Geocoder();
                            geocoder.geocode({address: address}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    document.getElementById("item_lat").value = results[0].geometry.location.lat().toFixed(7);
                                    document.getElementById("item_lng").value = results[0].geometry.location.lng().toFixed(7);
                                    
                                } else {
                                     document.getElementById("item_lat").value ='';
                                    document.getElementById("item_lng").value = '';
                                }
                            });
                }
                 $.validator.addMethod(
                        'validate-address', function (val) { 
                            console.log($('#item_lat').val().trim());
                            if(  $('#item_lat').val().trim() == "" || $('#item_lng').val().trim() == ""){
                                return false;
                            }
                            return true;
                         }, $.mage.__('Please enter a valid address')
                );

            });//end
            </script>
EOD;

        return $javascript . parent::_toHtml();
    }


    public function getHeaderText()
    {
        $item = $this->_coreRegistry->registry('current_magehit_storelocator_items');
        if ($item->getId()) {
            return __("Edit Item '%1'", $this->escapeHtml($item->getStore_name()));
        } else {
            return __('New Item');
        }
    }
}
