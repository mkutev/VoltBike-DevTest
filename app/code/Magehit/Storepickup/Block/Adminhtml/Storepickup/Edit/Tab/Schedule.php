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

class Schedule extends Generic implements TabInterface
{

    public $wysiwygConfig;

    public $booleanOptions;

    protected $enabledisable;

    public $metaRobotsOptions;

    public $systemStore;
    
    protected $_serialize;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Enabledisable $enableDisable,
        Robots $metaRobotsOptions,
        Store $systemStore,
        array $data = [],
        \Magehit\Storepickup\Serialize\Serializer\Json $serialize
    )
    {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->enabledisable = $enableDisable;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->systemStore = $systemStore;
        $this->_serialize = $serialize;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    public function getTabLabel()
    {
        return __('Store Schedule');
    }


    public function getTabTitle()
    {
        return __('Store Schedule');
    }


    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('magehit_storepickup');
       
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
       
        
        $days = [
        'sun'=>__('Sunday'),
        'mon'=> __('Monday'),
        'tue'=>__('Tuesday'),
        'wed'=>__('Wednesday'),
        'thu'=>__('Thursday'),
        'fri'=>__('Friday'),
        'sat'=>__('Saturday'),
        
        ];

        //begin
        foreach ($days as $dayCode => $dayName):
            $fieldset = $form->addFieldset(
                $dayCode,
                [
                    'legend' => __(ucfirst($dayName).' Schedule'),
                    'class'     => 'fieldset-wide',
                    'expanded'  => true,
                ]
            );
            $fieldset->addType(
            'magehitTime',
            'Magehit\Storepickup\Block\Adminhtml\Storepickup\Edit\Renderer\Time'
            );
            $fieldset->addType(
            'magehitstatus',
            'Magehit\Storepickup\Block\Adminhtml\Storepickup\Edit\Renderer\Status'
            );

            $fieldset->addField(
                'schedule['.$dayCode.'][from]', 'magehitTime', [
                    'label'    => __('Open Time'),
                    'required' => true,
                    'name'     => 'schedule['.$dayCode.'][from]',
                    'time'    => $this->getTime($dayCode, 'from'),
                ]
            );
            $fieldset->addField(
                'schedule['.$dayCode.'][to]', 'magehitTime', [
                    'label'    => __('Close Time'),
                    'required' => true,
                    'name'     => 'schedule['.$dayCode.'][to]',
                    'time'    => $this->getTime($dayCode, 'to'),
                ]
            );

            $fieldset->addField(
            'schedule['.$dayCode.'][status]', 'magehitstatus', array(
                'label'    => __('Status'),
                'required' => false,
                'name'     => 'schedule['.$dayCode.'][status]',
                'values'   => array('1' => 'Open', '0' => 'Close'),  
                'status'  =>   $this->getTime($dayCode, 'status'),
            )
            );

        endforeach;
        //end

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
    public function getTime($day, $time)
    {

        $model = $this->_coreRegistry->registry('magehit_storepickup');

        if(!$model) return 0;
        if(!$model->getData('schedule')){ return $model->getData('schedule'); }
        $data = $this->_serialize->unserialize($model->getData('schedule'));
        //$data = unserialize($model->getData('schedule'));
        if (isset($data[$day][$time])) {

            return $data[$day][$time];
        }
    }
}
