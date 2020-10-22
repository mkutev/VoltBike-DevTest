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



class Handlingfee extends Generic implements TabInterface
{
    const XML_NOTIFY_PRICE = 'carriers/storepickup/price';

    public $booleanOptions;

    protected $enabledisable;

    public $metaRobotsOptions;

    public $_systemStore;

    protected $_storeManager;

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Enabledisable $enableDisable,
        Robots $metaRobotsOptions,
        Store $systemStore,
        array $data = []
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->booleanOptions = $booleanOptions;
        $this->enabledisable = $enableDisable;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->_systemStore = $systemStore;
        $this->_storeManager = $context->getStoreManager();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Handling fee');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Handling fee');
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Handling fee')]);
        
        $fieldset->addField(
            'chose_handlingfee', 'select', array(
                'label'    => __('Handling fee'),
                'required' => false,
                'name'     => 'chose_handlingfee',
                'values'   => array('1' => 'Use handling fee in this store', '0' => 'Use global handling fee'),
                
            )
        );
        $fieldset->addField(
            'handling_fee',
            'text',
            ['name' => 'handling_fee', 'label' => __('Fee Amount'), 'title' => __('Fee Amount'), 'required' => false,'class' => 'validate-number validate-zero-or-greater']
        );
       
        if($model->getData()){
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $price_config = $this->scopeConfig->getValue(self::XML_NOTIFY_PRICE, $storeScope);
            $data = $model->getData();
            if($model->getHandlingFee() != $price_config) {$data['chose_handlingfee'] = 1;}
            $model->setData($data);
        }
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
