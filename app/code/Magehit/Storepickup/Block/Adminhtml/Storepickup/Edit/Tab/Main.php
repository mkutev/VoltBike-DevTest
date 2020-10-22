<?php
namespace Magehit\Storepickup\Block\Adminhtml\Storepickup\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Design\Robots;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;



class Main extends Generic implements TabInterface
{

    public $wysiwygConfig;

    public $booleanOptions;

    protected $enabledisable;

    public $metaRobotsOptions;

    public $_systemStore;

    protected $_storeManager;

    protected $_countryFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Enabledisable $enableDisable,
        Robots $metaRobotsOptions,
        Store $systemStore,
         \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    )
    {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->enabledisable = $enableDisable;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->_systemStore = $systemStore;
        $this->_storeManager = $context->getStoreManager();
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }


    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('magehit_storepickup');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Information')]);
        if ($model->getId()) {
            $fieldset->addField('storepickup_id', 'hidden', ['name' => 'storepickup_id']);
        }
        $fieldset->addField(
            'store_name',
            'text',
            ['name' => 'store_name', 'label' => __('Store Name'), 'title' => __('Store Name'), 'required' => true,]
        );

        $fieldset->addField(
            'status', 'select', array(
                'label'    => __('Status'),
                'required' => false,
                'name'     => 'status',
                'value'    => '1',
                'values'   => array('1' => 'Enabled', '0' => 'Disabled'),
                
            )
        );

         $fieldset->addField(
               'in_store',
               'multiselect',
               [
                 'name'     => 'in_store[]',
                 'label'    => __('Store Views'),
                 'title'    => __('Store Views'),
                 'required' => true,
                 'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
               ]
            );
        
        $fieldset->addField(
            'email',
            'text',
            ['name' => 'email','class'=> 'validate-email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true]
        );
        $fieldset->addField(
            'street',
            'text',
            ['name' => 'street','class'=> 'validate-address', 'label' => __('Street'), 'title' => __('Street'), 'required' => true]
        );
        $fieldset->addField(
            'city',
            'text',
            ['name' => 'city','class'=> 'validate-address', 'label' => __('City'), 'title' => __('City'), 'required' => true]
        );
        // $fieldset->addField(
        //     'region',
        //     'text',
        //     ['name' => 'region','class'=> 'validate-address', 'label' => __('State/Province'), 'title' => __('State/Province'), 'required' => false]
        // );
        $fieldset->addField(
            'postcode',
            'text',
            ['name' => 'postcode', 'label' => __('Zip/Postal Code'), 'title' => __('Zip/Postal Code'), 'required' => true]
        );
                $optionsc=$this->_countryFactory->toOptionArray();
        $country = $fieldset->addField(
                'country',
                'select',
                [
                    'name' => 'country','class'=> 'validate-address', 'label' => __('Country'), 'title' => __('Country'), 'required' => true,'values' => $optionsc,
                ]
            );
        $fieldset->addField(
            'region',
            'text',
            ['name' => 'region','class'=> 'validate-address', 'label' => __('State/Province'), 'title' => __('State/Province'), 'required' => false]
        );
        $fieldset->addField(
                'region_id',
                'select',
                [
                    'name' => 'region_id',
                    'class'=> 'validate-address',
                    'label' => __('State/Province'),
                    'title' => __('State/Province'),
                    'required' => false,
                    'values' =>  ['--Please Select Country--'],
                ]
            );


         /*
            * Add Ajax to the Country select box html output
            */
        if(!$model->getRegion()){
            $html = '<style type="text/css">
                        .field-region{
                            display: none;
                        }
                    </style>';
        }else{
             $html = '<style type="text/css">
                        .field-region_id{
                            display: none;
                        }
                    </style>';
        }
        $js ="";
        if($model->getRegionId()){
            $js = "/region/".$model->getRegionId();
        }
        $country->setAfterElementHtml($html."   
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                    function ajax(){
                         $.ajax({
                               url : '". $this->getUrl('magehit_storepickup/*/regionlist') . "country/' +  $('#item_country').val() + '".$js."',
                                type: 'get',
                                dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                    $('#item_region_id').empty();
                                    if(data.status === 1){
                                        $('.field-region').hide();
                                        $('.field-region_id').show();
                                        $('#item_region_id').append(data.htmlconent);
                                   
                                    }else{
                                        $('.field-region').show();
                                        $('.field-region_id').hide();
                                    }
                                    
                               }
                            });
                    };
                   $(window).load( function(event){
                       ajax();
                   })
                   $('#edit_form').on('change', '#item_country', function(event){
                       ajax();
                   })
                }

            );
            </script>"
        );

        $fieldset->addField(
            'telephone',
            'text',
            ['name' => 'telephone', 'label' => __('Telephone'), 'title' => __('Telephone'), 'required' => false]
        );
       
      

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
