<?php
namespace Magehit\Storepickup\Block\Adminhtml\Storepickup;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        $this->_objectId = 'storepickup_id';
        $this->_controller = 'adminhtml_storepickup';
        $this->_blockGroup = 'Magehit_Storepickup';

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
        
        $javascript ='
            <script type="text/javascript">
            require([
                \'jquery\',
                \'jquery/ui\', 
                \'jquery/validate\',
                \'mage/translate\'
            ],function($) {
              "use strict";

                $(document).ready(function($){
                   if($( "#item_chose_handlingfee" ).val() == 0){
                    $(".field-handling_fee").hide();
                   }
                   
                   $( "#item_chose_handlingfee" ).change(function() {
                      if($(this).val() == 1){
                        $(".field-handling_fee").show();
                      }else{
                        $(".field-handling_fee").hide();
                      }
                    });
                });
                return;
            });//end
            </script>';

        return $javascript . parent::_toHtml();
    }


    public function getHeaderText()
    {
        $item = $this->_coreRegistry->registry('current_magehit_store_items');
        if ($item->getId()) {
            return __("Edit Item '%1'", $this->escapeHtml($item->getStore_name()));
        } else {
            return __('New Item');
        }
    }
}
