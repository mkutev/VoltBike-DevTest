<?php
namespace Magehit\Storepickup\Block\Adminhtml\Storepickup\Edit\Tab;

use Magento\Framework\App\ObjectManager;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Conditions extends Generic implements TabInterface
{

    protected $_rendererFieldset;

    protected $_conditions;

    private $ruleFactory;

    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    private function getRuleFactory()
    {
        if ($this->ruleFactory === null) {
            $this->ruleFactory = ObjectManager::getInstance()->get('Magento\CatalogRule\Model\RuleFactory');
        }
        return $this->ruleFactory;
    }

    public function getTabClass()
    {
        return null;
    }


    public function getTabLabel()
    {
        return __('Conditions');
    }


    public function getTabTitle()
    {
        return __('Conditions');
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
        $rule_id = '';
        $model = $this->_coreRegistry->registry('magehit_storepickup');
        if($model){
            $rule_id = $model->getRule_id();
    
        }
        $model2 = $this->_objectManager->create('Magehit\Storepickup\Model\Rule')->load($rule_id);

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $formName= 'rule_conditions_fieldset';
        $conditionsFieldSetId = 'rule_conditions_fieldset';
        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            //$this->getUrl('magehit_storepickup/rule/newConditionHtml/form/catalog_rule_form')
            $this->getUrl(
                'catalog_rule/promo_catalog/newConditionHtml/form/' .$conditionsFieldSetId,
                ['form_namespace' => $formName]
            )
        );
 
        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                )
            ]
        )->setRenderer(
            $renderer
        );
 
        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions'),'data-form-part' => $formName]
        )->setRule(
            $model2
        )->setRenderer(
            $this->_conditions
        );
        $formData = $model2->getData();
        $formData['conditions']  = $model2->getConditionsSerialized();
        $form->setValues($formData);
        $this->setConditionFormName($model2->getConditions(), $formName);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }

}