<?php
namespace Magehit\Storelocator\Block\Adminhtml\Photo\Edit\Tab;


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
        $model = $this->_coreRegistry->registry('magehit_storelocator_photo');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Information')]);
        if ($model->getId()) {
            $fieldset->addField('photo_id', 'hidden', ['name' => 'photo_id']);
        }
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );
		
        $fieldset->addField(
            'status', 'select', array(
                'label'    => __('Status'),
                'required' => false,
                'name'     => 'status',
                'values'   => array('1' => 'Enabled', '0' => 'Disabled'),
                
            )
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Store Image'),
                'title' => __('Store Image'),
                'required'  => true,
               
            ]
        );
		
        $fieldset->addField(
            'city',
            'text',
            ['name' => 'city', 'label' => __('City'), 'title' => __('City'), 'required' => false]
        );
		
		
        $fieldset->addField(
            'state',
            'text',
            ['name' => 'state', 'label' => __('State'), 'title' => __('State'), 'required' => false]
        );
		
		$fieldset->addField(
            'country',
            'text',
            ['name' => 'country', 'label' => __('Country'), 'title' => __('Country'), 'required' => false]
        );
		
        $fieldset->addField('details', 'editor', [
                'name' => 'details',
                'label' => __('Details'),
                'title' => __('Details'),
                'config' => $this->wysiwygConfig->getConfig(['add_variables' => false, 'add_widgets' => false])
            ]
        );

        $form->setValues($model->getData());
		/* $p = $form->getElement('image')->getValue();
		$form->getElement('image')->setValue('community_photos' . $p);
		 */
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
}
