<?php
namespace Magehit\Storelocator\Block\Adminhtml\Storelocator\Edit\Tab;


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

    public function __construct(
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
        $this->wysiwygConfig = $wysiwygConfig;
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
        $model = $this->_coreRegistry->registry('magehit_storelocator_storelocator');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Information')]);
        if ($model->getId()) {
            $fieldset->addField('storelocator_id', 'hidden', ['name' => 'storelocator_id']);
        }
        $fieldset->addField(
            'store_name',
            'text',
            ['name' => 'store_name', 'label' => __('Store Name'), 'title' => __('Store Name'), 'required' => true,'note' => '<a style="color:red" href="https://www.google.com/maps" target="_blank">Get address from google maps</a>']
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
            'store_url',
            'text',
            ['name' => 'store_url','class'=> 'validate-identifier', 'label' => __('Store URL key'), 'title' => __('Store URL key'), 'required' => false]
        );

        $fieldset->addField(
            'store_thumnail',
            'image',
            [
                'name' => 'store_thumnail',
                'label' => __('Store Image'),
                'title' => __('Store Image'),
                'required'  => false,
               
            ]
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
            'store_schedule', 'select', array(
                'label'    => __('Store Schedule'),
                'required' => false,
                'name'     => 'store_schedule',
                'value'    => '1',
                'values'   => array('1' => 'Enabled', '0' => 'Disabled'),
                
            )
        );
        $fieldset->addField(
            'email',
            'text',
            ['name' => 'email','class'=> 'validate-email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true,'note'=>__('This is field will be used when client want contact to store location.')]
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
        $fieldset->addField(
            'region',
            'text',
            ['name' => 'region','class'=> 'validate-address', 'label' => __('State/Province'), 'title' => __('State/Province'), 'required' => false]
        );
        $fieldset->addField(
            'postcode',
            'text',
            ['name' => 'postcode', 'label' => __('Zip/Postal Code'), 'title' => __('Zip/Postal Code'), 'required' => true]
        );
        $fieldset->addField(
            'country',
            'text',
            ['name' => 'country','class'=> 'validate-address', 'label' => __('Country'), 'title' => __('Country'), 'required' => true]
        );

        $fieldset->addField(
            'telephone',
            'text',
            ['name' => 'telephone', 'label' => __('Telephone'), 'title' => __('Telephone'), 'required' => false]
        );
        $fieldset->addField(
            'fax',
            'text',
            ['name' => 'fax', 'label' => __('Fax'), 'title' => __('Fax'), 'required' => false]
        );
        $fieldset->addField(
            'website',
            'text',
            ['name' => 'website', 'label' => __('Website'), 'title' => __('Website'), 'required' => false]
        );
        $fieldset->addField('content', 'editor', [
                'name' => 'content',
                'label' => __('Other Information'),
                'title' => __('Other Information'),
                'config' => $this->wysiwygConfig->getConfig(['add_variables' => false, 'add_widgets' => false])
            ]
        );
        $fieldset->addField('lng', 'hidden', ['name' => 'lng']);
        $fieldset->addField('lat', 'hidden', ['name' => 'lat']);

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
